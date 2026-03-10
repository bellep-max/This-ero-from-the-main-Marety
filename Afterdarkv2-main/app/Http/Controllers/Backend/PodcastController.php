<?php

/**
 * Created by NiNaCoder.
 * Date: 2019-05-26
 * Time: 10:54.
 */

namespace App\Http\Controllers\Backend;

use App\Constants\DefaultConstants;
use App\Helpers\MessageHelper;
use App\Http\Requests\Backend\Podcast\PodcastIndexRequest;
use App\Http\Requests\Backend\Podcast\PodcastStoreRequest;
use App\Http\Requests\Backend\Podcast\PodcastUpdateRequest;
use App\Models\Podcast;
use App\Services\ArtworkService;
use Carbon\Carbon;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PodcastController
{
    private const DEFAULT_ROUTE = 'backend.podcasts.index';

    public function index(PodcastIndexRequest $request): View|Application|Factory
    {
        $podcasts = Podcast::query()
            ->withoutGlobalScopes()
            ->when($request->has('term'), function ($query) use ($request) {
                switch ($request->input('location')) {
                    case 0:
                        $query->search($request->input('term'));
                        break;
                    case 1:
                        $query->where('title', 'LIKE', '%' . $request->input('term') . '%');
                        break;
                    case 2:
                        $query->where('description', 'LIKE', '%' . $request->input('term') . '%');
                        break;
                }
            }, function ($query) use ($request) {
                $query->where('title', 'LIKE', '%' . $request->input('term') . '%');
            })
            ->when($request->input('userIds'), function ($query) use ($request) {
                $query->whereIn('user_id', $request->input('userIds'));
            })
            ->when($request->input('category'), function ($query) use ($request) {
                $query->where('category', 'REGEXP', '(^|,)(' . implode(',', $request->input('category')) . ')(,|$)');
            })
            ->when($request->input('created_from'), function ($query) use ($request) {
                $query->where('created_at', '>=', Carbon::parse($request->input('created_from')));
            })
            ->when($request->has('created_until'), function ($query) use ($request) {
                $query->where('created_at', '<=', Carbon::parse($request->input('created_until')));
            })
            ->when($request->input('comment_count_from'), function ($query) use ($request) {
                $query->where('comment_count', '>=', $request->input('comment_count_from'));
            })
            ->when($request->has('comment_count_until'), function ($query) use ($request) {
                $query->where('comment_count', '<=', $request->input('comment_count_until'));
            })
            ->when($request->has('fixed'), function ($query) {
                $query->where('fixed', DefaultConstants::TRUE);
            })
            ->when($request->has('comment_disabled'), function ($query) {
                $query->where('allow_comments', DefaultConstants::FALSE);
            })
            ->when($request->has('hidden'), function ($query) {
                $query->where('is_visible', DefaultConstants::FALSE);
            })
            ->when($request->has('country'), function ($query) use ($request) {
                $query->where('country_code', $request->input('country'));
            })
            ->when($request->has('city'), function ($query) use ($request) {
                $query->where('city_id', $request->input('city'));
            })
            ->when($request->has('language'), function ($query) use ($request) {
                $query->where('language_id', $request->input('language'));
            })
            ->when($request->has('title'), function ($query) use ($request) {
                $query->orderBy('title', $request->input('title'));
            })
            ->when($request->has('created_at'), function ($query) use ($request) {
                $query->orderBy('created_at', $request->input('created_at'));
            })
            ->with(['user:id,uuid,username'])
            ->withCount('episodes');

        return view('backend.podcasts.index')
            ->with([
                'podcasts' => $request->has('results_per_page')
                    ? $podcasts->paginate(intval($request->input('results_per_page')))
                    : $podcasts->paginate(20),
            ]);
    }

    public function create(): View|Application|Factory
    {
        return view('backend.podcasts.create');
    }

    public function edit(Podcast $podcast): View|Application|Factory
    {
        return view('backend.podcasts.edit')
            ->with([
                'podcast' => $podcast,
            ]);
    }

    public function store(PodcastStoreRequest $request): RedirectResponse
    {
        $podcast = Podcast::create($request->all());

        if ($request->hasFile('artwork')) {
            ArtworkService::updateArtwork($request, $podcast, request()->route()->getName() == 'backend.podcasts.edit.post');
        }

        if ($request->input('rss_feed_url')) {
            @libxml_use_internal_errors(true);
            $rss = @simplexml_load_file($request->input('rss_feed_url'));

            if ($rss === false) {
                return redirect()
                    ->back()
                    ->with([
                        'status' => 'failed',
                        'message' => 'Can not fetch the rss.',
                    ]);
            }

            if (isset($rss->channel)) {
                $itunes = $rss->channel->children('http://www.itunes.com/dtds/podcast-1.0.dtd');
                $podcast->addMediaFromUrl(reset($rss->channel->image->url) ? reset($rss->channel->image->url) : reset($itunes->image->attributes()->href))
                    ->usingFileName(time() . '.jpg')
                    ->toMediaCollection('artwork', config('settings.storage_artwork_location', 'public'));
                $podcast->created_at = Carbon::parse($rss->channel->pubDate);
                $podcast->updated_at = Carbon::parse($rss->channel->lastBuildDate);
                $podcast->save();
            } else {
                return redirect()
                    ->back()
                    ->with([
                        'status' => 'failed',
                        'message' => 'RSS format does not match a podcast feed.',
                    ]);
            }

            if (isset($rss->channel->item)) {
                foreach ($rss->channel->item as $item) {
                    if (!$podcast->episodes()->where('created_at', Carbon::parse($item->pubDate))->exists()) {
                        $itunes = $item->children('http://www.itunes.com/dtds/podcast-1.0.dtd');

                        $podcast->episodes()->create([
                            'title' => $item->title,
                            'description' => $item->description,
                            'type' => 1,
                            'stream_url' => $item->enclosure['url'],
                            'duration' => intval(reset($itunes->duration)),
                            'explicit' => (reset($itunes->explicit) == 'clean' || reset($itunes->explicit) == 'no')
                                ? DefaultConstants::FALSE
                                : DefaultConstants::TRUE,
                            'created_at' => Carbon::parse($item->pubDate),
                        ]);
                    }
                }
            }
        }

        return MessageHelper::redirectMessage('Podcast successfully edited!', self::DEFAULT_ROUTE);
    }

    public function update(Podcast $podcast, PodcastUpdateRequest $request): RedirectResponse
    {
        $podcast->update($request->all());

        if ($request->hasFile('artwork')) {
            ArtworkService::updateArtwork($request, $podcast, request()->route()->getName() == 'backend.podcasts.edit.post');
        }

        if ($request->input('rss_feed_url')) {
            @libxml_use_internal_errors(true);
            $rss = @simplexml_load_file($request->input('rss_feed_url'));

            if ($rss === false) {
                return redirect()
                    ->back()
                    ->with([
                        'status' => 'failed',
                        'message' => 'Can not fetch the rss.',
                    ]);
            }

            if (isset($rss->channel)) {
                $itunes = $rss->channel->children('http://www.itunes.com/dtds/podcast-1.0.dtd');
                $podcast->addMediaFromUrl(reset($rss->channel->image->url) ? reset($rss->channel->image->url) : reset($itunes->image->attributes()->href))
                    ->usingFileName(time() . '.jpg')
                    ->toMediaCollection('artwork', config('settings.storage_artwork_location', 'public'));
                $podcast->created_at = Carbon::parse($rss->channel->pubDate);
                $podcast->updated_at = Carbon::parse($rss->channel->lastBuildDate);
                $podcast->save();
            } else {
                return redirect()
                    ->back()
                    ->with([
                        'status' => 'failed',
                        'message' => 'RSS format does not match a podcast feed.',
                    ]);
            }

            if (isset($rss->channel->item)) {
                foreach ($rss->channel->item as $item) {
                    if (!$podcast->episodes()->where('created_at', Carbon::parse($item->pubDate))->exists()) {
                        $itunes = $item->children('http://www.itunes.com/dtds/podcast-1.0.dtd');

                        $podcast->episodes()->create([
                            'title' => $item->title,
                            'description' => $item->description,
                            'type' => 1,
                            'stream_url' => $item->enclosure['url'],
                            'duration' => intval(reset($itunes->duration)),
                            'explicit' => (reset($itunes->explicit) == 'clean' || reset($itunes->explicit) == 'no')
                                ? DefaultConstants::FALSE
                                : DefaultConstants::TRUE,
                            'created_at' => Carbon::parse($item->pubDate),
                        ]);
                    }
                }
            }
        }

        return MessageHelper::redirectMessage('Podcast successfully edited!', self::DEFAULT_ROUTE);
    }

    public function destroy(Podcast $podcast): RedirectResponse
    {
        $podcast->delete();

        return MessageHelper::redirectMessage('Podcast successfully deleted!', self::DEFAULT_ROUTE);
    }

    public function search(Request $request): JsonResponse
    {
        $result = Podcast::query()
            ->where('title', 'LIKE', "%{$request->input('q')}%")
            ->paginate(20);

        return response()->json($result);
    }
}
