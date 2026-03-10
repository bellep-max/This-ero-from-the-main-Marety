<?php

/**
 * Created by NiNaCoder.
 * Date: 2019-05-24
 * Time: 13:08.
 */

namespace App\Http\Controllers\Api;

use App\Constants\DefaultConstants;
use App\Models\Channel;
use App\Models\Genre;
use App\Models\Slide;
use Illuminate\Http\JsonResponse;

class DiscoverController
{
    public function index(): JsonResponse
    {
        $discover = (object) [];

        $discover->channels = Channel::query()
            ->where('allow_discover', DefaultConstants::TRUE)
            ->orderBy('priority')
            ->get();

        $discover->slides = Slide::query()
            ->where('allow_discover', DefaultConstants::TRUE)
            ->orderBy('priority')
            ->get();

        $discover->genres = Genre::query()
            ->orderBy('priority')
            ->discover()
            ->get();

        return response()->json(json_decode(json_encode($discover)));
    }
}
