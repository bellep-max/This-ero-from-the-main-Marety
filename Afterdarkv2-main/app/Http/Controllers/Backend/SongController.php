<?php

/**
 * Created by NiNaCoder.
 * Date: 2019-05-24
 * Time: 20:12.
 */

namespace App\Http\Controllers\Backend;

use App\Constants\ActionConstants;
use App\Constants\DefaultConstants;
use App\Helpers\MessageHelper;
use App\Http\Requests\Backend\Song\SongIndexRequest;
use App\Http\Requests\Backend\Song\SongRejectRequest;
use App\Http\Requests\Backend\Song\SongTitleUpdateRequest;
use App\Http\Requests\Backend\Song\SongUpdateRequest;
use App\Models\Email;
use App\Models\Song;
use App\Services\ArtworkService;
use App\Services\Backend\BackendService;
use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SongController
{
    private const DEFAULT_ROUTE = 'backend.songs.index';

    public function index(SongIndexRequest $request): View|Application|Factory
    {
        $songs = Song::query()
            ->withoutGlobalScopes()
            ->when($request->input('term'), function ($query) use ($request) {
                $query->where('title', 'LIKE', '%' . $request->input('term') . '%');
            })
            ->when($request->input('artistIds'), function ($query) use ($request) {
                $query->orWhereHas('artists', function ($query) use ($request) {
                    $query->whereIn('id', $request->input('artistIds'));
                });
            })
            ->when($request->input('userIds'), function ($query) use ($request) {
                $query->orWhereIn('id', $request->input('userIds'));
            })
            ->when($request->input('genre'), function ($query) use ($request) {
                $query->orWhereHas('genres', function ($query) use ($request) {
                    $query->whereIn('id', $request->input('genre'));
                });
            })
            ->when($request->input('created_at'), function ($query) use ($request) {
                $query->where('created_at', '>=', $request->input('created_from'));
            })
            ->when($request->input('created_until'), function ($query) use ($request) {
                $query->where('created_at', '<=', $request->input('created_until'));
            })
            ->when($request->input('comment_count_from'), function ($query) use ($request) {
                $query->where('comment_count', '>=', $request->input('comment_count_from'));
            })
            ->when($request->input('comment_count_until'), function ($query) use ($request) {
                $query->where('comment_count', '<=', $request->input('comment_count_until'));
            })
            ->when($request->input('duration_from'), function ($query) use ($request) {
                $query->where('duration', '>=', $request->input('duration_from'));
            })
            ->when($request->input('duration_until'), function ($query) use ($request) {
                $query->where('duration', '<=', $request->input('duration_until'));
            })
            ->when($request->input('comment_disabled'), function ($query) {
                $query->where('allow_comments', DefaultConstants::FALSE);
            })
            ->when($request->input('not_approved'), function ($query) {
                $query->where('approved', DefaultConstants::FALSE);
            })
            ->when($request->input('approved'), function ($query) {
                $query->where('approved', DefaultConstants::TRUE);
            })
            ->when($request->input('hidden'), function ($query) {
                $query->where('is_visible', DefaultConstants::FALSE);
            })
            ->when($request->input('format'), function ($query) use ($request) {
                $query->where($request->input('format'), DefaultConstants::TRUE);
            })
            ->when($request->input('loves'), function ($query) use ($request) {
                $query->orderBy('loves', $request->input('loves'));
            })
            ->when($request->input('plays'), function ($query) use ($request) {
                $query->orderBy('plays', $request->input('plays'));
            })
            ->when($request->input('title'), function ($query) use ($request) {
                $query->orderBy('title', $request->input('title'));
            })
            ->when($request->input('albumIds'), function ($query) use ($request) {
                $query->leftJoin('album_songs', 'album_songs.song_id', '=', 'songs.id')
                    ->select('songs.*', 'album_songs.id as host_id')
                    ->where(function ($query) use ($request) {
                        foreach ($request->input('albumIds') as $index => $albumId) {
                            if ($index == 0) {
                                $query->where('album_songs.album_id', '=', 35);
                            } else {
                                $query->orWhere('album_songs.album_id', '=', $albumId);
                            }
                        }
                    });
            });

        return view('backend.songs.index')
            ->with([
                'songs' => $request->has('results_per_page')
                    ? $songs->paginate(intval($request->input('results_per_page')))
                    : $songs->paginate(20),
                'total_songs' => $songs->count(),
            ]);
    }

    public function edit(string $songUuid): View|Application|Factory
    {
        $song = Song::query()
            ->withoutGlobalScopes()
            ->where('uuid', $songUuid)
            ->with([
                'genres',
            ])
            ->firstOrFail();

        return view('backend.songs.edit')
            ->with([
                'song' => $song,
                'songGenres' => $song->genres()->pluck('genres.id')->toArray(),
                'options' => BackendService::groupPermission($song->access),
            ]);
    }

    public function update(string $songUuid, SongUpdateRequest $request): RedirectResponse
    {
        $song = Song::query()
            ->withoutGlobalScopes()
            ->where('uuid', $songUuid)
            ->with([
                'genres',
            ])
            ->firstOrFail();

        if (!$song->approved && $request->input('approved')) {
            try {
                (new Email)->approvedSong($song->user, $song);
            } catch (Exception $e) {
            }
        }

        $song->update($request->all());

        if ($request->filled('albumIds')) {
            $song->albums()->sync($request->input('albumIds'));
        }

        if ($request->filled('artistIds')) {
            $song->artists()->sync($request->input('artistIds'));
        }

        if ($request->filled('genre')) {
            $song->genres()->sync($request->input('genre'));
        }

        if ($request->hasFile('artwork')) {
            ArtworkService::updateArtwork($request, $song);
        }

        if ($request->input('youtube_id')) {
            $song->log()->updateOrCreate([
                'youtube' => $request->input('youtube_id'),
            ]);
        }

        return MessageHelper::redirectMessage('Song successfully updated!', self::DEFAULT_ROUTE);
    }

    public function updateTitle(string $songUuid, SongTitleUpdateRequest $request): JsonResponse
    {
        $song = Song::query()
            ->withoutGlobalScopes()
            ->where('uuid', $songUuid)
            ->with([
                'genres',
            ])
            ->firstOrFail();

        $song->update($request->validated());

        return response()->json($song);
    }

    public function destroy(string $songUuid): RedirectResponse
    {
        $song = Song::query()
            ->withoutGlobalScopes()
            ->where('uuid', $songUuid)
            ->with([
                'genres',
            ])
            ->firstOrFail();

        $song->delete();

        return MessageHelper::redirectMessage('Songs successfully deleted!', self::DEFAULT_ROUTE);
    }

    public function reject(string $songUuid, SongRejectRequest $request): RedirectResponse
    {
        $song = Song::query()
            ->withoutGlobalScopes()
            ->where('uuid', $songUuid)
            ->with([
                'genres',
            ])
            ->firstOrFail();

        (new Email)->rejectedAlbum($song->user, $song, $request->input('comment'));

        $song->delete();

        return MessageHelper::redirectMessage('Song successfully rejected!', self::DEFAULT_ROUTE);
    }

    public function batch(Request $request)
    {
        $songQuery = Song::query()
            ->whereIn('id', $request->input('ids'));

        $count = count($request->input('ids'));

        if ($request->input('action') == ActionConstants::ADD_GENRE) {
            return view('backend.commons.mass_genre')
                ->with([
                    'message' => 'Add genre',
                    'sub_message' => "Add Genre for Chosen Songs (<strong>$count</strong>)",
                    'action' => $request->input('action'),
                    'ids' => $request->input('ids'),
                ]);
        } elseif ($request->input('action') == ActionConstants::STORE_GENRE) {
            $songQuery->chunk(200, function (Collection $songs) use ($request) {
                foreach ($songs as $song) {
                    $song->genres()->syncWithoutDetaching($request->input('genre'));
                }
            });

            return MessageHelper::redirectMessage('Song successfully saved!', self::DEFAULT_ROUTE);
        } elseif ($request->input('action') == ActionConstants::EDIT_GENRE) {
            return view('backend.commons.mass_genre')
                ->with([
                    'message' => 'Change genre',
                    'sub_message' => "Change Genre for Chosen Songs (<strong>$count</strong>)",
                    'action' => $request->input('action'),
                    'ids' => $request->input('ids'),
                ]);
        } elseif ($request->input('action') == ActionConstants::UPDATE_GENRE) {
            $songQuery->chunk(200, function (Collection $songs) use ($request) {
                foreach ($songs as $song) {
                    $song->genres()->sync($request->input('genre'));
                }
            });

            return MessageHelper::redirectMessage('Song successfully saved!', self::DEFAULT_ROUTE);
        } elseif ($request->input('action') == ActionConstants::EDIT_ARTIST) {
            return view('backend.commons.mass_artist')
                ->with([
                    'message' => 'Change artist',
                    'sub_message' => "Change Artist for Chosen Songs (<strong>$count</strong>)",
                    'action' => $request->input('action'),
                    'ids' => $request->input('ids'),
                ]);
        } elseif ($request->input('action') == ActionConstants::UPDATE_ARTIST) {
            $songQuery->chunk(200, function (Collection $songs) use ($request) {
                foreach ($songs as $song) {
                    $song->artists()->sync($request->input('artistIds'));
                }
            });

            return MessageHelper::redirectMessage('Song successfully saved!', self::DEFAULT_ROUTE);
        } elseif ($request->input('action') == ActionConstants::EDIT_ALBUM) {
            return view('backend.commons.mass_album')
                ->with([
                    'message' => 'Change album',
                    'sub_message' => "Change Album for Chosen Songs (<strong>$count</strong>)",
                    'action' => $request->input('action'),
                    'ids' => $request->input('ids'),
                ]);
        } elseif ($request->input('action') == ActionConstants::UPDATE_ALBUM) {
            $songQuery->chunk(200, function (Collection $songs) use ($request) {
                foreach ($songs as $song) {
                    $song->albums()->sync($request->input('albumIds'));
                }
            });

            return MessageHelper::redirectMessage('Song successfully saved!', self::DEFAULT_ROUTE);
        } elseif ($request->input('action') == ActionConstants::APPROVE) {
            $songQuery->update([
                'approved' => DefaultConstants::TRUE,
            ]);

            return MessageHelper::redirectMessage('Song successfully saved!');
        } elseif ($request->input('action') == ActionConstants::NOT_APPROVE) {
            $songQuery->update([
                'approved' => DefaultConstants::FALSE,
            ]);

            return MessageHelper::redirectMessage('Song successfully saved!');
        } elseif ($request->input('action') == ActionConstants::COMMENTED) {
            $songQuery->update([
                'allow_comments' => DefaultConstants::TRUE,
            ]);

            return MessageHelper::redirectMessage('Song successfully saved!');
        } elseif ($request->input('action') == ActionConstants::UNCOMMENTED) {
            $songQuery->update([
                'allow_comments' => DefaultConstants::FALSE,
            ]);

            return MessageHelper::redirectMessage('Song successfully saved!');
        } elseif ($request->input('action') == ActionConstants::CLEAR_COUNT) {
            $songQuery->update([
                'plays' => 0,
            ]);

            return MessageHelper::redirectMessage('Song successfully saved!');
        } elseif ($request->input('action') == ActionConstants::DELETE) {
            $songQuery->delete();

            return redirect()
                ->back()
                ->with([
                    'status' => 'success',
                    'message' => 'Songs successfully deleted!',
                ]);
        }
    }

    public function search(Request $request): JsonResponse
    {
        $result = Song::query()
            ->where('title', 'LIKE', '%' . $request->input('q') . '%')
            ->paginate(20);

        return response()->json($result);
    }
}
