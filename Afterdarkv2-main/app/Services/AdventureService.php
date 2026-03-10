<?php

namespace App\Services;

use App\Constants\DefaultConstants;
use App\Enums\AdventureSongTypeEnum;
use App\Http\Requests\Frontend\Adventure\AdventureUpdateRequest;
use App\Jobs\ConvertToHLS;
use App\Jobs\ConvertToMp3;
use App\Models\Adventure;
use App\Traits\SanitizedRequest;
use FFMpeg\FFProbe;
use getID3;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image;
use stdClass;

class AdventureService
{
    use SanitizedRequest;

    private const EXTENSION_MP3 = 'mp3';

    private array $versions = [
        0x0 => '2.5',
        0x1 => 'x',
        0x2 => '2',
        0x3 => '1',
        // x=>'reserved'
    ];

    private array $layers = [
        0x0 => 'x',
        0x1 => '3',
        0x2 => '2',
        0x3 => '1',
        // x=>'reserved'
    ];

    private array $bitRates = [
        'V1L1' => [0, 32, 64, 96, 128, 160, 192, 224, 256, 288, 320, 352, 384, 416, 448],
        'V1L2' => [0, 32, 48, 56, 64, 80, 96, 112, 128, 160, 192, 224, 256, 320, 384],
        'V1L3' => [0, 32, 40, 48, 56, 64, 80, 96, 112, 128, 160, 192, 224, 256, 320],
        'V2L1' => [0, 32, 48, 56, 64, 80, 96, 112, 128, 144, 160, 176, 192, 224, 256],
        'V2L2' => [0, 8, 16, 24, 32, 40, 48, 56, 64, 80, 96, 112, 128, 144, 160],
        'V2L3' => [0, 8, 16, 24, 32, 40, 48, 56, 64, 80, 96, 112, 128, 144, 160],
    ];

    private array $sampleRates = [
        '1' => [44100, 48000, 32000],
        '2' => [22050, 24000, 16000],
        '2.5' => [11025, 12000, 8000],
    ];

    private array $samples = [
        1 => [1 => 384, 2 => 1152, 3 => 1152],
        2 => [1 => 384, 2 => 1152, 3 => 576],
    ];

    public function handle(array $input, UploadedFile $track, ?UploadedFile $artwork = null)
    {
        $filePath = $track->path();

        $mp3Info = $this->getMp3Data($filePath);

        $audioData = $this->getAudioData($mp3Info, $filePath);

        return $this->processFile($input, $track, $mp3Info, $audioData, $artwork);
    }

    public function getBasicInfo($filename): stdClass
    {
        $fd = fopen($filename, 'rb');

        $buffer = new stdClass;
        $duration = 0;
        $block = fread($fd, 100);
        $offset = $this->skipID3v2Tag($block);
        fseek($fd, $offset);

        while (!feof($fd)) {
            $block = fread($fd, 10);

            if (strlen($block) < 10) {
                break;
            } elseif ($block[0] == "\xff" && (ord($block[1]) & 0xE0)) {
                $info = $this->parseFrameHeader(substr($block, 0, 4));
                if (empty($info['Framesize'])) {
                    $buffer->duration = $duration;
                    $buffer->bitrate = $info['Bitrate'];

                    return $buffer;
                }
                fseek($fd, $info['Framesize'] - 10, SEEK_CUR);
                $duration += ($info['Samples'] / $info['Sampling Rate']);
                $buffer->bitrate = $info['Bitrate'];
            } elseif (str_starts_with($block, 'TAG')) {
                fseek($fd, 128 - 10, SEEK_CUR);
            } else {
                fseek($fd, -9, SEEK_CUR);
            }
        }

        $buffer->duration = round($duration);

        return $buffer;
    }

    public function parseFrameHeader($fourBytes): array
    {
        $b1 = ord($fourBytes[1]);
        $b2 = ord($fourBytes[2]);

        $versionBits = ($b1 & 0x18) >> 3;
        $version = $this->versions[$versionBits];
        $simpleVersion = ($version == '2.5' ? 2 : $version);

        $layerBits = ($b1 & 0x06) >> 1;
        $layer = $this->layers[$layerBits];

        $bitrateKey = sprintf('V%dL%d', $simpleVersion, $layer);
        $bitrateIdx = ($b2 & 0xF0) >> 4;
        $bitrate = $this->bitRates[$bitrateKey][$bitrateIdx] ?? 0;

        $sampleRateIdx = ($b2 & 0x0C) >> 2; // 0xc => b1100
        $sampleRate = $this->sampleRates[$version][$sampleRateIdx] ?? 0;
        $paddingBit = ($b2 & 0x02) >> 1;

        return [
            'Version' => $version,
            'Layer' => $layer,
            'Bitrate' => $bitrate,
            'Sampling Rate' => $sampleRate,
            'Framesize' => $this->frameSize($layer, $bitrate, $sampleRate, $paddingBit),
            'Samples' => $this->samples[$version][$sampleRate],
        ];
    }

    public function skipID3v2Tag($block): float|int
    {
        if (str_starts_with($block, 'ID3')) {
            $id3v2Flags = ord($block[5]);
            $flagFooterPresent = $id3v2Flags & 0x10 ? DefaultConstants::TRUE : DefaultConstants::FALSE;

            $z0 = ord($block[6]);
            $z1 = ord($block[7]);
            $z2 = ord($block[8]);
            $z3 = ord($block[9]);

            if ((($z0 & 0x80) == 0) && (($z1 & 0x80) == 0) && (($z2 & 0x80) == 0) && (($z3 & 0x80) == 0)) {
                $headerSize = 10;
                $tagSize = (($z0 & 0x7F) * 2097152) + (($z1 & 0x7F) * 16384) + (($z2 & 0x7F) * 128) + ($z3 & 0x7F);
                $footerSize = $flagFooterPresent ? 10 : 0;

                return $headerSize + $tagSize + $footerSize; // bytes to skip
            }
        }

        return 0;
    }

    private function frameSize($layer, $bitrate, $sampleRate, $paddingBit): int
    {
        return $layer == 1
            ? intval(((12 * $bitrate * 1000 / $sampleRate) + $paddingBit) * 4)
            : intval(((144 * $bitrate * 1000) / $sampleRate) + $paddingBit);
    }

    private function processFile(array $input, UploadedFile $track, array $mp3Info, array $data, ?UploadedFile $artwork = null)
    {
        if ($input['type'] === AdventureSongTypeEnum::Root) {
            $input['parent_id'] = Adventure::query()->whereUuid($input['parent_uuid'])->value('id');
        }

        $adventureTrack = Adventure::create([
            'title' => $input['title'],
            'description' => array_key_exists('description', $input) ? $input['description'] : null,
            'parent_id' => array_key_exists('parent_id', $input) ? $input['parent_id'] : null,
            'type' => $input['type'],
            'duration' => $data['playtimeSeconds'],
            'user_id' => auth()->id(),
            'order' => array_key_exists('order', $input) ? $input['order'] : null,
        ]);

        if ($input['type'] === AdventureSongTypeEnum::Heading) {
            $adventureTrack->property()->create([
                'approved' => DefaultConstants::TRUE,
                'is_visible' => $input['is_visible'],
                'allow_comments' => $input['allow_comments'],
            ]);

            if (array_key_exists('genres', $input)) {
                $adventureTrack->genres()->sync($input['genres']);
            }

            if (array_key_exists('tags', $input)) {
                TagService::setModelTags($adventureTrack, $input['tags']);
            }
        }

        $trackInfo = $this->getId3Data($mp3Info);

        $filePath = $track->path();

        $tempPath = Str::random(32);
        File::copy($filePath, Storage::disk('public')->path($tempPath));

        $this->processVisualMedia($adventureTrack, $mp3Info, $trackInfo, $artwork ?: null);
        $this->refactoredProcessAudioMedia($adventureTrack, $track, $tempPath, $data['bitRate']);

        return $adventureTrack;
    }

    private function checkIfMp3(UploadedFile $file): bool
    {
        return strtolower($file->extension()) === self::EXTENSION_MP3;
    }

    public function getId3Data(array $mp3Info): array
    {
        $trackInfo = [];

        if (isset($mp3Info['audio']['id3v2']['title'][0])) {
            $trackInfo = $mp3Info['tags']['id3v2'];
        } elseif (isset($mp3Info['tags']['id3v2']['title'][0])) {
            $trackInfo = $mp3Info['tags']['id3v2'];
        } elseif (isset($mp3Info['tags']['id3v1'])) {
            $trackInfo = $mp3Info['tags']['id3v1'];
        } elseif (isset($mp3Info['id3v2']['comments']['title'][0])) {
            $trackInfo = $mp3Info['id3v2']['comments'];
        }

        return $trackInfo;
    }

    public function getAudioData(array $mp3Info, string $filePath): array
    {
        if (!isset($mp3Info['audio']) || !isset($mp3Info['audio']['bitrate'])) {
            if (AudioService::checkIfFFMPEGInstalled()) {
                $ffprobe = FFProbe::create([
                    'ffmpeg.binaries' => env('FFMPEG_PATH', '/usr/bin/ffmpeg'),
                    'ffprobe.binaries' => env('FFPROBE_PATH', '/usr/bin/ffprobe'),
                    'timeout' => 3600,
                    'ffmpeg.threads' => 12,
                ]);

                $data = [
                    'bitRate' => intval($ffprobe->format($filePath)->get('duration')),
                    'playtimeSeconds' => intval($ffprobe->format($filePath)->get('bit_rate') / 1000),
                ];

                if (!isset($data['bitRate'])) {
                    header('HTTP/1.0 403 Forbidden');
                    header('Content-Type: application/json');
                    echo json_encode([
                        'message' => 'Not support',
                        'errors' => ['message' => [__('Audio file is not supported')]],
                    ]);
                    exit;
                }
            } else {
                $basicInfo = $this->getBasicInfo($filePath);

                if (isset($basicInfo->bitrate) && isset($basicInfo->duration)) {
                    $data = [
                        'bitRate' => intval($basicInfo->bitrate),
                        'playtimeSeconds' => intval($basicInfo->duration),
                    ];
                } else {
                    header('HTTP/1.0 403 Forbidden');
                    header('Content-Type: application/json');
                    echo json_encode([
                        'message' => 'Not support',
                        'errors' => ['message' => [__('Audio file is not supported')]],
                    ]);
                    exit;
                }
            }
        } else {
            $data = [
                'bitRate' => intval($mp3Info['audio']['bitrate'] / 1000),
                'playtimeSeconds' => intval($mp3Info['playtime_seconds']),
            ];
        }

        Validator::make($data, [
            'bitRate' => [
                'required',
                'numeric',
                'min:' . config('settings.min_audio_bitrate', 64),
                'max:' . config('settings.max_audio_bitrate', 320),
            ],
            'playtimeSeconds' => [
                'required',
                'numeric',
                'min:' . config('settings.min_audio_duration', 60),
                'max:' . config('settings.max_audio_duration', 300),
            ],
        ])->validate();

        return $data;
    }

    public function getMp3Data(string $filePath): array
    {
        $getID3 = new getID3;

        return $getID3->analyze($filePath);
    }

    public function processAudioMedia(Model $model, Request $request, string $tempPath, $bitrate): void
    {
        if (config('settings.ffmpeg') && AudioService::checkIfFFMPEGInstalled()) {
            $audio = new stdClass;
            $audio->path = Storage::disk('public')->path($tempPath);
            $audio->original_name = $request->file('file')->getClientOriginalName();
            $audio->bitrate = $bitrate;

            $model->update([
                'pending' => DefaultConstants::TRUE,
            ]);

            if (!config('settings.audio_stream_hls') || (config('settings.audio_stream_hls') && config('settings.audio_mp3_backup'))) {
                dispatch(new ConvertToMp3($model, $audio));
            }

            if (config('settings.audio_stream_hls')) {
                dispatch(new ConvertToHLS($model, $audio));
            }
        } else {
            $model->addMedia($request->file('file')->path())
                ->usingFileName(Str::random(10) . '.mp3', PATHINFO_FILENAME)
                ->withCustomProperties(['bitrate' => $bitrate])
                ->toMediaCollection('audio', config('settings.storage_audio_location', 'public'));

            $model->update([
                'mp3' => DefaultConstants::TRUE,
            ]);
        }
    }

    public function refactoredProcessAudioMedia(Model $model, UploadedFile $file, string $tempPath, $bitrate): void
    {
        if (!$this->checkIfMp3($file)) {
            $audio = new stdClass;
            $audio->path = Storage::disk('public')->path($tempPath);
            $audio->original_name = $file->getClientOriginalName();
            $audio->bitrate = $bitrate;

            $model->update([
                'pending' => DefaultConstants::TRUE,
            ]);

            dispatch(new ConvertToMp3($model, $audio));
        } else {
            $model->addMedia($file->path())
                ->usingFileName(Str::random(10) . self::EXTENSION_MP3, PATHINFO_FILENAME)
                ->withCustomProperties(['bitrate' => $bitrate])
                ->toMediaCollection('audio', config('settings.storage_audio_location', 'public'));

            $model->update([
                'mp3' => DefaultConstants::TRUE,
            ]);
        }
    }

    public function processVisualMedia(Model $model, array $mp3Info, array $trackInfo, ?UploadedFile $artwork = null): void
    {
        if ($artwork) {
            $model->addMedia($artwork)
                ->usingFileName(Str::random(10) . '.jpg')
                ->toMediaCollection('artwork', config('settings.storage_artwork_location', 'public'));
        } elseif (isset($mp3Info['comments']['picture']['0']['data']) || isset($trackInfo['picture']['0']['data'])) {
            $pictureData = $mp3Info['comments']['picture']['0']['data'] ?? $trackInfo['picture']['0']['data'];

            $model->addMediaFromBase64(
                base64_encode(
                    Image::read($pictureData)
                        ->coverDown(config('settings.image_artwork_max'), config('settings.image_artwork_max'))
                        ->toJpeg(config('settings.image_jpeg_quality'))
                )
            )
                ->usingFileName(Str::random(10) . '.jpg')
                ->toMediaCollection('artwork', config('settings.storage_artwork_location', 'public'));
        }
    }

    public function update(Adventure $adventure, AdventureUpdateRequest $request): ?Adventure
    {
        $input = $request->validated();

        $adventure->update($input);

        if ($adventure->type === AdventureSongTypeEnum::Heading) {
            $adventure->property()->update([
                'approved' => DefaultConstants::TRUE,
                'is_visible' => $input['is_visible'],
                'allow_comments' => $input['allow_comments'],
            ]);

            if ($request->filled('genres')) {
                $adventure->genres()->sync($request->input('genres'));
            }

            if ($request->filled('tags')) {
                TagService::setModelTags($adventure, $request->input('tags'));
            }
        }

        if ($request->file('artwork')) {
            $adventure->clearMediaCollection('artwork');
            $this->processVisualMedia($adventure, [], [], $request->file('artwork'));
        }

        return $adventure;
    }

    public function destroy(Adventure $adventure): void
    {
        foreach ($adventure->children as $child) {
            $child->tags()->detach();
            $child->genres()->detach();
            $child->children()->delete();
        }

        $adventure->tags()->detach();
        $adventure->genres()->detach();
        $adventure->children()->delete();
        $adventure->clearMediaCollection('audio');
        $adventure->clearMediaCollection('artwork');
        $adventure->delete();
    }

    public function updateChildrenProperties(Adventure $adventure): void
    {
        if (!$adventure->children()->exists()) {
            return;
        }

        $adventure->children()->each(function (Adventure $childAdventure) use ($adventure) {
            $childAdventure->updateQuietly([
                'is_visible' => $adventure->is_visible,
                'allow_comments' => $adventure->allow_comments,
                'allow_download' => $adventure->allow_download,
            ]);

            $childAdventure->genres()->sync($adventure->genres->pluck('id')->toArray());
            $childAdventure->tags()->sync($adventure->tags->pluck('id')->toArray());

            $this->updateChildrenProperties($childAdventure);
        });
    }
}
