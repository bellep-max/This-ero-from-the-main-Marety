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
use App\Http\Requests\Backend\Country\CountryStoreRequest;
use App\Http\Requests\Backend\Country\CountryUpdateRequest;
use App\Models\Country;
use App\Services\ArtworkService;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CountryController
{
    private $request;

    private const DEFAULT_ROUTE = 'backend.countries.index';

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function index(): View|Application|Factory
    {
        $countries = Country::query()
            ->withoutGlobalScopes()
            ->when($this->request->has('name'), function ($query) {
                $query->where('title', 'LIKE', '%' . $this->request->input('term') . '%')
                    ->orderBy('name', $this->request->input('name'));
            })
            ->when($this->request->has('fixed'), function ($query) {
                $query->where('fixed', DefaultConstants::TRUE);
            })
            ->when($this->request->has('hidden'), function ($query) {
                $query->where('is_visible', DefaultConstants::FALSE);
            })
            ->when($this->request->has('government_form'), function ($query) {
                $query->where('government_form', 'like', '%' . $this->request->input('government_form') . '%');
            })
            ->when($this->request->has('region'), function ($query) {
                $query->whereHas('region', function ($query) {
                    $query->where('id', intval($this->request->input('region')));
                });
            });

        $governmentForms = [];

        foreach (Country::query()->select('government_form')->groupBy('government_form')->get() as $country) {
            $governmentForms["$country->government_form"] = $country->government_form;
        }

        return view('backend.countries.index')
            ->with([
                'countries' => $this->request->has('results_per_page')
                    ? $countries->paginate(intval($this->request->input('results_per_page')))
                    : $countries->orderBy('fixed', 'desc')->paginate(20),
                'governmentForms' => $governmentForms,
            ]);
    }

    public function create(): View|Application|Factory
    {
        return view('backend.countries.create');
    }

    public function edit(Country $country): View|Application|Factory
    {
        return view('backend.countries.edit')
            ->with([
                'country' => $country,
            ]);
    }

    public function store(CountryStoreRequest $request): RedirectResponse
    {
        $country = Country::create($request->input());

        if ($this->request->hasFile('artwork')) {
            ArtworkService::updateArtwork($this->request, $country, request()->route()->getName() == 'backend.countries.update');
        }

        return MessageHelper::redirectMessage('Country successfully edited!', self::DEFAULT_ROUTE);
    }

    public function update(Country $country, CountryUpdateRequest $request): RedirectResponse
    {
        $country->update($request->input());

        if ($this->request->hasFile('artwork')) {
            ArtworkService::updateArtwork($this->request, $country, request()->route()->getName() == 'backend.countries.update');
        }

        return MessageHelper::redirectMessage('Country successfully edited!', self::DEFAULT_ROUTE);
    }

    public function destroy(Country $country): RedirectResponse
    {
        $country->delete();

        return MessageHelper::redirectMessage('Country successfully deleted!', self::DEFAULT_ROUTE);
    }

    public function getCity(CountryCodeGetRequest $request): string
    {
        return Helper::makeCityDropDown($request->input('countryCode'), 'city_id', 'form-control select2-active', $selected = null);
    }
}
