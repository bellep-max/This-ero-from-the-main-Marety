<?php

/**
 * Created by NiNaCoder.
 * Date: 2019-05-26
 * Time: 10:54.
 */

namespace App\Http\Controllers\Backend;

use App\Helpers\MessageHelper;
use App\Http\Requests\Backend\Region\RegionStoreRequest;
use App\Models\Region;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;

class RegionController
{
    private const DEFAULT_ROUTE = 'backend.regions.index';

    public function index(): View|Application|Factory
    {
        return view('backend.regions.index')
            ->with([
                'regions' => Region::query()->withoutGlobalScopes()->paginate(20),
            ]);
    }

    public function create(): View|Application|Factory
    {
        return view('backend.regions.create');
    }

    public function edit(Region $region): View|Application|Factory
    {
        return view('backend.regions.edit')
            ->with([
                'region' => $region,
            ]);
    }

    public function store(RegionStoreRequest $request): RedirectResponse
    {
        Region::create($request->all());

        return MessageHelper::redirectMessage('Region successfully added!', self::DEFAULT_ROUTE);
    }

    public function update(Region $region, RegionStoreRequest $request): RedirectResponse
    {
        $region->update($request->all());

        return MessageHelper::redirectMessage('Region successfully updated!', self::DEFAULT_ROUTE);
    }

    public function destroy(Region $region): RedirectResponse
    {
        $region->delete();

        return MessageHelper::redirectMessage('Region successfully deleted!', self::DEFAULT_ROUTE);
    }
}
