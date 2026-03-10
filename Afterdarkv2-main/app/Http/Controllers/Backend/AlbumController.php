<?php

namespace App\Http\Controllers\Backend;

use App\Constants\DefaultConstants;
use App\Helpers\MessageHelper;
use App\Models\Album;
use App\Models\AlbumSong;
use App\Models\Collection;
use App\Models\Email;
use App\Models\Song;
use App\Services\ArtworkService;
use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AlbumController
{
    private $request;

    private const DEFAULT_ROUTE = 'backend.albums.index';

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function index(): View|Application|Factory
    {
        $albums = Album::query()
            ->withoutGlobalScopes()
            ->when($this->request->has('term'), function ($query) {
                $query->where('title', 'LIKE', '%' . $this->request->input('term') . '%');
            })
            ->when($this->request->input('artistIds') && is_array($this->request->input('artistIds')), function ($query) {
                $query->orWhereHas('artists', function ($query) {
                    $query->whereIn('id', $this->request->input('artistIds'));
                });
            })
            ->when($this->request->input('genre') && is_array($this->request->input('genre')), function ($query) {
                $query->orWhereHas('genres', function ($query) {
                    $query->whereIn('id', $this->request->input('genre'));
                });
            })
            ->when($this->request->has('created_from'), function ($query) {
                $query->where('created_at', '>=', Carbon::parse($this->request->input('created_from')));
            })
            ->when($this->request->has('created_until'), function ($query) {
                $query->where('created_at', '<=', Carbon::parse($this->request->input('created_until')));
            })
            ->when($this->request->input('comment_count_from'), function ($query) {
                $query->where('comment_count', '>=', intval($this->request->input('comment_count_from')));
            })
            ->when($this->request->input('comment_count_until'), function ($query) {
                $query->where('comment_count', '<=', intval($this->request->input('comment_count_until')));
            })
            ->when($this->request->has('comment_disabled'), function ($query) {
                $query->where('allow_comments', DefaultConstants::FALSE);
            })
            ->when($this->request->has('not_approved'), function ($query) {
                $query->where('approved', DefaultConstants::FALSE);
            })
            ->when($this->request->has('hidden'), function ($query) {
                $query->where('is_visible', DefaultConstants::FALSE);
            })
            ->when($this->request->has('hidden'), function ($query) {
                $query->where('is_explicit', DefaultConstants::FALSE);
            })
            ->when($this->request->has('approved'), function ($query) {
                $query->where('approved', $this->request->input('approved'));
            })
            ->when($this->request->has('title'), function ($query) {
                $query->orderBy('title', $this->request->input('title'));
            });

        return view('backend.albums.index')
            ->with([
                'albums' => $this->request->has('results_per_page')
                    ? $albums->paginate(intval($this->request->input('results_per_page')))
                    : $albums->paginate(20),
            ]);
    }

    public function create(): View|Application|Factory
    {
        return view('backend.albums.create');
    }

    public function store(): RedirectResponse
    {
        $this->request->validate([
            'name' => 'required|string',
            'artistIds' => 'required|array',
            'created_at' => 'nullable|date_format:m/d/Y',
            'released_at' => 'nullable|date_format:m/d/Y',
        ]);

        $album = Album::create([
            'title' => $this->request->input('name'),
            'description' => $this->request->input('description'),
            'approved' => DefaultConstants::TRUE,
            'user_id' => auth()->id(),
            'copyright' => $this->request->boolean('copyright')
                ? $this->request->input('copyright')
                : '',
            'released_at' => $this->request->boolean('released_at')
                ? Carbon::parse($this->request->input('released_at'))
                : null,
            'created_at' => $this->request->boolean('created_at')
                ? Carbon::parse($this->request->input('created_at'))
                : null,
        ]);

        if (is_array($this->request->input('genre'))) {
            $album->genres()->sync($this->request->input('genre'));
        }

        if (is_array($this->request->input('artistIds'))) {
            $album->artists()->sync($this->request->input('artistIds'));
        }

        if ($this->request->hasFile('artwork')) {
            ArtworkService::updateArtwork($this->request, $album);
        }

        return MessageHelper::redirectMessage('Album successfully added!', self::DEFAULT_ROUTE);
    }

    public function edit(Album $album): View|Application|Factory
    {
        return view('backend.albums.edit')
            ->with([
                'album' => $album->load('songTest', 'artists')
                    ->loadCount('songTest'),
                'albumGenres' => $album->genres->pluck('id')->toArray(),
            ]);
    }

    public function update(): RedirectResponse
    {
        $this->request->validate([
            'name' => 'required|string',
            'artistIds' => 'required|array',
            'created_at' => 'nullable|date_format:m/d/Y',
            'released_at' => 'nullable|date_format:m/d/Y',
        ]);

        $album = Album::query()
            ->withoutGlobalScopes()
            ->findOrFail($this->request->route('id'));

        if ($this->request->hasFile('artwork')) {
            ArtworkService::updateArtwork($this->request, $album);
        }

        $album->title = $this->request->input('name');
        $album->description = $this->request->input('description');
        $album->copyright = $this->request->input('copyright') ? $this->request->input('copyright') : '';

        $album->released_at = Carbon::parse($this->request->input('released_at'));
        $album->created_at = Carbon::parse($this->request->input('created_at'));

        if (!$album->approved && $this->request->input('approved')) {
            try {
                (new Email)->approvedAlbum($album->user, $album);
            } catch (Exception $e) {
            }
        }

        $album->approved = $this->request->input('approved');

        if (is_array($this->request->input('genre'))) {
            $album->genres()->sync($this->request->input('genre'));
        }

        if (is_array($this->request->input('artistIds'))) {
            $album->artists()->sync($this->request->input('artistIds'));
        }

        $album->save();

        if ($this->request->input('update-song-artwork')) {
            if ($album->getFirstMediaUrl('artwork')) {
                $album_artwork = $album->getMedia('artwork')->first();
                foreach ($album->songs()->withoutGlobalScopes()->get() as $song) {
                    $song->clearMediaCollection('artwork');
                    $album_artwork->copy($song, 'artwork');
                }
            }
        }

        return MessageHelper::redirectMessage('Album successfully edited!', self::DEFAULT_ROUTE);
    }

    public function destroy(): RedirectResponse
    {
        Album::query()
            ->withoutGlobalScopes()
            ->where('id', $this->request->route('id'))
            ->delete();

        AlbumSong::query()
            ->where('album_id', '=', $this->request->route('id'))
            ->delete();

        return MessageHelper::redirectMessage('Album successfully deleted!', self::DEFAULT_ROUTE);
    }

    public function trackList(Album $album): View|Application|Factory
    {
        return view('backend.albums.tracklist')
            ->with([
                'album' => $album->load('songs'),
            ]);
    }

    public function trackListMassAction()
    {
        if (!$this->request->input('action')) {
            $songIds = $this->request->input('songIds');

            foreach ($songIds as $index => $songId) {
                AlbumSong::query()
                    ->where('album_id', $this->request->route('id'))
                    ->where('song_id', $songId)
                    ->update([
                        'priority' => $index,
                    ]);
            }

            return MessageHelper::redirectMessage('Priority successfully changed!');
        }

        $this->request->validate([
            'action' => 'required|string',
            'ids' => 'required|array',
        ]);

        if ($this->request->input('action') == 'remove_from_album') {
            AlbumSong::query()
                ->whereIn('song_id', $this->request->input('ids'))
                ->delete();

            return MessageHelper::redirectMessage('Songs successfully removed from the album!');
        } elseif ($this->request->input('action') == 'delete') {
            Song::query()
                ->withoutGlobalScopes()
                ->whereIn('id', $this->request->input('ids'))
                ->delete();

            return MessageHelper::redirectMessage('Songs successfully deleted!');
        }
    }

    public function upload(Album $album): View|Application|Factory
    {
        return view('backend.albums.upload')
            ->with([
                'album' => $album,
            ]);
    }

    public function reject(): RedirectResponse
    {
        $this->request->validate([
            'comment' => 'nullable|string',
        ]);

        $album = Album::query()
            ->withoutGlobalScopes()
            ->findOrFail($this->request->route('id'));

        (new Email)->rejectedAlbum($album->user, $album, $this->request->input('comment'));

        $album->delete();

        return MessageHelper::redirectMessage('Album successfully rejected!', self::DEFAULT_ROUTE);
    }

    public function batch()
    {
        $this->request->validate([
            'action' => 'required|string',
            'ids' => 'required|array',
        ]);

        if ($this->request->input('action') == 'add_genre') {
            $message = 'Add genre';
            $subMessage = 'Add Genre for Chosen Albums (<strong>' . count($this->request->input('ids')) . '</strong>)';

            return view('backend.commons.mass_genre')
                ->with([
                    'message' => $message,
                    'subMessage' => $subMessage,
                    'action' => $this->request->input('action'),
                    'ids' => $this->request->input('ids'),
                ]);
        } elseif ($this->request->input('action') == 'save_add_genre') {
            Album::query()
                ->whereIn('id', $this->request->input('ids'))
                ->chunk(100, function (Collection $albums) {
                    foreach ($albums as $album) {
                        $album->genres()->attach($this->request->input('genre'));
                    }
                });

            return MessageHelper::redirectMessage('Album successfully saved!', self::DEFAULT_ROUTE);
        } elseif ($this->request->input('action') == 'change_genre') {
            $message = 'Change genre';
            $subMessage = 'Change Genre for Chosen Albums (<strong>' . count($this->request->input('ids')) . '</strong>)';

            return view('backend.commons.mass_genre')
                ->with([
                    'message' => $message,
                    'subMessage' => $subMessage,
                    'action' => $this->request->input('action'),
                    'ids' => $this->request->input('ids'),
                ]);
        } elseif ($this->request->input('action') == 'save_change_genre') {
            Album::query()
                ->whereIn('id', $this->request->input('ids'))
                ->chunk(100, function (Collection $albums) {
                    foreach ($albums as $album) {
                        $album->genres()->sync($this->request->input('genre'));
                    }
                });

            return MessageHelper::redirectMessage('Albums successfully saved!', self::DEFAULT_ROUTE);
        } elseif ($this->request->input('action') == 'change_artist') {
            $message = 'Change artist';
            $subMessage = 'Change Album for Chosen Albums (<strong>' . count($this->request->input('ids')) . '</strong>)';

            return view('backend.commons.mass_artist')
                ->with([
                    'message' => $message,
                    'subMessage' => $subMessage,
                    'action' => $this->request->input('action'),
                    'ids' => $this->request->input('ids'),
                ]);
        } elseif ($this->request->input('action') == 'save_change_artist') {
            Album::query()
                ->whereIn('id', $this->request->input('ids'))
                ->chunk(100, function (Collection $albums) {
                    foreach ($albums as $album) {
                        $album->artists()->attach($this->request->input('artistIds'));
                    }
                });

            return MessageHelper::redirectMessage('Albums successfully updated!', self::DEFAULT_ROUTE);
        } elseif ($this->request->input('action') == 'approve') {
            Album::query()
                ->withoutGlobalScopes()
                ->whereIn('id', $this->request->input('ids'))
                ->update([
                    'approved' => DefaultConstants::TRUE,
                ]);

            return MessageHelper::redirectMessage('Albums successfully updated!', self::DEFAULT_ROUTE);
        } elseif ($this->request->input('action') == 'not_approve') {
            Album::query()
                ->withoutGlobalScopes()
                ->whereIn('id', $this->request->input('ids'))
                ->update([
                    'approved' => DefaultConstants::FALSE,
                ]);

            return MessageHelper::redirectMessage('Album successfully updated!', self::DEFAULT_ROUTE);
        } elseif ($this->request->input('action') == 'comments') {
            Album::query()
                ->withoutGlobalScopes()
                ->whereIn('id', $this->request->input('ids'))
                ->update([
                    'allow_comments' => DefaultConstants::TRUE,
                ]);

            return MessageHelper::redirectMessage('Album successfully updated!', self::DEFAULT_ROUTE);
        } elseif ($this->request->input('action') == 'not_comments') {
            Album::query()
                ->withoutGlobalScopes()
                ->whereIn('id', $this->request->input('ids'))
                ->update([
                    'allow_comments' => DefaultConstants::FALSE,
                ]);

            return MessageHelper::redirectMessage('Album successfully updated!', self::DEFAULT_ROUTE);
        } elseif ($this->request->input('action') == 'clear_count') {
            Album::query()
                ->withoutGlobalScopes()
                ->whereIn('id', $this->request->input('ids'))
                ->update([
                    'plays' => 0,
                ]);

            return MessageHelper::redirectMessage('Album successfully updated!', self::DEFAULT_ROUTE);
        } elseif ($this->request->input('action') == 'delete') {
            Album::query()
                ->withoutGlobalScopes()
                ->whereIn('id', $this->request->input('ids'))
                ->delete();

            return MessageHelper::redirectMessage('Album successfully updated!', self::DEFAULT_ROUTE);
        }
    }

    public function search(Request $request): JsonResponse
    {
        $result = Album::query()
            ->where('name', 'LIKE', "%{$request->input('q')}%")
            ->paginate(20);

        return response()->json($result);
    }
}
