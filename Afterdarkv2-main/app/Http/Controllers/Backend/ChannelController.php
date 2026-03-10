<?php

/**
 * Created by NiNaCoder.
 * Date: 2019-05-25
 * Time: 20:58.
 */

namespace App\Http\Controllers\Backend;

use App\Constants\DefaultConstants;
use App\Helpers\MessageHelper;
use App\Http\Requests\Backend\Channel\ChannelSortRequest;
use App\Http\Requests\Backend\Channel\ChannelStoreRequest;
use App\Http\Requests\Backend\Channel\ChannelUpdateRequest;
use App\Models\Channel;
use App\Models\Genre;
use App\Models\PodcastCategory;
use App\Models\RadioCategory;
use Cache;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ChannelController
{
    private const DEFAULT_ROUTE = 'backend.channels.index';

    public function index(Request $request): View|Application|Factory
    {
        $channels = Channel::query()
            ->when($request->route()->getName() == 'backend.channels.home', function ($query) {
                $query->where('allow_home', DefaultConstants::TRUE);
            })
            ->when($request->route()->getName() == 'backend.channels.discover', function ($query) {
                $query->where('allow_discover', DefaultConstants::TRUE);
            })
            ->when($request->route()->getName() == 'backend.channels.radio', function ($query) {
                $query->where('allow_radio', DefaultConstants::TRUE);
            })
            ->when($request->route()->getName() == 'backend.channels.community', function ($query) {
                $query->where('allow_community', DefaultConstants::TRUE);
            })
            ->when($request->route()->getName() == 'backend.channels.trending', function ($query) {
                $query->where('allow_trending', DefaultConstants::TRUE);
            })
            ->when($request->route()->getName() == 'backend.channels.genre', function ($query) use ($request) {
                $query->whereRaw("genre REGEXP '(^|,)(" . $request->route('id') . ")(,|$)'");
            })
            ->when($request->route()->getName() == 'backend.channels.station-category', function ($query) use ($request) {
                $query->whereRaw("radio REGEXP '(^|,)(" . $request->route('id') . ")(,|$)'");
            })
            ->when($request->route()->getName() == 'backend.channels.podcast-category', function ($query) use ($request) {
                $query->whereRaw("podcast REGEXP '(^|,)(" . $request->route('id') . ")(,|$)'");
            })
            ->with('user')
            ->orderBy('priority', 'asc')
            ->get();

        return view('backend.channels.index')
            ->with([
                'channels' => $channels,
                'genres' => Genre::all(),
                'radio_categories' => RadioCategory::all(),
                'podcast_categories' => PodcastCategory::all(),
            ]);
    }

    public function create(): View|Application|Factory
    {
        return view('backend.channels.create');
    }

    public function store(ChannelStoreRequest $request): RedirectResponse
    {
        $channel = Channel::create($request->all());

        if ($request->filled('genre')) {
            $channel->genres()->sync($request->input('genre'));
        }

        if ($request->filled('podcast_categories')) {
            $channel->podcastCategories()->sync($request->input('podcast_categories'));
        }

        if ($request->filled('radio_categories')) {
            $channel->radioCategories()->sync($request->input('radio_categories'));
        }

        /*
         * Clear homage cache
         */
        Cache::clear('homepage');

        return MessageHelper::redirectMessage('Channel successfully added!', self::DEFAULT_ROUTE);
    }

    public function edit(Channel $channel): View|Application|Factory
    {
        return view('backend.channels.edit')
            ->with([
                'channel' => $channel,
            ]);
    }

    public function update(Channel $channel, ChannelUpdateRequest $request): RedirectResponse
    {
        $channel->update($request->all());

        if ($request->filled('genre')) {
            $channel->genres()->sync($request->input('genre'));
        }

        if ($request->filled('object_ids')) {
            switch ($request->input('object_type')) {
                case 'podcast':
                    $channel->podcasts()->sync($request->input('object_ids'));
                    break;
                case 'song':
                    $channel->songs()->sync($request->input('object_ids'));
                    break;
                case 'album':
                    $channel->albums()->sync($request->input('object_ids'));
                    break;
                case 'artist':
                    $channel->artists()->sync($request->input('object_ids'));
                    break;
                case 'playlist':
                    $channel->playlists()->sync($request->input('object_ids'));
                    break;
                case 'station':
                    $channel->stations()->sync($request->input('object_ids'));
                    break;
                case 'user':
                    $channel->users()->sync($request->input('object_ids'));
                    break;
            }
        }

        if ($request->filled('podcast_categories')) {
            $channel->podcastCategories()->sync($request->input('podcast_categories'));
        }

        if ($request->filled('radio_categories')) {
            $channel->radioCategories()->sync($request->input('radio_categories'));
        }

        /*
         * Clear homage cache
         */
        Cache::clear('homepage');

        return MessageHelper::redirectMessage('Channel successfully updated!', self::DEFAULT_ROUTE);
    }

    public function destroy(Channel $channel): RedirectResponse
    {
        $channel->delete();

        Cache::clear('homepage');

        return MessageHelper::redirectMessage('Channel successfully deleted!', self::DEFAULT_ROUTE);
    }

    public function sort(ChannelSortRequest $request): RedirectResponse
    {
        foreach ($request->input('object_ids') as $index => $objectId) {
            Channel::query()
                ->where('id', $objectId)
                ->update([
                    'priority' => $index + 1,
                ]);
        }

        return MessageHelper::redirectMessage('Priority successfully changed!', self::DEFAULT_ROUTE);
    }
}
