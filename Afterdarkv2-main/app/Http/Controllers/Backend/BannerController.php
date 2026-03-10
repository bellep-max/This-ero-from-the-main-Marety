<?php

/**
 * Created by NiNaCoder.
 * Date: 2019-05-25
 * Time: 09:01.
 */

namespace App\Http\Controllers\Backend;

use App\Helpers\MessageHelper;
use App\Http\Requests\Backend\Banner\BannerStoreRequest;
use App\Http\Requests\Backend\Banner\BannerUpdateRequest;
use App\Models\Banner;
use Cache;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;

class BannerController
{
    private const DEFAULT_ROUTE = 'backend.banners.index';

    public function index(): View|Application|Factory
    {
        return view('backend.banners.index')
            ->with([
                'banners' => Banner::query()->paginate(20),
            ]);
    }

    public function create(): View|Application|Factory
    {
        return view('backend.banners.create');
    }

    public function store(BannerStoreRequest $request): RedirectResponse
    {
        Banner::create($request->validated());

        Cache::forget('banners');

        return MessageHelper::redirectMessage('Banner successfully added!', self::DEFAULT_ROUTE);
    }

    public function edit(Banner $banner): View|Application|Factory
    {
        return view('backend.banners.edit')
            ->with([
                'banner' => $banner,
            ]);
    }

    public function update(Banner $banner, BannerUpdateRequest $request): RedirectResponse
    {
        $banner->update($request->validated());

        Cache::forget('banners');

        return MessageHelper::redirectMessage('Banner successfully edited!', self::DEFAULT_ROUTE);
    }

    public function delete(Banner $banner): RedirectResponse
    {
        $banner->delete();

        return MessageHelper::redirectMessage('Banners successfully deleted!');
    }

    public function disable(Banner $banner): RedirectResponse
    {
        $banner->update([
            'approved' => !$banner->approved,
        ]);

        Cache::forget('banners');

        return MessageHelper::redirectMessage('Banner successfully edited!', self::DEFAULT_ROUTE);
    }
}
