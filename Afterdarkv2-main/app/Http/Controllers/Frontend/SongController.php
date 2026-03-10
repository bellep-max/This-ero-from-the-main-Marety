<?php

namespace App\Http\Controllers\Frontend;

use App\Constants\DefaultConstants;
use App\Constants\TypeConstants;
use App\Http\Controllers\Controller;
use App\Http\Requests\Frontend\Song\SongUpdateRequest;
use App\Http\Resources\CommentResource;
use App\Http\Resources\Song\SongEditResource;
use App\Http\Resources\Song\SongFullResource;
use App\Http\Resources\User\UserProfileResource;
use App\Models\History;
use App\Models\Song;
use App\Models\User;
use App\Services\NotificationService;
use App\Services\SongService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Inertia\Inertia;
use Inertia\Response;
use ZipArchive;

class SongController extends Controller
{
    public function __construct(
        private SongService $songService,
        private readonly NotificationService $notificationService,
    ) {}

    public function topDaily()
    {
        $songs = History::query()
            ->select([
                'id',
                'historyable_id',
                'historyable_type',
                'interaction_count',
                DB::raw("DATE_FORMAT(updated_at, '%Y-%m-%d')"),
                DB::raw('RANK() OVER (ORDER BY interaction_count DESC) interaction_count_rank'),
            ])
            ->where(DB::raw('DATE(updated_at)'), now()->toDateString())
            ->with('historyable')
            ->paginate(20);

        return View::make('song.daily')
            ->with([
                'songs' => $songs,
            ]);
    }

    public function topWeekly()
    {
        $songs = History::query()
            ->select([
                'id',
                'historyable_id',
                'historyable_type',
                'interaction_count',
                DB::raw("DATE_FORMAT(updated_at, '%Y-%m-%d')"),
                DB::raw('RANK() OVER (ORDER BY interaction_count DESC) interaction_count_rank'),
            ])
            ->where(DB::raw('DATE(updated_at)'), '>=', now()->subDays(7)->toDateString())
            ->with('historyable')
            ->groupBy('historyable_id')
            ->paginate(20);

        return View::make('song.daily')
            ->with([
                'songs' => $songs,
            ]);
    }

    public function show(Song $song, Request $request): Response
    {
        Gate::authorize('show', $song);

        $song->loadMissing([
            'tags',
            'genres',
            'user',
            'slides' => function ($query) use ($song) {
                $query->when(auth()->id() !== $song->user_id, function ($query) {
                    $query->where('is_visible', DefaultConstants::TRUE);
                });
            },
        ])
            ->loadCount('fans');

        if ($song->allow_comments) {
            $song->loadMissing([
                'comments' => function ($query) {
                    $query->whereNull('parent_id')
                        ->orderBy('created_at', 'desc')
                        ->with([
                            'replies.user:id,name',
                            'user:id,name',
                        ]);
                },
            ]);
        }

        if ($request->hasValidSignature() && auth()->id() !== $song->user_id) {
            $song->increment('referral_plays');
        }

        //        $view = View::make('song.index')
        //            ->with([
        //                'song' => $song,
        //                'comments' => $song->comments()->with('user')->get(),
        //                'activeUserSubscription' => $request->user() ? $request->user()->activeUserSubscription($song->user_id) : null,
        //                'canHavePaidSubscriptions' => (bool) $song->user->activeSubscription(),
        //            ]);

        //        if (count($song->artists) == 1) {
        //            $artist = $song->artists->first();
        //            $artist->setRelation('songs', $artist->songs()->where('id', '!=', $song->id)->paginate(5));
        //        }

        $this->notificationService->markAsRead($song);

        return Inertia::render('Track/Show', [
            'user' => UserProfileResource::make($song->user),
            'track' => SongFullResource::make($song),
            'comments' => CommentResource::collection($song->comments),
        ]);
    }

    public function edit(Song $song): Response
    {
        Gate::authorize('edit', $song);

        $song->loadMissing(
            'tags',
            'genres',
            'vocal'
        )->loadCount(
            'comments',
            'loves',
            'playlists',
        );

        return Inertia::render('Song/Edit', [
            'user' => UserProfileResource::make($song->user),
            'song' => SongEditResource::make($song),
        ]);
    }

    public function update(Song $song, SongUpdateRequest $request): RedirectResponse
    {
        Gate::authorize('edit', $song);

        $this->songService->updateSong($song, $request);

        session()->flash('message', [
            'level' => 'success',
            'content' => 'The song was successfully updated.',
        ]);

        return to_route('songs.show', $song);
    }

    public function destroy(Song $song): RedirectResponse
    {
        Gate::authorize('destroy', $song);

        session()->flash('message', $song->delete()
            ? [
                'level' => 'success',
                'content' => 'Song deleted successfully',
            ] : [
                'level' => 'danger',
                'content' => 'Song could not be deleted',
            ]);

        return to_route('users.tracks', $song->user);
    }

    public function download(Song $song): JsonResponse
    {
        return response()
            ->json([
                'download_url' => $this->songService->downloadSong($song),
            ]);
    }

    public function downloadHd(Song $song): JsonResponse
    {
        return response()
            ->json([
                'download_url' => $this->songService->downloadSong($song, true),
            ]);
    }

    public function autoplay(Request $request): JsonResponse
    {
        $request->validate([
            'type' => 'required|string|in:activity,song,artist,album,playlist,queue,user,genre,recent,community,obsessed,trending',
            'id' => 'nullable|integer',
            'recent_songs' => 'nullable|string',
        ]);

        $song = new Song;

        switch ($request->input('type')) {
            case TypeConstants::QUEUE:
            case TypeConstants::SONG:
                break;
            case TypeConstants::ARTIST:
                $song = $song->where('artistIds', 'REGEXP', '(^|,)(' . $request->input('id') . ')(,|$)');
                break;
            case TypeConstants::ALBUM:
                $song = $song->leftJoin('album_songs', 'album_songs.song_id', '=', 'songs.id')
                    ->select(['songs.*', 'album_songs.id as host_id']);
                $song = $song->where(function ($query) use ($request) {
                    $query->where('album_songs.album_id', '=', $request->input('id'));
                });
                break;
            case TypeConstants::PLAYLIST:
                $song = $song->leftJoin('playlist_songs', 'playlist_songs.song_id', '=', 'songs.id')
                    ->select(['songs.*', 'playlist_songs.id as host_id']);
                $song = $song->where(function ($query) use ($request) {
                    $query->where('playlist_songs.playlist_id', '=', $request->input('id'));
                });
                break;
            case TypeConstants::RECENT:
            case TypeConstants::USER:
                $user = User::find($request->input('id'));
                $song = $user->recent();
                break;
            case TypeConstants::GENRE:
                $song = $song->where('genre', 'REGEXP', '(^|,)(' . implode(',', $request->input('id')) . ')(,|$)');
                break;
            case TypeConstants::COMMUNITY:
                $user = User::find($request->input('id'));
                $song = $user->communitySongs();
                break;
            case TypeConstants::OBSESSED:
                $user = User::find($request->input('id'));
                $song = $user->obsessed();
                break;
            default:
                $song = new Song;
                break;
        }

        if ($request->input('recent_songs')) {
            $song = $song->whereNotIn('songs.id', explode(',', $request->input('recent_songs')));
        }

        $song = $song->inRandomOrder()->first();

        return response()->json($song);
    }

    public function bulkDownload(Request $request)
    {
        $zip = new ZipArchive;

        if ($zip->open('archive.zip', ZipArchive::CREATE) === true) {
            $profile = auth()->user();
            /* $session = $request->session()->all();
            dd($session);
            $this->getProfile();
            dd(2); */
            $tracks = $profile->tracks()/* ->latest() */ ->get();
            // dd($tracks[0);
            foreach ($tracks as $track) {
                if ($track->is_adventure != DefaultConstants::TRUE/*  && ($track->id == 10263 || $track->id == 10261) */) {
                    $media = $track->getFirstMedia('audio');
                    if ($media) {
                        $fullUrl = $media->getFullUrl();
                        $zip->addFromString($track->title . '.mp3', file_get_contents($fullUrl));
                    }

                    /* $a = $track->meid->getFullUrl();
                    dd($a); */
                    /* $file = new Download(
                        $track->getFirstMedia('audio'),
                        $track->title . '.mp3',
                        intval(Group::getValue('option_download_resume')),
                        intval(Group::getValue('option_download_speed'))
                    );
                    $file->downloadFile();
                    die(); */
                }
                /*  dd($track->getFirstMedia('audio'));

                dd($track->downloadFile()); */
            }
            $zip->close();

            return @response()
                ->download(public_path('archive.zip'))
                ->deleteFileAfterSend(true);

            /* header("Pragma: public");
            header("Expires: 0");
            header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
            header("Cache-Control: private", false);
            header("Content-Type: " . "application/zip");
            header('Content-Disposition: attachment; filename="' . "archive.zip" . '"');
            header("Location: archive.zip");

            header("Connection: close"); */
        }
    }
}
