<?php

namespace App\Http\Controllers\Backend;

use App\Helpers\MessageHelper;
use App\Http\Requests\Backend\Genre\GenreIndexRequest;
use App\Http\Requests\Backend\Genre\GenreSortRequest;
use App\Http\Requests\Backend\Genre\GenreStoreRequest;
use App\Http\Requests\Backend\Genre\GenreUpdateRequest;
use App\Models\Genre;
use App\Services\ArtworkService;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;

class GenreController
{
    private const DEFAULT_ROUTE = 'backend.genres.index';

    public function index(GenreIndexRequest $request): Factory|Application|View
    {
        $genres = Genre::query()
            ->when($request->has('term'), function ($query) use ($request) {
                $query->where('name', 'like', '%' . $request->input('term') . '%');
            })
            ->orderBy('discover', 'desc')
            ->orderBy('priority', 'asc')
            ->paginate(50);

        return view('backend.genres.index')
            ->with([
                'genres' => $genres,
            ]);
    }

    public function create(): Factory|Application|View
    {
        return view('backend.genres.create');
    }

    public function store(GenreStoreRequest $request): RedirectResponse
    {
        $genre = Genre::create($request->all());

        ArtworkService::updateArtwork($request, $genre);

        return MessageHelper::redirectMessage('Genre successfully added!', self::DEFAULT_ROUTE);
    }

    public function edit(Genre $genre): Factory|Application|View
    {
        return view('backend.genres.edit')
            ->with([
                'genre' => $genre,
            ]);
    }

    public function update(Genre $genre, GenreUpdateRequest $request): RedirectResponse
    {
        $genre->update($request->all());

        if ($request->hasFile('artwork')) {
            ArtworkService::updateArtwork($request, $genre);
        }

        return MessageHelper::redirectMessage('Genre successfully updated!', self::DEFAULT_ROUTE);
    }

    public function destroy(Genre $genre): RedirectResponse
    {
        $genre->delete();

        return MessageHelper::redirectMessage('Genre successfully deleted!', self::DEFAULT_ROUTE);
    }

    public function sort(GenreSortRequest $request): RedirectResponse
    {
        foreach ($request->input('genreIds') as $index => $genreId) {
            Genre::query()
                ->where('id', $genreId)
                ->update([
                    'priority' => $index + 1,
                ]);
        }

        return MessageHelper::redirectMessage('Priority successfully changed!', self::DEFAULT_ROUTE);
    }
}
