<?php

/**
 * Created by NiNaCoder.
 * Date: 2019-07-21
 * Time: 13:17.
 */

namespace App\Http\Controllers\Backend;

use App\Helpers\MessageHelper;
use App\Http\Requests\Backend\Metatag\MetatagSortRequest;
use App\Http\Requests\Backend\Metatag\MetatagStoreRequest;
use App\Http\Requests\Backend\Metatag\MetatagUpdateRequest;
use App\Models\Meta;
use App\Services\ArtworkService;
use App\Services\MetatagService;
use Cache;
use Illuminate\Http\RedirectResponse;

class MetaTagController
{
    private MetatagService $metaTagService;

    private const DEFAULT_ROUTE = 'backend.metatags.index';

    public function __construct(MetatagService $metatagService)
    {
        $this->metaTagService = $metatagService;
    }

    public function index()
    {
        return view('backend.metatags.index')
            ->with([
                'metatags' => Meta::query()->orderBy('priority', 'asc')->get(),
            ]);
    }

    public function edit(Meta $metaTag)
    {
        $this->metaTagService->setMetatags($metaTag);

        return view('backend.metatags.edit')
            ->with([
                'metatag' => $metaTag,
            ]);
    }

    public function store(MetatagStoreRequest $request): RedirectResponse
    {
        $metatag = Meta::create($request->all());

        if ($request->hasFile('artwork')) {
            ArtworkService::updateArtwork($request, $metatag);
        }

        Cache::forget('metatags');

        return MessageHelper::redirectMessage('Meta tag successfully added!', self::DEFAULT_ROUTE);
    }

    public function update(Meta $metaTag, MetatagUpdateRequest $request): RedirectResponse
    {
        $metaTag->update($request->all());

        if ($request->hasFile('artwork')) {
            ArtworkService::updateArtwork($request, $metaTag);
        }

        Cache::forget('metatags');

        return MessageHelper::redirectMessage('Meta tag successfully updated!', self::DEFAULT_ROUTE);
    }

    public function destroy(Meta $metaTag): RedirectResponse
    {
        $metaTag->delete();

        Cache::forget('metatags');

        return MessageHelper::redirectMessage('Meta tag successfully deleted!', self::DEFAULT_ROUTE);
    }

    public function sort(MetatagSortRequest $request): RedirectResponse
    {
        foreach ($request->input('metaIds') as $index => $metaId) {
            Meta::query()
                ->where('id', $metaId)
                ->update([
                    'priority' => $index + 1,
                ]);
        }

        Cache::forget('metatags');

        return MessageHelper::redirectMessage('Priority successfully changed!', self::DEFAULT_ROUTE);
    }
}
