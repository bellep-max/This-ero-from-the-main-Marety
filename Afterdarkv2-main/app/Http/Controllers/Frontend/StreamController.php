<?php

namespace App\Http\Controllers\Frontend;

use App\Constants\TypeConstants;
use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\Artist;
use App\Models\Episode;
use App\Models\Group;
use App\Models\History;
use App\Models\Song;
use App\Models\Stream;
use App\Services\StreamService;
use Carbon\Carbon;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Foundation\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use JetBrains\PhpStorm\NoReturn;

class StreamController extends Controller
{
    public function __construct(private readonly StreamService $streamService) {}

    #[NoReturn]
    public function mp3(string $uuid, string $type): void
    {
        $model = $type::query()->withoutGlobalScopes()->where('uuid', $uuid)->firstOrFail();

        $this->streamService->getMp3File($model, 'audio');

        exit();
    }

    #[NoReturn]
    public function mp3HD(string $uuid, string $type): JsonResponse
    {
        $model = $type::query()->withoutGlobalScopes()->where('uuid', $uuid)->firstOrFail();

        $this->streamService->getMp3File($model, 'hd_audio');

        exit();
    }

    public function hls(string $uuid, string $type): Application|Response|ResponseFactory
    {
        $model = $type::query()->withoutGlobalScopes()->where('uuid', $uuid)->firstOrFail();

        return response($this->streamService->getHlsFile($model, 'hls'))
            ->withHeaders([
                'Content-Type' => 'text/plain',
                'Cache-Control' => 'no-store, no-cache',
                'Content-Disposition' => 'attachment; filename="track.m3u8',
            ]);
    }

    public function hlsHD(string $uuid, string $type): Application|Response|ResponseFactory
    {
        $model = $type::query()->withoutGlobalScopes()->where('uuid', $uuid)->firstOrFail();

        return response($this->streamService->getHlsFile($model, 'hd-hls'))
            ->withHeaders([
                'Content-Type' => 'text/plain',
                'Cache-Control' => 'no-store, no-cache',
                'Content-Disposition' => 'attachment; filename="track.m3u8',
            ]);
    }

    public function onTrackPlayed(Request $request): JsonResponse
    {
        // Increase song plays time
        $request->validate([
            'type' => 'string|in:song,episode',
        ]);

        if ($request->input('type') == TypeConstants::SONG) {
            $song = Song::query()
                ->withoutGlobalScopes()
                ->findOrFail($request->input('id'));

            $song->increment('plays');

            DB::statement('INSERT INTO ' . DB::getTablePrefix() . 'popular (`song_id`, `plays`, `created_at`) VALUES (' . intval($song->id) . ", '1', '" . Carbon::now() . "') ON DUPLICATE KEY UPDATE plays=plays+1");

            if ($song->user_id && config('settings.monetization') && Group::getUserValue('monetization_streaming', $song->user_id)) {
                if ($song->user_id && config('settings.monetization') && Group::getUserValue('monetization_streaming', $song->user_id)) {
                    if (request()->ip()) {
                        if (!Stream::where('streamable_id', $song->id)->where('streamable_type', Song::class)->where('ip', request()->ip())->exists()) {
                            $revenue = Group::getUserValue('monetization_streaming_rate', $song->user_id);

                            Stream::create([
                                'user_id' => $song->user_id,
                                'streamable_id' => $song->id,
                                'streamable_type' => Song::class,
                                'revenue' => $revenue,
                                'ip' => request()->ip(),
                            ]);

                            $song->user()->increment('balance', $revenue);
                        }
                    }
                }
            }

            if (auth()->check()) {
                if ($song->user_id) {
                    $song->user()->increment('balance', Group::getUserValue('monetization_streaming_rate', $song->user_id));
                }

                Helper::makeActivity(auth()->id(), $song->id, (new Song)->getMorphClass(), 'playSong', $song->id);

                History::updateOrCreate([
                    'user_id' => auth()->id(),
                    'historyable_id' => $song->id,
                    'historyable_type' => Song::class,
                ], [
                    'created_at' => now(),
                    'ownerable_type' => Artist::class,
                    'ownerable_id' => isset($song->artists[0]) ? $song->artists[0]->id : null,
                    'interaction_count' => DB::raw('interaction_count + 1'),
                ]);
            }

            return response()->json(['success' => true]);
        } else {
            $episode = Episode::findOrFail($request->input('id'));
            $episode->increment('plays');

            if ($episode->user_id && config('settings.monetization') && Group::getUserValue('monetization_streaming', $episode->user_id)) {
                if (request()->ip()) {
                    if (!Stream::where('streamable_id', $episode->id)->where('streamable_type', Episode::class)->where('ip', request()->ip())->exists()) {
                        $revenue = Group::getUserValue('monetization_streaming_rate', $episode->user_id);
                        Stream::create([
                            'user_id' => $episode->user_id,
                            'streamable_id' => $episode->id,
                            'streamable_type' => Episode::class,
                            'revenue' => $revenue,
                            'ip' => request()->ip(),
                        ]);

                        $episode->user()->increment('balance', $revenue);
                    }
                }
            }

            return response()->json(['success' => true]);
        }
    }

    public function youtube(Request $request, Song $song): JsonResponse
    {
        $videos = [];

        if (isset($song->log->youtube)) {
            $videos[] = [
                'title' => $song->title,
                'id' => [
                    'videoId' => $song->log->youtube,
                ],
            ];

            $buffer = [];
            $buffer['items'] = $videos;

            if ($request->get('callback')) {
                return response()
                    ->jsonp($request->get('callback'), $buffer)
                    ->header('Content-Type', 'application/javascript');
            }

            return response()->json($buffer);
        }

        $query = urlencode($song->title . ' ' . $song->artists[0]->name);
        $response = Http::get("https://www.youtube.com/results?search_query=$query");
        $html = $response->body();

        if (Str::contains($html, 'ytInitialData')) {
            $firstStep = explode('ytInitialData', $html);
            if (isset($firstStep[1])) {
                $secondStep = explode('</script>', $firstStep[1]);
            }
            if (isset($secondStep[0])) {
                $json = substr($secondStep[0], 2, -1);

                $json = json_decode($json, true);
                $videos = $json['contents']['twoColumnSearchResultsRenderer']['primaryContents']['sectionListRenderer']['contents'][0]['itemSectionRenderer']['contents'];

                $videos = array_filter($videos, function ($video) {
                    return isset($video['videoRenderer']);
                });

                $videos = array_slice($videos, 0, 5);
                $videos = array_map(function ($video) {
                    $video = $video['videoRenderer'];

                    return [
                        'title' => $video['title']['runs'][0]['text'],
                        'id' => [
                            'videoId' => $video['videoId'],
                        ],
                    ];
                }, $videos);
            } else {
                abort('500', "Can't get youtube video id");
            }
        } else {
            abort('500', "Can't get youtube site content");
        }

        $buffer = [];
        $buffer['items'] = $videos;

        if (count($videos)) {
            $song->log->youtube = $videos[0]['id']['videoId'];
            $song->log->save();
        }

        if ($request->get('callback')) {
            return response()
                ->jsonp($request->get('callback'), $buffer)
                ->header('Content-Type', 'application/javascript');
        }

        return response()->json($buffer);
    }
}
