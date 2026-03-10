<?php

/**
 * Created by NiNaCoder.
 * Date: 2019-05-26
 * Time: 15:46.
 */

namespace App\Http\Controllers\Api;

use App\Constants\ActionConstants;
use App\Constants\DefaultConstants;
use App\Models\Activity;
use App\Models\Channel;
use App\Models\Slide;
use Illuminate\Http\JsonResponse;
use stdClass;

class CommunityController
{
    public function index(): JsonResponse
    {
        $community = new stdClass;
        $community->activities = Activity::query()
            ->where('action', '!=', ActionConstants::ADD_EVENT)
            ->latest()
            ->paginate(20);

        $channels = Channel::query()
            ->where('allow_community', DefaultConstants::TRUE)
            ->orderBy('priority', 'asc')
            ->get();

        $slides = Slide::query()
            ->where('allow_community', DefaultConstants::TRUE)
            ->orderBy('priority', 'asc')
            ->get();

        return response()->json([
            'slides' => json_decode(json_encode($slides)),
            'channels' => json_decode(json_encode($channels)),
            'community' => $community,
        ]);
    }
}
