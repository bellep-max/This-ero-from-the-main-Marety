<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\SongTag;
use App\Services\AjaxViewService;
use Illuminate\Http\Request;
use View;

class TagController extends Controller
{
    private $request;

    private AjaxViewService $ajaxViewService;

    public function __construct(Request $request, AjaxViewService $ajaxViewService)
    {
        $this->request = $request;
        $this->ajaxViewService = $ajaxViewService;
    }

    public function index()
    {
        $tag = SongTag::query()
            ->where('tag', str_replace('-', ' ', $this->request->route('tag')))
            ->firstOrFail();

        $tag->setRelation('songs', $tag->songs()->paginate(20));

        if ($this->request->is('api*')) {
            return response()->json([
                'tag' => $tag,
            ]);
        }

        $view = View::make('tag.index')
            ->with([
                'tag' => $tag,
            ]);

        return $this->request->ajax()
            ? $this->ajaxViewService->getRenderedSections($view, $this->request)
            : $view;
    }
}
