<?php

/**
 * Created by NiNaCoder.
 * Date: 2019-05-26
 * Time: 15:46.
 */

namespace App\Http\Controllers\Api;

use App\Models\Podcast;

class RssController
{
    public static function getFeedItems()
    {
        return Podcast::all();
    }
}
