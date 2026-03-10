<?php

/**
 * Created by NiNaCoder.
 * Date: 2019-06-18
 * Time: 24:20.
 */

namespace App\Http\Controllers\Frontend;

use App\Constants\CacheKeyConstants;
use App\Constants\DefaultConstants;
use App\Constants\DurationConstants;
use App\Contracts\TrendingInterface;
use App\Http\Controllers\Controller;
use App\Models\Channel;
use App\Models\Genre;
use App\Models\Slide;
use App\Models\Song;
use App\Services\AjaxViewService;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use Inertia\Inertia;
use Inertia\Response;

class TrendingController extends Controller
{
    private Request $request;

    private int $range;

    private AjaxViewService $ajaxViewService;

    public function __construct(Request $request, AjaxViewService $ajaxViewService)
    {
        $this->request = $request;
        $this->range = (int) Route::currentRouteName();
        $this->ajaxViewService = $ajaxViewService;

        $this->range = match ($this->range == 'frontend.trending') {
            'frontend.trending.week' => DurationConstants::WEEK,
            'frontend.trending.month' => DurationConstants::MONTH,
            default => DurationConstants::NONE,
        };
    }

    public function index(TrendingInterface $trending): Response
    {
        $genres = Genre::all();

        return Inertia::render('Trending', [
            'popularAudios' => $trending->popularAudios($genres),
            'topGenre' => $trending->topByGenres($genres),
            'topFemale' => $trending->topByVoice(array_search('[F] female', __('web.GENDER_TAGS')), 20),
            'topMale' => $trending->topByVoice(array_search('[M] male', __('web.GENDER_TAGS')), 20),
        ]);
    }

    public function topByGenre(Genre $genre, TrendingInterface $trending): \Illuminate\Contracts\View\View|string|null
    {
        $songs = $trending->topByGenrePaginate($genre->id, 20);

        $view = View::make('trending.songs')
            ->with([
                'songs' => $songs,
                'pageData' => [
                    'title' => $genre->name,
                    'desc' => 'Top by Genre',
                ],
            ]);

        return $this->request->ajax()
            ? $this->ajaxViewService->getRenderedSections($view, $this->request, $this->ajaxViewService::FULL_CHECK)
            : $view;
    }

    public function topSongs(): \Illuminate\Contracts\View\View|string|null
    {
        $songs = Song::query()
            ->orderBy('plays', 'desc')
            ->simplePaginate(20);

        $view = View::make('trending.songs')
            ->with([
                'songs' => $songs,
                'pageData' => [
                    'title' => 'Songs',
                    'desc' => 'Top songs',
                ],
            ]);

        return $this->request->ajax()
            ? $this->ajaxViewService->getRenderedSections($view, $this->request, $this->ajaxViewService::FULL_CHECK)
            : $view;
    }

    public function topVoice(string $voice, TrendingInterface $trending): \Illuminate\Contracts\View\View|string|null
    {
        $songs = $trending->topByVoicePaginate($voice, 20);

        $view = View::make('trending.songs')
            ->with([
                'songs' => $songs,
                'pageData' => [
                    'title' => 'Voices',
                    'desc' => 'Top voices',
                ],
            ]);

        return $this->request->ajax()
            ? $this->ajaxViewService->getRenderedSections($view, $this->request, $this->ajaxViewService::FULL_CHECK)
            : $view;
    }

    //    public function index()
    //    {
    //        $trending = (object) [];
    //
    //        switch (Route::currentRouteName()) {
    //            case 'frontend.trending.week':
    //                $startDate = Carbon::now()->subWeeks(2);
    //                $endDate = Carbon::now()->subWeek();
    //                $cacheKey = CacheKeyConstants::TRENDING_WEEK;
    //                break;
    //            case 'frontend.trending.month':
    //                $startDate = Carbon::now()->subMonths(2);
    //                $endDate = Carbon::now()->subMonth();
    //                $cacheKey = CacheKeyConstants::TRENDING_MONTH;
    //                break;
    //            default:
    //                $startDate = Carbon::now()->subDays(2);
    //                $endDate = Carbon::now()->subDay();
    //                $cacheKey = CacheKeyConstants::TRENDING_DAY;
    //        }
    //
    //        if (Cache::has($cacheKey)) {
    //            $trending->songs = Cache::get($cacheKey);
    //        } else {
    //            $lastTimeToCompare = $this->songs($startDate, $endDate)->toArray();
    //            $lastTimeToCompare = $lastTimeToCompare['data'];
    //
    //            $present = $this->songs($startDate, Carbon::now());
    //
    //            $trending->songs = $present->map(function ($row) use ($lastTimeToCompare) {
    //                if (isset($row->id)) {
    //                    $song = $row;
    //                    $song->last_postion = array_search($row->id, array_column($lastTimeToCompare, 'id'));
    //
    //                    return $song;
    //                }
    //            });
    //
    //            Cache::put($cacheKey, $trending->songs, now()->addDay());
    //        }
    //
    //        $trending->channels = Channel::query()
    //            ->where('allow_trending', DefaultConstants::TRUE)
    //            ->orderBy('priority', 'asc')
    //            ->get();
    //        $trending->slides = Slide::query()
    //            ->where('allow_trending', DefaultConstants::TRUE)
    //            ->orderBy('priority', 'asc')
    //            ->get();
    //
    //        $trending = json_decode(json_encode($trending));
    //
    //        if ($this->request->is('api*')) {
    //            return response()->json($trending);
    //        }
    //
    //        $view = View::make('trending.old.index')
    //            ->with([
    //                'trending' => $trending,
    //            ]);
    //
    //        if ($this->request->ajax()) {
    //            return $this->ajaxViewService->getRenderedSections($view, $this->request, $this->ajaxViewService::NO_CHECK);
    //        }
    //
    //        getMetatags();
    //
    //        return $view;
    //    }

    public function songs($startDate, $endDate, $limit = 50): LengthAwarePaginator
    {
        return Song::query()
            ->leftJoin('popular', 'popular.song_id', '=', 'songs.id')
            ->select('songs.*', DB::raw('sum(popular.plays) AS total_plays'))
            ->where('popular.created_at', '<=', $endDate)
            ->where('popular.created_at', '>=', $startDate)
            ->groupBy('popular.song_id')
            ->groupBy('popular.created_at')
            ->orderBy('total_plays', 'desc')
            ->paginate($limit);
    }
}
