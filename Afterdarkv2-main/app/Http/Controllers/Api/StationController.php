<?php

/**
 * Created by NiNaCoder.
 * Date: 2019-07-06
 * Time: 15:50.
 */

namespace App\Http\Controllers\Api;

use App\Models\Station;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StationController
{
    private $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function index(Station $station): JsonResponse
    {
        if ($this->request->get('callback')) {
            $station->artists = [['name' => __('web.LIVE')]];

            return response()
                ->jsonp($this->request->get('callback'), [$station])
                ->header('Content-Type', 'application/javascript');
        }

        return response()->json($station);
    }
}
