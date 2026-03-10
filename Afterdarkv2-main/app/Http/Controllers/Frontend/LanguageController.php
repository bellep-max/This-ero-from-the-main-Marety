<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Session;

class LanguageController extends Controller
{
    private Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function switchLanguage(): JsonResponse
    {
        $this->request->validate([
            'locale' => 'required|string',
        ]);

        Session::put('website_language', $this->request->input('locale'));
        App::setLocale($this->request->input('locale'));

        return response()->json(Lang::get('web'));
    }

    public function currentLanguage(): JsonResponse
    {
        return response()->json(Lang::get('web'));
    }
}
