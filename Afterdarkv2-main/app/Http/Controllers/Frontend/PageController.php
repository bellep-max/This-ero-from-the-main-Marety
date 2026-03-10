<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Services\MetatagService;
use Inertia\Inertia;
use Inertia\Response;

class PageController extends Controller
{
    public function __construct(private readonly MetatagService $metatagService) {}

    public function show(Page $page): Response
    {
        $this->metatagService->setMetatags($page);

        return Inertia::render('StaticPage', [
            'content' => $page,
        ]);
    }
}
