<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\HashTag;
use App\Services\AjaxViewService;
use DB;
use Illuminate\Http\Request;
use Route;
use View;

class HashTagController extends Controller
{
    private $request;

    private AjaxViewService $ajaxViewService;

    public function __construct(Request $request, AjaxViewService $ajaxViewService)
    {
        $this->request = $request;
        $this->ajaxViewService = $ajaxViewService;
    }

    public function index($slug)
    {
        $tags = Route::currentRouteName() == 'frontend.hashtag'
            ? HashTag::query()
                ->leftJoin('activities', 'hash_tags.hashable_id', '=', 'activities.id')
                ->select('hashable_id')
                ->where('tag', $slug)
                ->orderBy('activities.comment_count', 'desc')
                ->paginate(20)
            : HashTag::query()
                ->select('hashable_id')
                ->orderBy('id', 'desc')
                ->where('tag', $slug)
                ->paginate(20);

        $totalCount = HashTag::query()
            ->groupBy('tag')
            ->where('tag', $slug)
            ->count();

        $ids = [];

        foreach ($tags as $tag) {
            $ids[] = $tag->hashable_id;
        }

        if (!count($ids)) {
            abort(404);
        }

        $activities = Activity::query()
            ->whereIn('id', $ids)
            ->orderBy(DB::raw('FIELD(id, ' . implode(',', $ids) . ')', 'FIELD'))
            ->get();

        $view = View::make('hashtag.index')
            ->with([
                'tag' => $slug,
                'activities' => $activities,
                'total' => $totalCount,
            ]);

        return $this->request->ajax()
            ? $this->ajaxViewService->getRenderedSections($view, $this->request)
            : $view;
    }
}
