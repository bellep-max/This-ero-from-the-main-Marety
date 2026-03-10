<?php

/**
 * Created by NiNaCoder.
 * Date: 2019-05-26
 * Time: 10:54.
 */

namespace App\Http\Controllers\Backend;

use App\Constants\DefaultConstants;
use App\Helpers\Helper;
use App\Helpers\MessageHelper;
use App\Http\Requests\Backend\Country\CountryCodeGetRequest;
use App\Http\Requests\Backend\CountryLanguage\CountryLanguageStoreRequest;
use App\Http\Requests\Backend\CountryLanguage\CountryLanguageUpdateRequest;
use App\Models\Country;
use App\Models\CountryLanguage;
use App\Services\ArtworkService;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CountryLanguageController
{
    private const DEFAULT_ROUTE = 'backend.country.languages.index';

    public function index(Request $request): View|Application|Factory
    {
        $languages = CountryLanguage::query()
            ->withoutGlobalScopes()
            ->when($request->get('region'), function ($query) use ($request) {
                $query->whereHas('country', function ($query) use ($request) {
                    $query->where('region_id', intval($request->input('region')));
                });
            })
            ->when($request->get('term'), function ($query) use ($request) {
                $query->where('name', 'LIKE', '%' . $request->input('term') . '%');
            })
            ->when($request->get('fixed'), function ($query) {
                $query->where('fixed', DefaultConstants::TRUE);
            })
            ->when($request->get('hidden'), function ($query) {
                $query->where('is_visible', DefaultConstants::FALSE);
            })
            ->when($request->get('name'), function ($query) use ($request) {
                $query->where('name', $request->input('name'));
            });

        $governmentForms = [];

        foreach (Country::query()->select('government_form')->groupBy('government_form')->get() as $country) {
            $governmentForms["$country->government_form"] = $country->government_form;
        }

        return view('backend.country-languages.index')
            ->with([
                'languages' => $request->has('results_per_page')
                    ? $languages->paginate(intval($request->input('results_per_page')))
                    : $languages->orderBy('fixed', 'desc')->paginate(20),
                'governmentForms' => $governmentForms,
            ]);
    }

    public function create(): View|Application|Factory
    {
        return view('backend.country-languages.create');
    }

    public function edit(CountryLanguage $language): View|Application|Factory
    {
        return view('backend.country-languages.edit')
            ->with([
                'language' => $language,
            ]);
    }

    public function store(CountryLanguageStoreRequest $request): RedirectResponse
    {
        $language = CountryLanguage::create($request->input());

        if ($request->hasFile('artwork')) {
            ArtworkService::updateArtwork($request, $language);
        }

        return MessageHelper::redirectMessage('Language successfully edited!', self::DEFAULT_ROUTE);
    }

    public function update(CountryLanguage $language, CountryLanguageUpdateRequest $request): RedirectResponse
    {
        $language->update($request->input());

        if ($request->hasFile('artwork')) {
            ArtworkService::updateArtwork($request, $language);
        }

        return MessageHelper::redirectMessage('Language successfully edited!', self::DEFAULT_ROUTE);
    }

    public function destroy(CountryLanguage $language): RedirectResponse
    {
        $language->delete();

        return MessageHelper::redirectMessage('Language successfully deleted!', self::DEFAULT_ROUTE);
    }

    public function batch(Request $request): RedirectResponse
    {
        $collectionQuery = CountryLanguage::query()
            ->withoutGlobalScopes()
            ->whereIn('id', $request->input('ids'));

        if ($request->input('action') == 'make_hidden') {
            $collectionQuery->update([
                'is_visible' => DefaultConstants::FALSE,
            ]);
        } elseif ($request->input('action') == 'make_visible') {
            $collectionQuery->update([
                'is_visible' => DefaultConstants::TRUE,
            ]);
        } elseif ($request->input('action') == 'fixed') {
            $collectionQuery->update([
                'fixed' => DefaultConstants::TRUE,
            ]);
        } elseif ($request->input('action') == 'unfixed') {
            $collectionQuery->update([
                'fixed' => DefaultConstants::FALSE,
            ]);
        } elseif ($request->input('action') == 'delete') {
            CountryLanguage::destroy($request->input('ids'));

            return MessageHelper::redirectMessage('Languages successfully deleted!');
        }

        return MessageHelper::redirectMessage('Languages successfully updated!');
    }

    public function getLanguage(CountryCodeGetRequest $request): string
    {
        return Helper::makeCountryLanguageDropDown($request->input('countryCode'), 'language_id', 'form-control select2-active', $selected = null);
    }
}
