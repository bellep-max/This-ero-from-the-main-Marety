<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\View\View;

class AjaxViewService
{
    public const NO_CHECK = 0;

    public const NORMAL = 1;

    public const FULL_CHECK = 2;

    public const SEARCH_CHECK = 3;

    public function getRenderedSections(View $view, Request $request, $type = self::NORMAL): ?string
    {
        $sections = $view->renderSections();

        if ($type == self::NO_CHECK) {
            return $sections['content'];
        } elseif ($type == self::FULL_CHECK) {
            return ($request->input('page') && intval($request->input('page')) > 1) || $request->input('browsing')
                ? $sections['pagination']
                : $sections['content'];
        } elseif ($type == self::SEARCH_CHECK) {
            return $request->input('search') || ($request->input('page') && intval($request->input('page')) > 1)
                ? $sections['pagination']
                : $sections['content'];
        }

        return $request->input('page') && intval($request->input('page')) > 1
            ? $sections['pagination']
            : $sections['content'];
    }
}
