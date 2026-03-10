<?php

namespace App\Http\Controllers\Frontend\Podcast;

use App\Constants\DefaultConstants;
use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\Podcast\PodcastUpdateRequest;
use App\Http\Requests\Frontend\Podcast\PodcastSearchRequest;
use App\Http\Requests\Frontend\Podcast\PodcastStoreRequest;
use App\Http\Resources\OptionResource;
use App\Http\Resources\Podcast\PodcastEditResource;
use App\Http\Resources\Podcast\PodcastShortResource;
use App\Models\Channel;
use App\Models\Country;
use App\Models\CountryLanguage;
use App\Models\Podcast;
use App\Models\PodcastCategory;
use App\Models\Region;
use App\Models\Slide;
use App\Services\PodcastService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;
use JetBrains\PhpStorm\NoReturn;

class PodcastController extends Controller
{
    private const TAKE_AMOUNT = 20;

    public function __construct(private readonly PodcastService $podcastService) {}

    public function index(PodcastSearchRequest $request): Response
    {
        $podcasts = Podcast::query()
            ->when($request->filled('categories'), function ($query) use ($request) {
                $query->whereIn('category', $request->input('categories'));
            })
            ->when($request->filled('languages'), function ($query) use ($request) {
                $query->whereIn('language_id', $request->input('languages'));
            })
            ->when($request->filled('countries'), function ($query) use ($request) {
                $query->whereHas('country', function ($query) use ($request) {
                    $query->whereIn('id', $request->input('countries'));
                });
            })
            ->when($request->filled('tags'), function ($query) use ($request) {
                $query->whereHas('tags', function ($query) use ($request) {
                    $query->whereIn('tags.id', $request->input('tags'));
                });
            })
            ->has('episodes')
            ->where(function ($query) {
                $query->where('is_visible', DefaultConstants::TRUE)
                    ->when(auth()->check(), function ($query) {
                        $query->orWhere('user_id', auth()->id());
                    });
            })
            ->withMax('episodes', 'season')
            ->withCount('episodes')
            ->paginate(self::TAKE_AMOUNT);

        return Inertia::render('Podcast/Index', [
            'podcasts' => PodcastShortResource::collection($podcasts),
            'regions' => OptionResource::collection(Region::all()),
            'channels' => Channel::query()
                ->where('allow_podcasts', DefaultConstants::TRUE)
                ->orderBy('priority', 'asc')
                ->get(),
            'slides' => Slide::query()
                ->where('allow_podcasts', DefaultConstants::TRUE)
                ->orderBy('priority', 'asc')
                ->get(),
            'filters' => $request->validated(),
        ]);
    }

    public function store(PodcastStoreRequest $request): RedirectResponse
    {
        $podcast = $this->podcastService->store($request);

        if (!$podcast) {
            session()->flash('message', [
                'level' => 'error',
                'content' => 'Failed to create podcast.',
            ]);

            return redirect()->back(303);
        }

        session()->flash('message', [
            'level' => 'success',
            'content' => 'Podcast created successfully.',
        ]);

        return redirect()->back(303);
    }

    public function show(Podcast $podcast): Response
    {
        Gate::authorize('show', $podcast);

        $podcast->loadMissing([
            'user',
        ]);

        //        $podcast->setRelation('episodes', $podcast->episodes()->with('podcast')->paginate(20));
        //        $podcast->loadCount('episodes');
        //
        //        if (isset($podcast->artist)) {
        //            $artist = $podcast->artist;
        //            $artist->setRelation('similar', $artist->similar()->paginate(5));
        //        }

        return Inertia::render('Podcast/Show', [
            'podcast' => PodcastShortResource::make($podcast),

            //            'categories' => OptionResource::collection(PodcastCategory::query()
            //                ->orderBy('priority', 'asc')
            //                ->get()),
            //            'languages' => OptionResource::collection(CountryLanguage::query()
            //                ->fixed()
            //                ->get()),
            //            'countries' => OptionResource::collection(Country::query()
            //                ->fixed()
            //                ->get()),
            //            'regions' => OptionResource::collection(Region::all()),
            //            'channels' => Channel::query()
            //                ->where('allow_podcasts', DefaultConstants::TRUE)
            //                ->orderBy('priority', 'asc')
            //                ->get(),
            //            'slides' => Slide::query()
            //                ->where('allow_podcasts', DefaultConstants::TRUE)
            //                ->orderBy('priority', 'asc')
            //                ->get(),
            //            'filters' => $request->validated(),
        ]);
    }

    public function edit(Podcast $podcast): Response
    {
        Gate::authorize('edit', $podcast);

        $podcast->loadMissing([
            'user',
            'tags',
            'categories',
        ]);

        //        $podcast->setRelation('episodes', $podcast->episodes()->with('podcast')->paginate(20));
        //        $podcast->loadCount('episodes');
        //
        //        if (isset($podcast->artist)) {
        //            $artist = $podcast->artist;
        //            $artist->setRelation('similar', $artist->similar()->paginate(5));
        //        }

        return Inertia::render('Podcast/Edit', [
            'podcast' => PodcastEditResource::make($podcast),
        ]);
    }

    #[NoReturn]
    public function update(Podcast $podcast, PodcastUpdateRequest $request): RedirectResponse
    {
        $this->podcastService->update($request, $podcast);

        session()->flash('message', [
            'level' => 'success',
            'content' => 'Podcast updated successfully.',
        ]);

        return to_route('podcasts.show', $podcast);
    }
}
