<?php

namespace App\Http\Controllers\Backend;

use App\Constants\ActionConstants;
use App\Constants\DefaultConstants;
use App\Helpers\MessageHelper;
use App\Http\Requests\Backend\Playlist\PlaylistIndexRequest;
use App\Http\Requests\Backend\Playlist\PlaylistMassActionRequest;
use App\Http\Requests\Backend\Playlist\PlaylistStoreRequest;
use App\Http\Requests\Backend\Playlist\PlaylistUpdateRequest;
use App\Models\Playlist;
use App\Services\ArtworkService;
use Carbon\Carbon;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PlaylistController
{
    private const DEFAULT_ROUTE = 'backend.playlists.index';

    public function index(PlaylistIndexRequest $request)
    {
        $playlists = Playlist::query()->withoutGlobalScopes()
            ->when($request->has('term'), function ($query) use ($request) {
                $query->where('title', 'like', '%' . $request->input('term') . '%');
            })
            ->when($request->input('userIds'), function ($query) use ($request) {
                $query->whereIn('user_id', $request->input('userIds'));
            })
            ->when($request->input('genre'), function ($query) use ($request) {
                $query->orWhereHas('genres', function ($query) use ($request) {
                    $query->whereIn('id', $request->input('genre'));
                });
            })
            ->when($request->input('created_from'), function ($query) use ($request) {
                $query->where('created_at', '>=', Carbon::parse($request->input('created_from')));
            })
            ->when($request->input('created_until'), function ($query) use ($request) {
                $query->where('created_at', '<=', Carbon::parse($request->input('created_until')));
            })
            ->when($request->input('comment_count_from'), function ($query) use ($request) {
                $query->where('comment_count', '>=', $request->input('comment_count_from'));
            })
            ->when($request->input('comment_count_until'), function ($query) use ($request) {
                $query->where('comment_count', '<=', $request->input('comment_count_until'));
            })
            ->when($request->input('comment_disabled'), function ($query) {
                $query->where('allow_comments', DefaultConstants::FALSE);
            })
            ->when($request->input('not_approved'), function ($query) {
                $query->where('approved', DefaultConstants::FALSE);
            })
            ->when($request->input('hidden'), function ($query) {
                $query->where('is_visible', DefaultConstants::FALSE);
            })
            ->when($request->input('hidden'), function ($query) {
                $query->where('is_explicit', DefaultConstants::FALSE);
            })
            ->when($request->has('loves'), function ($query) use ($request) {
                $query->orderBy('loves', $request->input('loves'));
            })
            ->when($request->has('plays'), function ($query) use ($request) {
                $query->orderBy('plays', $request->input('loves'));
            })
            ->when($request->has('title'), function ($query) use ($request) {
                $query->orderBy('title', $request->input('loves'));
            });

        return view('backend.playlists.index')
            ->with([
                'playlists' => $request->has('results_per_page')
                    ? $playlists->paginate(intval($request->input('results_per_page')))
                    : $playlists->paginate(20),
            ]);
    }

    public function edit(Playlist $playlist): View|Application|Factory
    {
        return view('backend.playlists.edit')
            ->with([
                'playlist' => $playlist->load('user'),
                'playlistGenres' => $playlist->genres()->pluck('genres.id')->toArray(),
            ]);
    }

    public function store(PlaylistStoreRequest $request): RedirectResponse
    {
        $playlist = Playlist::create($request->input());

        if ($request->hasFile('artwork')) {
            ArtworkService::updateArtwork($request, $playlist);
        }

        if ($request->filled('genre')) {
            $playlist->genres()->sync($request->input('genre'));
        }

        return MessageHelper::redirectMessage('Playlist successfully updated!', self::DEFAULT_ROUTE);
    }

    public function update(Playlist $playlist, PlaylistUpdateRequest $request): RedirectResponse
    {
        $playlist->update($request->input());

        if ($request->hasFile('artwork')) {
            ArtworkService::updateArtwork($request, $playlist);
        }

        if ($request->filled('genre')) {
            $playlist->genres()->sync($request->input('genre'));
        }

        return MessageHelper::redirectMessage('Playlist successfully updated!', self::DEFAULT_ROUTE);
    }

    public function destroy(Playlist $playlistVisible): RedirectResponse
    {
        $playlistVisible->delete();

        return MessageHelper::redirectMessage('Playlist successfully deleted!', self::DEFAULT_ROUTE);
    }

    public function batch(PlaylistMassActionRequest $request)
    {
        $playlistQuery = Playlist::query()
            ->withoutGlobalScopes()
            ->whereIn('id', $request->input('ids'));

        if ($request->input('action') == ActionConstants::ADD_GENRE) {
            return view('backend.commons.mass_genre')
                ->with([
                    'message' => 'Add genre',
                    'subMessage' => 'Add Genre for Chosen Playlists (<strong>' . count($request->input('ids')) . '</strong>)',
                    'action' => $request->input('action'),
                    'ids' => $request->input('ids'),
                ]);
        } elseif ($request->input('action') == ActionConstants::STORE_GENRE) {
            $playlistQuery->chunk(200, function (Collection $playlists) use ($request) {
                foreach ($playlists as $playlist) {
                    $playlist->genres()->syncWithoutDetaching($request->input('genre'));
                }
            });

            return MessageHelper::redirectMessage('Playlists successfully updated!', self::DEFAULT_ROUTE);
        } elseif ($request->input('action') == ActionConstants::EDIT_GENRE) {
            return view('backend.commons.mass_genre')
                ->with([
                    'message' => 'Change genre',
                    'subMessage' => 'Change Genre for Chosen Playlists (<strong>' . count($request->input('ids')) . '</strong>)',
                    'action' => $request->input('action'),
                    'ids' => $request->input('ids'),
                ]);
        } elseif ($request->input('action') == ActionConstants::UPDATE_GENRE) {
            $playlistQuery->chunk(200, function (Collection $playlists) use ($request) {
                foreach ($playlists as $playlist) {
                    $playlist->genres()->sync($request->input('genre'));
                }
            });

            return MessageHelper::redirectMessage('Playlists successfully updated!', self::DEFAULT_ROUTE);
        } elseif ($request->input('action') == ActionConstants::SHOW) {
            $playlistQuery->update([
                'is_visible' => DefaultConstants::TRUE,
            ]);

            return MessageHelper::redirectMessage('Playlists successfully updated!', self::DEFAULT_ROUTE);
        } elseif ($request->input('action') == ActionConstants::HIDE) {
            $playlistQuery->update([
                'is_visible' => DefaultConstants::FALSE,
            ]);

            return MessageHelper::redirectMessage('Playlists successfully updated!', self::DEFAULT_ROUTE);
        } elseif ($request->input('action') == ActionConstants::SHOW) {
            $playlistQuery->update([
                'is_explicit' => DefaultConstants::TRUE,
            ]);

            return MessageHelper::redirectMessage('Playlists successfully updated!', self::DEFAULT_ROUTE);
        } elseif ($request->input('action') == ActionConstants::HIDE) {
            $playlistQuery->update([
                'is_explicit' => DefaultConstants::FALSE,
            ]);

            return MessageHelper::redirectMessage('Playlists successfully updated!', self::DEFAULT_ROUTE);
        } elseif ($request->input('action') == ActionConstants::COMMENTED) {
            $playlistQuery->update([
                'allow_comments' => DefaultConstants::TRUE,
            ]);

            return MessageHelper::redirectMessage('Playlists successfully updated!', self::DEFAULT_ROUTE);
        } elseif ($request->input('action') == ActionConstants::UNCOMMENTED) {
            $playlistQuery->update([
                'allow_comments' => DefaultConstants::FALSE,
            ]);

            return MessageHelper::redirectMessage('Playlists successfully updated!', self::DEFAULT_ROUTE);
        } elseif ($request->input('action') == ActionConstants::DELETE) {
            $playlistQuery->delete();

            return MessageHelper::redirectMessage('Playlists successfully deleted!', self::DEFAULT_ROUTE);
        }
    }

    public function search(Request $request): JsonResponse
    {
        $result = Playlist::query()
            ->where('title', 'LIKE', "%{$request->input('q')}%")
            ->paginate(20);

        return response()->json($result);
    }
}
