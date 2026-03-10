<?php

/**
 * Created by NiNaCoder.
 * Date: 2019-05-26
 * Time: 10:54.
 */

namespace App\Http\Controllers\Backend;

use App\Constants\ActionConstants;
use App\Helpers\MessageHelper;
use App\Http\Requests\Backend\PodcastEpisode\PodcastEpisodeMassActionRequest;
use App\Models\Episode;
use App\Models\Podcast;
use Illuminate\Http\RedirectResponse;

class PodcastEpisodeController
{
    public function show(Podcast $podcast)
    {
        $podcast->setRelation('episodes', $podcast->episodes()->withoutGlobalScopes()->paginate(20));

        return view('backend.podcasts.episodes')
            ->with([
                'podcast' => $podcast,
            ]);
    }

    public function upload(Podcast $podcast)
    {
        return view('backend.podcasts.upload')
            ->with([
                'podcast' => $podcast,
            ]);
    }

    public function batch(PodcastEpisodeMassActionRequest $request): RedirectResponse
    {
        if ($request->input('action') == ActionConstants::REMOVE_PODCAST_EPISODE) {
            Episode::query()
                ->withoutGlobalScopes()
                ->whereIn('id', $request->input('ids'))
                ->delete();

            return MessageHelper::redirectMessage('Episodes successfully deleted!');
        }
    }
}
