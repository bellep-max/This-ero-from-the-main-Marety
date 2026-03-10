<?php

/**
 * Created by NiNaCoder.
 * Date: 2019-05-25
 * Time: 20:58.
 */

namespace App\Http\Controllers\Backend;

use App\Helpers\MessageHelper;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use NiNaCoder\Translation\Drivers\Translation;
use NiNaCoder\Translation\Http\Requests\LanguageRequest;

class LanguageController extends Controller
{
    private $translation;

    public function __construct(Translation $translation, Request $request)
    {
        // $this->request = $request;
        $this->translation = $translation;
    }

    public function index()
    {
        return view('backend.languages.index')
            ->with([
                'languages' => $this->translation->allLanguages(),
            ]);
    }

    public function store(LanguageRequest $request): RedirectResponse
    {
        $this->translation->addLanguage($request->input('locale'), ($request->input('name')));

        return MessageHelper::redirectMessage('Language successfully added!');
    }

    public function destroy(Request $request): RedirectResponse
    {
        if ($request->route('language') == env('APP_LOCALE', 'en')) {
            abort(403, 'You can not delete the default language.');
        }

        $this->translation->deleteLanguage($request->route('language'));

        return MessageHelper::redirectMessage('Language successfully deleted!');
    }
}
