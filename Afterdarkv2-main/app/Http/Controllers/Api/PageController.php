<?php

/**
 * Created by NiNaCoder.
 * Date: 2019-06-19
 * Time: 23:42.
 */

namespace App\Http\Controllers\Api;

use App\Models\Page;
use Illuminate\Http\JsonResponse;

class PageController
{
    public function show(Page $page): JsonResponse
    {
        return response()->json($page);
    }
}
