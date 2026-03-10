<?php

namespace App\Http\Controllers\Backend;

use App\Helpers\MessageHelper;
use App\Http\Requests\Backend\Page\PageStoreRequest;
use App\Http\Requests\Backend\Page\PageUpdateRequest;
use App\Models\Page;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;

class PageController
{
    private const DEFAULT_ROUTE = 'backend.pages.index';

    public function index(): View|Application|Factory
    {
        return view('backend.pages.index')
            ->with([
                'pages' => Page::query()->paginate(20),
            ]);
    }

    public function create(): View|Application|Factory
    {
        return view('backend.pages.create');
    }

    public function store(PageStoreRequest $request): RedirectResponse
    {
        Page::create($request->all());

        return MessageHelper::redirectMessage('Static page successfully created!', self::DEFAULT_ROUTE);
    }

    public function edit(Page $page): View|Application|Factory
    {
        return view('backend.pages.edit')
            ->with([
                'page' => $page,
            ]);
    }

    public function update(Page $page, PageUpdateRequest $request): RedirectResponse
    {
        $page->update($request->all());

        return MessageHelper::redirectMessage('Static page successfully updated!', self::DEFAULT_ROUTE);
    }

    public function destroy(Page $page): RedirectResponse
    {
        $page->delete();

        return MessageHelper::redirectMessage('Static page successfully deleted!', self::DEFAULT_ROUTE);
    }
}
