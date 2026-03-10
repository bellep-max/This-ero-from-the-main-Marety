<?php

namespace App\Http\Controllers\Backend;

use App\Constants\DefaultConstants;
use App\Helpers\MessageHelper;
use App\Http\Requests\Backend\Artist\ArtistIndexRequest;
use App\Http\Requests\Backend\Artist\ArtistMassActionRequest;
use App\Http\Requests\Backend\Artist\ArtistStoreRequest;
use App\Http\Requests\Backend\Artist\ArtistUpdateRequest;
use App\Models\Artist;
use App\Models\Collection;
use App\Services\ArtworkService;
use App\Services\MigrationService;
use Carbon\Carbon;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ArtistController
{
    private const DEFAULT_ROUTE = 'backend.artists.index';

    public function index(ArtistIndexRequest $request): Factory|Application|View
    {
        //        MigrationService::createGenreRelations(Artist::class);

        $artists = Artist::query()
            ->withoutGlobalScopes()
            ->when($request->has('term'), function ($query) use ($request) {
                $query->where('name', 'like', '%' . $request->input('term') . '%');
            })
            ->when($request->has('genre'), function ($query) use ($request) {
                $query->orWhereHas('genre', function ($query) use ($request) {
                    $query->whereIn('id', $request->input('genre'));
                });
            })
            ->when($request->has('created_from'), function ($query) use ($request) {
                $query->where('created_at', '>=', Carbon::parse($request->input('created_from')));
            })
            ->when($request->has('created_until'), function ($query) use ($request) {
                $query->where('created_at', '<=', Carbon::parse($request->input('created_until')));
            })
            ->when($request->has('comment_count_from'), function ($query) use ($request) {
                $query->where('comment_count', '>=', intval($request->input('comment_count_from')));
            })
            ->when($request->has('comment_count_until'), function ($query) use ($request) {
                $query->where('comment_count', '<=', intval($request->input('comment_count_until')));
            })
            ->when($request->has('followers_from'), function ($query) use ($request) {
                $query->has('followers', '>=', intval($request->input('followers_from')));
            })
            ->when($request->has('followers_until'), function ($query) use ($request) {
                $query->has('followers', '<=', intval($request->input('followers_until')));
            })
            ->when($request->has('comment_disabled'), function ($query) {
                $query->where('allow_comments', DefaultConstants::FALSE);
            })
            ->when($request->has('verified'), function ($query) {
                $query->where('verified', DefaultConstants::TRUE);
            })
            ->when($request->has('hidden'), function ($query) {
                $query->where('is_visible', DefaultConstants::FALSE);
            })
            ->withCount('songs', 'albums', 'comments');

        return view('backend.artists.index')
            ->with([
                'artists' => $request->has('results_per_page')
                    ? $artists->paginate(intval($request->input('results_per_page')))
                    : $artists->paginate(20),
            ]);
    }

    public function create(): View|Application|Factory
    {
        return view('backend.artists.create');
    }

    public function store(ArtistStoreRequest $request): RedirectResponse
    {
        $artist = Artist::create([
            'name' => $request->input('name'),
            'bio' => $request->input('bio'),
        ]);

        $genres = explode(',', $request->input('genre'));

        $artist->genres()->sync($genres);

        if ($request->hasFile('artwork')) {
            ArtworkService::updateArtwork($request, $artist);
        }

        return MessageHelper::redirectMessage('Artist successfully added!', self::DEFAULT_ROUTE);
    }

    public function edit(Artist $artist): View|Application|Factory
    {
        return view('backend.artists.edit')
            ->with([
                'artist' => $artist,
                'artistGenres' => $artist->genres->pluck('id')->toArray(),
            ]);
    }

    public function update(Artist $artist, ArtistUpdateRequest $request): RedirectResponse
    {
        $artist->update([
            'name' => $request->input('name'),
            'bio' => $request->input('bio'),
        ]);

        if ($request->hasFile('artwork')) {
            ArtworkService::updateArtwork($request, $artist);
        }

        $genres = explode(',', $request->input('genre'));

        $artist->genres()->sync($genres);

        return MessageHelper::redirectMessage('Artist successfully updated!', self::DEFAULT_ROUTE);
    }

    public function destroy(Artist $artist): RedirectResponse
    {
        $artist->delete();

        return MessageHelper::redirectMessage('Artist successfully deleted!', self::DEFAULT_ROUTE);
    }

    public function upload(Artist $artist): View|Application|Factory
    {
        return view('backend.artists.upload')
            ->with([
                'artist' => $artist,
            ]);
    }

    public function batch(ArtistMassActionRequest $request)
    {
        if ($request->input('action') == 'add_genre') {
            return view('backend.commons.mass_genre')
                ->with([
                    'message' => 'Add genre',
                    'subMessage' => 'Add Genre for Chosen Artists (<strong>' . count($request->input('ids')) . '</strong>)',
                    'action' => $request->input('action'),
                    'ids' => $request->input('ids'),
                ]);
        } elseif ($request->input('action') == 'save_add_genre') {
            $genres = explode(',', $request->input('genre'));

            Artist::query()
                ->whereIn('id', $request->input('ids'))
                ->chunk(100, function (Collection $artists) use ($genres) {
                    foreach ($artists as $artist) {
                        $artist->genres()->attach($genres);
                    }
                });

            return MessageHelper::redirectMessage('Artists successfully saved!', self::DEFAULT_ROUTE);
        } elseif ($request->input('action') == 'change_genre') {
            return view('backend.commons.mass_genre')
                ->with([
                    'message' => 'Change genre',
                    'subMessage' => 'Change Genre for Chosen Artists (<strong>' . count($request->input('ids')) . '</strong>)',
                    'action' => $request->input('action'),
                    'ids' => $request->input('ids'),
                ]);
        } elseif ($request->input('action') == 'save_change_genre') {
            $genres = explode(',', $request->input('genre'));

            Artist::query()
                ->whereIn('id', $request->input('ids'))
                ->chunk(100, function (Collection $artists) use ($genres) {
                    foreach ($artists as $artist) {
                        $artist->genres()->sync($genres);
                    }
                });

            return MessageHelper::redirectMessage('Artists successfully saved!', self::DEFAULT_ROUTE);
        } elseif ($request->input('action') == 'verified') {
            Artist::query()
                ->withoutGlobalScopes()
                ->whereIn('id', $request->input('ids'))
                ->update([
                    'verified' => DefaultConstants::TRUE,
                ]);

            return MessageHelper::redirectMessage('Artists successfully saved!', self::DEFAULT_ROUTE);
        } elseif ($request->input('action') == 'unverified') {
            Artist::query()
                ->withoutGlobalScopes()
                ->whereIn('id', $request->input('ids'))
                ->update([
                    'verified' => DefaultConstants::FALSE,
                ]);

            return MessageHelper::redirectMessage('Artists successfully saved!', self::DEFAULT_ROUTE);
        } elseif ($request->input('action') == 'comments') {
            Artist::query()
                ->withoutGlobalScopes()
                ->whereIn('id', $request->input('ids'))
                ->update([
                    'allow_comments' => DefaultConstants::TRUE,
                ]);

            return MessageHelper::redirectMessage('Artists successfully saved!', self::DEFAULT_ROUTE);
        } elseif ($request->input('action') == 'not_comments') {
            Artist::query()
                ->withoutGlobalScopes()
                ->whereIn('id', $request->input('ids'))
                ->update([
                    'allow_comments' => DefaultConstants::FALSE,
                ]);

            return MessageHelper::redirectMessage('Artists successfully saved!', self::DEFAULT_ROUTE);
        } elseif ($request->input('action') == 'delete') {
            Artist::query()
                ->withoutGlobalScopes()
                ->whereIn('id', $request->input('ids'))
                ->delete();

            return MessageHelper::redirectMessage('Artists successfully deleted!', self::DEFAULT_ROUTE);
        }
    }

    public function search(Request $request): JsonResponse
    {
        $result = Artist::query()
            ->where('name', 'LIKE', "%{$request->input('q')}%")
            ->paginate(20);

        return response()->json($result);
    }
}
