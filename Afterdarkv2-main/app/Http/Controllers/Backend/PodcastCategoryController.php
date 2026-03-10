<?php

/**
 * Created by NiNaCoder.
 * Date: 2019-05-26
 * Time: 10:54.
 */

namespace App\Http\Controllers\Backend;

use App\Helpers\MessageHelper;
use App\Http\Requests\Backend\PodcastCategory\PodcastCategorySortRequest;
use App\Http\Requests\Backend\PodcastCategory\PodcastCategoryStoreRequest;
use App\Http\Requests\Backend\PodcastCategory\PodcastCategoryUpdateRequest;
use App\Models\PodcastCategory;
use App\Services\ArtworkService;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;

class PodcastCategoryController
{
    private const DEFAULT_ROUTE = 'backend.podcast-categories.index';

    public function index(): View|Application|Factory
    {
        return view('backend.podcast-categories.index')
            ->with([
                'categories' => PodcastCategory::query()->orderBy('priority', 'asc')->get(),
            ]);
    }

    public function create(): View|Application|Factory
    {
        return view('backend.podcast-categories.create');
    }

    public function store(PodcastCategoryStoreRequest $request): RedirectResponse
    {
        $category = PodcastCategory::create($request->all());

        ArtworkService::updateArtwork($request, $category);

        return MessageHelper::redirectMessage('Podcast category successfully added!', self::DEFAULT_ROUTE);
    }

    public function edit(PodcastCategory $category): View|Application|Factory
    {
        return view('backend.podcast-categories.edit')
            ->with([
                'category' => $category,
            ]);
    }

    public function update(PodcastCategory $category, PodcastCategoryUpdateRequest $request): RedirectResponse
    {
        $category->update($request->all());

        if ($request->hasFile('artwork')) {
            ArtworkService::updateArtwork($request, $category);
        }

        $category->save();

        return MessageHelper::redirectMessage('Radio successfully updated!', self::DEFAULT_ROUTE);
    }

    public function destroy(PodcastCategory $category): RedirectResponse
    {
        $category->delete();

        return MessageHelper::redirectMessage('Podcast Category successfully deleted!', self::DEFAULT_ROUTE);
    }

    public function sort(PodcastCategorySortRequest $request): RedirectResponse
    {
        foreach ($request->input('categoryIds') as $index => $categoryId) {
            PodcastCategory::query()
                ->where('id', $categoryId)
                ->update([
                    'priority' => $index + 1,
                ]);
        }

        return MessageHelper::redirectMessage('Priority successfully changed!', self::DEFAULT_ROUTE);
    }
}
