<?php

namespace App\Http\Controllers\Frontend;

use App\Enums\LoveableObjectEnum;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

class ShareController extends Controller
{
    public function embed(Request $request): Factory|View|Application
    {
        $modelClass = LoveableObjectEnum::fromName($request->route('type'));

        $modelId = $modelClass::query()
            ->where('uuid', $request->route('uuid'))
            ->value('id');

        return view('embed', [
            'id' => $modelId,
            'theme' => $request->route('theme'),
            'type' => $request->route('type'),
        ]);
    }
}
