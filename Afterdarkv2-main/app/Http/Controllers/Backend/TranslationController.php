<?php

namespace App\Http\Controllers\Backend;

use App\Helpers\MessageHelper;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

// use NiNaCoder\Translation\Drivers\Translation;
// use NiNaCoder\Translation\Http\Requests\TranslationRequest;

class TranslationController extends Controller
{
    private $translation;

    //    public function __construct(Translation $translation)
    //    {
    //        // $this->request = $request;
    //        $this->translation = $translation;
    //    }

    public function show(Request $request, $language): View|Application|Factory|RedirectResponse
    {
        if ($request->has('language') && $request->get('language') !== $language) {
            return redirect()->route('backend.languages.translations.show', $request->get('language'))
                ->with([
                    'language' => $request->get('language'),
                    'group' => $request->get('group'),
                    'filter' => $request->get('filter'),
                ]);
        }

        $languages = $this->translation->allLanguages();
        $groups = $this->translation->getGroupsFor(env('APP_LOCALE', 'en'))->merge('missing');
        $translations = $this->translation->filterTranslationsFor($language, $request->get('filter'));

        if ($request->has('group') && $request->get('group')) {
            if ($request->get('group') === 'single') {
                $translations = $translations->get('single');
                $translations = new Collection(['single' => $translations]);
            } elseif ($request->get('group') === 'missing') {
                $missingTranslations = $this->translation->findMissingTranslations($language);
                $translations = [];
                foreach ($missingTranslations['group'] as $groupKey => $group) {
                    $translations['group'][$groupKey] = $group;
                    foreach ($group as $key => $value) {
                        $translations['group'][$groupKey][$key] = [
                            env('APP_LOCALE', 'en') => __($groupKey . '.' . $key),
                            $language => null,
                        ];
                    }
                }
                $translations = new Collection($translations);
            } else {
                $translations = $translations->get('group')->filter(function ($values, $group) use ($request) {
                    return $group === $request->get('group');
                });
                $translations = new Collection(['group' => $translations]);
            }
        }

        return view('backend.languages.translations.index')
            ->with([
                'language' => $language,
                'languages' => $languages,
                'groups' => $groups,
                'translations' => $translations,
            ]);
    }

    public function store(TranslationRequest $request, $language): RedirectResponse
    {
        if ($request->input('group')) {
            $namespace = $request->has('namespace') && $request->input('namespace') ? "{$request->input('namespace')}::" : '';
            $this->translation->addGroupTranslation(env('APP_LOCALE', 'en'), "$namespace{$request->input('group')}", $request->input('key'), $request->input('value') ?: '');
        } else {
            $this->translation->addSingleTranslation(env('APP_LOCALE', 'en'), 'single', $request->input('key'), $request->input('value') ?: '');
        }

        return MessageHelper::redirectMessage('Translation successfully updated!');
    }

    public function update(Request $request, $language): JsonResponse
    {
        if (!Str::contains($request->input('group'), 'single')) {
            $this->translation->addGroupTranslation($language, $request->input('group'), $request->get('key'), $request->input('value') ?: '');
        } else {
            $this->translation->addSingleTranslation($language, $request->input('group'), $request->input('key'), $request->input('value') ?: '');
        }

        return response()->json(['success' => true], 200);
    }
}
