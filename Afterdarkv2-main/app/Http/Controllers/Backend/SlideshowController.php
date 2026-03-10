<?php

namespace App\Http\Controllers\Backend;

use App\Constants\DefaultConstants;
use App\Helpers\MessageHelper;
use App\Http\Requests\Backend\Slide\SlideSortRequest;
use App\Http\Requests\Backend\Slide\SlideStoreRequest;
use App\Http\Requests\Backend\Slide\SlideUpdateRequest;
use App\Models\Genre;
use App\Models\PodcastCategory;
use App\Models\RadioCategory;
use App\Models\Slide;
use App\Services\ArtworkService;
use Cache;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Intervention\Image\Laravel\Facades\Image;

class SlideshowController
{
    private const DEFAULT_ROUTE = 'backend.slideshow.index';

    public function index(Request $request): View|Application|Factory
    {
        $slides = Slide::query()
            ->withoutGlobalScopes()
            ->when($request->route()->getName() == 'backend.slideshow.home', function ($query) {
                $query->where('allow_home', DefaultConstants::TRUE);
            })
            ->when($request->route()->getName() == 'backend.slideshow.discover', function ($query) {
                $query->where('allow_discover', DefaultConstants::TRUE);
            })
            ->when($request->route()->getName() == 'backend.slideshow.radio', function ($query) {
                $query->where('allow_radio', DefaultConstants::TRUE);
            })
            ->when($request->route()->getName() == 'backend.slideshow.community', function ($query) {
                $query->where('allow_community', DefaultConstants::TRUE);
            })
            ->when($request->route()->getName() == 'backend.slideshow.trending', function ($query) {
                $query->where('allow_trending', DefaultConstants::TRUE);
            })
            ->when($request->route()->getName() == 'backend.slideshow.genre', function ($query) use ($request) {
                $query->whereRaw("genre REGEXP '(^|,)(" . $request->route('id') . ")(,|$)'");
            })
            ->when($request->route()->getName() == 'backend.slideshow.station-category', function ($query) use ($request) {
                $query->whereRaw("radio REGEXP '(^|,)(" . $request->route('id') . ")(,|$)'");
            })
            ->when($request->route()->getName() == 'backend.slideshow.podcast-category', function ($query) use ($request) {
                $query->whereRaw("podcast REGEXP '(^|,)(" . $request->route('id') . ")(,|$)'");
            })
            ->with('user')
            ->orderBy('priority', 'asc')
            ->get();

        Cache::clear('homepage');

        return view('backend.slideshow.index')
            ->with([
                'slides' => $slides,
                'genres' => Genre::query()->discover()->get(),
                'radio' => RadioCategory::all(),
                'podcast' => PodcastCategory::all(),
            ]);
    }

    public function create(): View|Application|Factory
    {
        return view('backend.slideshow.create');
    }

    public function store(SlideStoreRequest $request): RedirectResponse
    {
        $slide = Slide::create($request->all());

        if ($request->filled('genre')) {
            $slide->genres()->sync($request->input('genre'));
        }

        if ($request->filled('podcast')) {
            $slide->podcasts()->sync($request->input('podcast'));
        }

        $slide->addMediaFromBase64(
            base64_encode(
                Image::read($request->file('artwork'))
                    ->coverDown(config('settings.image_artwork_max'), intval(500 * 0.5625))
                    ->toJpeg(config('settings.image_jpeg_quality'))
            )
        )
            ->usingFileName(time() . '.jpg')
            ->toMediaCollection('artwork', config('settings.storage_artwork_location', 'public'));

        /*
         * Clear homage cache
         */
        Cache::clear('homepage');

        return MessageHelper::redirectMessage('Slide successfully added!', self::DEFAULT_ROUTE);
    }

    public function edit(Slide $slide): View|Application|Factory
    {
        $slide->load('object');

        return view('backend.slideshow.edit')
            ->with([
                'slide' => $slide,
                'slideGenres' => $slide->genres()->pluck('genres.id')->toArray(),
                'object' => $slide->object,
            ]);
    }

    public function update(Slide $slide, SlideUpdateRequest $request): RedirectResponse
    {
        $slide->update($request->all());

        if ($request->hasFile('artwork')) {
            ArtworkService::updateArtwork($request, $slide);
        }

        if ($request->filled('genre')) {
            $slide->genres()->sync($request->input('genre'));
        }

        if ($request->filled('podcast')) {
            $slide->podcasts()->sync($request->input('podcast'));
        }

        /*
         * Clear homage cache
         */
        Cache::clear('homepage');

        return MessageHelper::redirectMessage('Slide successfully updated!', self::DEFAULT_ROUTE);
    }

    public function destroy(Slide $slide): RedirectResponse
    {
        $slide->delete();

        return MessageHelper::redirectMessage('Slideshow successfully deleted!', self::DEFAULT_ROUTE);
    }

    public function sort(SlideSortRequest $request): RedirectResponse
    {
        foreach ($request->input('slideshowIds') as $priority => $slideshowId) {
            Slide::query()
                ->where('id', $slideshowId)
                ->update([
                    'priority' => $priority + 1,
                ]);
        }

        return MessageHelper::redirectMessage('Priority successfully changed!', self::DEFAULT_ROUTE);
    }
}
