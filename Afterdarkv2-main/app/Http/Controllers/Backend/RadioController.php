<?php

/**
 * Created by NiNaCoder.
 * Date: 2019-05-26
 * Time: 10:54.
 */

namespace App\Http\Controllers\Backend;

use App\Helpers\MessageHelper;
use App\Http\Requests\Backend\Radio\RadioSortRequest;
use App\Http\Requests\Backend\Radio\RadioStoreRequest;
use App\Http\Requests\Backend\Radio\RadioUpdateRequest;
use App\Models\RadioCategory;
use App\Services\ArtworkService;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Intervention\Image\Laravel\Facades\Image;

class RadioController
{
    private const DEFAULT_ROUTE = 'backend.radios.index';

    public function index(): View|Application|Factory
    {
        return view('backend.radios.index')
            ->with([
                'radio_categories' => RadioCategory::query()->orderBy('priority', 'asc')->get(),
            ]);
    }

    public function create(): View|Application|Factory
    {
        return view('backend.radios.create');
    }

    public function store(RadioStoreRequest $request): RedirectResponse
    {
        $radio = RadioCategory::create($request->all());

        $radio->addMediaFromBase64(
            base64_encode(
                Image::read($request->file('artwork'))
                    ->coverDown(config('settings.image_artwork_max'), intval(500 * 0.5625))
                    ->toJpeg(config('settings.image_jpeg_quality'))
            )
        )->usingFileName(time() . '.jpg')
            ->toMediaCollection('artwork', config('settings.storage_artwork_location', 'public'));

        return MessageHelper::redirectMessage('Radio category successfully added!', self::DEFAULT_ROUTE);
    }

    public function edit(RadioCategory $radio): View|Application|Factory
    {
        return view('backend.radios.edit')
            ->with([
                'radio' => $radio,
            ]);
    }

    public function update(RadioCategory $radio, RadioUpdateRequest $request): RedirectResponse
    {
        $radio->update($request->all());

        if ($request->hasFile('artwork')) {
            ArtworkService::updateArtwork($request, $radio);
        }

        return MessageHelper::redirectMessage('Radio successfully updated!', self::DEFAULT_ROUTE);
    }

    public function destroy(RadioCategory $radio): RedirectResponse
    {
        $radio->delete();

        return MessageHelper::redirectMessage('Radio category successfully deleted!', self::DEFAULT_ROUTE);
    }

    public function sort(RadioSortRequest $request): RedirectResponse
    {
        foreach ($request->input('radioIds') as $index => $radioId) {
            RadioCategory::query()
                ->where('id', $radioId)
                ->update([
                    'priority' => $index + 1,
                ]);
        }

        return MessageHelper::redirectMessage('Priority successfully changed!', self::DEFAULT_ROUTE);
    }
}
