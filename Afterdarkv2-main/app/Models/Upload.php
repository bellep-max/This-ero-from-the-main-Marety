<?php

namespace App\Models;

use App\Constants\ArtistConstants;
use App\Constants\DefaultConstants;
use App\Jobs\ConvertToHLS;
use App\Jobs\ConvertToMp3;
use App\Services\AudioService;
use App\Services\UploadService;
use App\Traits\SanitizedRequest;
use Carbon\Carbon;
use getID3;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use stdClass;

class Upload
{
    use SanitizedRequest;

    public function handle($request, $artistIds = null, $album_id = null, $isAdminPanel = false)
    {
        /* validate file format */
        $uploadService = new UploadService;

        if (config('settings.ffmpeg') && AudioService::checkIfFFMPEGInstalled()) {
            if (config('settings.max_audio_file_size') == 0) {
                $request->validate([
                    'file.*' => 'required|mimetypes:application/octet-stream,audio/ogg,audio/x-wav,audio/wav,audio/mpeg,audio/flac,audio/x-hx-aac-adts,audio/x-m4a,video/mp4,video/x-ms-wma,audio/ac3,audio/aac',
                ]);
            } else {
                $request->validate([
                    'file.*' => 'required|mimetypes:application/octet-stream,audio/ogg,audio/x-wav,audio/wav,audio/mpeg,audio/flac,audio/x-hx-aac-adts,audio/x-m4a,video/mp4,video/x-ms-wma,audio/ac3,audio/aac|max:' . config('settings.max_audio_file_size', 51200),
                ]);
            }
        } else {
            if (config('settings.max_audio_file_size') == 0) {
                $request->validate([
                    'file.*' => 'required|mimetypes:audio/mpeg,application/octet-stream',
                ]);
            } else {
                $request->validate([
                    'file.*' => 'required|mimetypes:audio/mpeg,application/octet-stream|max:' . config('settings.max_audio_file_size', 10240),
                ]);
            }
        }

        if ($request->file('file')->isValid()) {
            $filePath = $request->file('file')->path();
            $fullFilename = $request->file('file')->getClientOriginalName();

            $mp3Info = $uploadService->getMp3Data($filePath);
            $data = $uploadService->getAudioData($mp3Info, $filePath);
            $trackInfo = $uploadService->getId3Data($mp3Info);

            $song = new Song;

            $song->title = $trackInfo['title'][0] ?? pathinfo($fullFilename, PATHINFO_FILENAME);
            $song->duration = $data['playtimeSeconds'];

            if ($artistIds) {
                $song->artistIds = $artistIds;
            } else {
                if ($isAdminPanel) {
                    if (isset($trackInfo['artist'][0])) {
                        $artistName = $trackInfo['artist'][0];
                        $row = Artist::query()
                            ->where('name', '=', $artistName)
                            ->first();

                        $this->extracted($row, $song, $artistName);
                    } else {
                        if ($album_id) {
                            $album = Album::find($album_id);
                            $song->artistIds = $album->artistIds;
                            $song->genre = $album->genre;
                        } else {
                            $row = Artist::query()
                                ->where('name', '=', ArtistConstants::VARIOUS)
                                ->first();

                            $this->extracted($row, $song, $artistName);
                        }
                    }
                }
            }

            $uploadService->processVisualMedia($song, $mp3Info, $trackInfo);

            $song->save();

            $tempPath = Str::random(32);
            File::copy($filePath, Storage::disk('public')->path($tempPath));

            if (config('settings.ffmpeg') && AudioService::checkIfFFMPEGInstalled()) {
                $audio = new stdClass;
                $audio->path = Storage::disk('public')->path($tempPath);
                $audio->original_name = $request->file('file')->getClientOriginalName();
                $audio->bitrate = $data['bitRate'];

                $song->update([
                    'pending' => DefaultConstants::TRUE,
                ]);

                if (!config('settings.audio_stream_hls') || config('settings.audio_stream_hls') && config('settings.audio_mp3_backup')) {
                    dispatch(new ConvertToMp3($song, $audio));

                    if (config('settings.audio_hd', false) && $data['bitRate'] >= config('settings.audio_hd_bitrate', 320)) {
                        dispatch(new ConvertToMp3($song, $audio, true));
                    }
                }

                if (config('settings.audio_stream_hls')) {
                    dispatch(new ConvertToHLS($song, $audio));

                    if (config('settings.audio_hd', false) && $data['bitRate'] >= config('settings.audio_hd_bitrate', 320)) {
                        dispatch(new ConvertToHLS($song, $audio, true));
                    }
                }
            } else {
                $song->addMedia($filePath)
                    ->usingFileName(Str::random(10) . '.mp3', PATHINFO_FILENAME)
                    ->withCustomProperties(['bitrate' => $data['bitRate']])
                    ->toMediaCollection('audio', config('settings.storage_audio_location', 'public'));
                $song->mp3 = DefaultConstants::TRUE;
            }

            $song->user_id = auth()->id();

            if ($isAdminPanel) {
                $song->approved = DefaultConstants::TRUE;
            }

            $song->save();

            if ($album_id) {
                AlbumSong::create([
                    'song_id' => $song->id,
                    'album_id' => $album_id,
                    'priority' => (intval(Carbon::parse($_SERVER['REQUEST_TIME_FLOAT'])->format('disu')) / 1000),
                ]);
            } elseif ($isAdminPanel) {
                if (isset($trackInfo['album'][0])) {
                    $row = Album::query()
                        ->where('title', '=', $trackInfo['album'][0])
                        ->first();

                    if (isset($row->id)) {
                        AlbumSong::create([
                            'song_id' => $song->id,
                            'album_id' => $row->id,
                        ]);
                    } else {
                        $album = new Album;
                        $album->title = $trackInfo['album'][0];
                        $album->artistIds = $song['artistIds'];
                        $album->approved = DefaultConstants::TRUE;

                        $service = new UploadService;
                        $service->processVisualMedia($album, $mp3Info, $trackInfo);

                        $album->save();

                        AlbumSong::create([
                            'song_id' => $song->id,
                            'album_id' => $album->id,
                        ]);
                    }
                }
            }

            return $song;
        }
    }

    public function handleEpisode($request, $podcastId)
    {
        if (config('settings.podcast_ffmpeg') && AudioService::checkIfFFMPEGInstalled()) {
            // Support all kind of audio file
            $request->validate([
                'file' => 'required|mimetypes:application/octet-stream,audio/ogg,audio/x-wav,audio/wav,audio/mpeg,audio/flac,audio/x-hx-aac-adts,audio/x-m4a,video/mp4,video/x-ms-wma,audio/ac3,audio/aac|max:' . config('settings.podcast_max_audio_file_size', 51200),
            ]);
        } else {
            // Only mp3 and mp2 supported
            $request->validate([
                'file' => 'required|mimetypes:audio/mpeg,application/octet-stream|max:' . config('settings.podcast_max_audio_file_size', 51200),
            ]);
        }

        if ($request->file('file')->isValid()) {
            $fullFilename = $request->file('file')->getClientOriginalName();
            $fullFilename = Str::random(10) . '_' . $fullFilename;

            $getID3 = new getID3;
            $mp3Info = $getID3->analyze($request->file('file')->path());

            if (!isset($mp3Info['audio']) || !isset($mp3Info['audio']['bitrate'])) {
                header('HTTP/1.0 403 Forbidden');
                header('Content-Type: application/json');
                echo json_encode([
                    'message' => 'Not support',
                    'errors' => ['message' => [__('Audio file is not supported')]],
                ]);
                exit;
            }

            /** validate file information */
            $data = [
                'fileType' => $mp3Info['audio']['dataformat'],
                'bitRate' => intval($mp3Info['audio']['bitrate'] / 1000),
                'playtimeSeconds' => intval($mp3Info['playtime_seconds']),
            ];

            Validator::make($data, [
                'fileType' => ['required', 'regex:(mp3)'],
                'bitRate' => ['required', 'numeric', 'min:' . config('settings.podcast_min_audio_bitrate', 64), 'max:' . config('settings.podcast_max_audio_bitrate', 320)],
                'playtimeSeconds' => ['required', 'numeric', 'min:' . config('settings.podcast_min_audio_duration', 60), 'max:' . config('settings.podcast_max_audio_duration', 300)],
            ])->validate();

            $trackInfo = [];

            if (isset($mp3Info['tags']['id3v2']['title'][0])) {
                $trackInfo = $mp3Info['tags']['id3v2'];
            } elseif (isset($mp3Info['tags']['id3v1'])) {
                $trackInfo = $mp3Info['tags']['id3v1'];
            }

            $episode = Episode::create([
                'title' => $trackInfo['title'][0] ?? pathinfo($fullFilename, PATHINFO_FILENAME),
                'duration' => intval($mp3Info['playtime_seconds']),
                'podcast_id' => $podcastId,
            ]);

            $tempPath = Str::random(32);
            File::copy($filePath, Storage::disk('public')->path($tempPath));

            $audio = new stdClass;
            $audio->path = Storage::disk('public')->path($tempPath);
            $audio->original_name = $request->file('file')->getClientOriginalName();

            if (config('settings.podcast_ffmpeg') && AudioService::checkIfFFMPEGInstalled()) {
                $episode->pending = DefaultConstants::TRUE;
                $episode->save();

                if (!config('settings.podcast_audio_stream_hls')) {
                    dispatch(new ConvertToMp3($episode, $audio));
                } elseif (config('settings.podcast_audio_stream_hls')) {
                    dispatch(new ConvertToHLS($episode, $audio));
                }
            } else {
                $episode->addMedia($filePath)
                    ->usingFileName(Str::random(10) . '.mp3', PATHINFO_FILENAME)
                    ->withCustomProperties(['bitrate' => intval($mp3Info['audio']['bitrate'] / 1000)])
                    ->toMediaCollection('audio', config('settings.storage_audio_location', 'public'));
                $episode->mp3 = DefaultConstants::TRUE;
            }

            $episode->user_id = auth()->id();
            $episode->save();

            return $episode;
        }
    }

    private function skipID3v2Tag(&$block): float|int
    {
        if (str_starts_with($block, 'ID3')) {
            $id3v2MajorVersion = ord($block[3]);
            $id3v2MinorVersion = ord($block[4]);
            $id3v2Flags = ord($block[5]);
            $flagUnsynchronisation = $id3v2Flags & 0x80 ? DefaultConstants::TRUE : DefaultConstants::FALSE;
            $flagExtendedHeader = $id3v2Flags & 0x40 ? DefaultConstants::TRUE : DefaultConstants::FALSE;
            $flagExperimentalInd = $id3v2Flags & 0x20 ? DefaultConstants::TRUE : DefaultConstants::FALSE;
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

    public static function parseFrameHeader($fourBytes): array
    {
        static $versions = [
            0x0 => '2.5',
            0x1 => 'x',
            0x2 => '2',
            0x3 => '1', // x=>'reserved'
        ];
        static $layers = [
            0x0 => 'x',
            0x1 => '3',
            0x2 => '2',
            0x3 => '1',
            // x=>'reserved'
        ];
        static $bitRates = [
            'V1L1' => [0, 32, 64, 96, 128, 160, 192, 224, 256, 288, 320, 352, 384, 416, 448],
            'V1L2' => [0, 32, 48, 56, 64, 80, 96, 112, 128, 160, 192, 224, 256, 320, 384],
            'V1L3' => [0, 32, 40, 48, 56, 64, 80, 96, 112, 128, 160, 192, 224, 256, 320],
            'V2L1' => [0, 32, 48, 56, 64, 80, 96, 112, 128, 144, 160, 176, 192, 224, 256],
            'V2L2' => [0, 8, 16, 24, 32, 40, 48, 56, 64, 80, 96, 112, 128, 144, 160],
            'V2L3' => [0, 8, 16, 24, 32, 40, 48, 56, 64, 80, 96, 112, 128, 144, 160],
        ];
        static $sampleRates = [
            '1' => [44100, 48000, 32000],
            '2' => [22050, 24000, 16000],
            '2.5' => [11025, 12000, 8000],
        ];
        static $samples = [
            1 => [
                1 => 384,
                2 => 1152,
                3 => 1152,
            ],
            2 => [
                1 => 384,
                2 => 1152,
                3 => 576,
            ],
        ];

        $b1 = ord($fourBytes[1]);
        $b2 = ord($fourBytes[2]);
        $b3 = ord($fourBytes[3]);

        $versionBits = ($b1 & 0x18) >> 3;
        $version = $versions[$versionBits];
        $simpleVersion = ($version == '2.5' ? 2 : $version);

        $layerBits = ($b1 & 0x06) >> 1;
        $layer = $layers[$layerBits];

        $protectionBit = ($b1 & 0x01);
        $bitrateKey = sprintf('V%dL%d', $simpleVersion, $layer);
        $bitrateIdx = ($b2 & 0xF0) >> 4;
        $bitrate = $bitRates[$bitrateKey][$bitrateIdx] ?? 0;

        $sampleRateIdx = ($b2 & 0x0C) >> 2; // 0xc => b1100
        $sampleRate = $sampleRates[$version][$sampleRateIdx] ?? 0;
        $paddingBit = ($b2 & 0x02) >> 1;
        $privateBit = ($b2 & 0x01);
        $channelModeBits = ($b3 & 0xC0) >> 6;
        $modeExtensionBits = ($b3 & 0x30) >> 4;
        $copyrightBit = ($b3 & 0x08) >> 3;
        $originalBit = ($b3 & 0x04) >> 2;
        $emphasis = ($b3 & 0x03);

        return [
            'Version' => $version,
            'Layer' => $layer,
            'Bitrate' => $bitrate,
            'Sampling Rate' => $sampleRate,
            'Framesize' => self::frameSize($layer, $bitrate, $sampleRate, $paddingBit),
            'Samples' => $samples[$simpleVersion][$layer],
        ];
    }

    private static function frameSize($layer, $bitrate, $sampleRate, $paddingBit): int
    {
        return $layer == 1
            ? intval(((12 * $bitrate * 1000 / $sampleRate) + $paddingBit) * 4)
            : intval(((144 * $bitrate * 1000) / $sampleRate) + $paddingBit);
    }

    protected function extracted($row, Song $song, $artistName): void
    {
        if (isset($row->id)) {
            $song->artistIds = $row->id;
            $song->genre = $row->genre;
        } else {
            $artist = Artist::create([
                'name' => $artistName ?: ArtistConstants::VARIOUS,
            ]);

            $song->artistIds = $artist->id;
        }
    }
}
