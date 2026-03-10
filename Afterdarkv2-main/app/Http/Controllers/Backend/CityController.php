<?php

/**
 * Created by NiNaCoder.
 * Date: 2019-05-26
 * Time: 10:54.
 */

namespace App\Http\Controllers\Backend;

use App\Constants\DefaultConstants;
use App\Helpers\MessageHelper;
use App\Http\Requests\Backend\City\CityStoreRequest;
use App\Http\Requests\Backend\City\CityUpdateRequest;
use App\Models\City;
use App\Models\Country;
use App\Services\ArtworkService;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CityController
{
    private const DEFAULT_ROUTE = 'backend.cities.index';

    public function index(Request $request): View|Application|Factory
    {
        $cities = City::query()
            ->withoutGlobalScopes()
            ->when($request->has('term'), function ($query) use ($request) {
                $query->where('name', 'LIKE', '%' . $request->input('term') . '%');
            })
            ->when($request->has('fixed'), function ($query) {
                $query->where('fixed', DefaultConstants::TRUE);
            })
            ->when($request->has('hidden'), function ($query) {
                $query->where('is_visible', DefaultConstants::FALSE);
            })
            ->when($request->has('country'), function ($query) use ($request) {
                $query->where('country_code', $request->input('country'));
            })
            ->when($request->has('name'), function ($query) use ($request) {
                $query->orderBy('name', $request->input('name'));
            });

        $governmentForms = [];

        foreach (Country::query()->select('government_form')->groupBy('government_form')->get() as $country) {
            $governmentForms["$country->government_form"] = $country->government_form;
        }

        return view('backend.cities.index')
            ->with([
                'cities' => $request->has('results_per_page')
                    ? $cities->paginate(intval($request->input('results_per_page')))
                    : $cities->orderBy('fixed', 'desc')->paginate(20),
                'governmentForms' => $governmentForms,
            ]);
    }

    public function create(): View|Application|Factory
    {
        return view('backend.cities.create');
    }

    public function store(CityStoreRequest $request): RedirectResponse
    {
        $city = City::create($request->all());

        if ($request->hasFile('artwork')) {
            ArtworkService::updateArtwork($request, $city);
        }

        return MessageHelper::redirectMessage('City successfully added!', self::DEFAULT_ROUTE);
    }

    public function edit(City $city): View|Application|Factory
    {
        return view('backend.cities.edit')
            ->with([
                'city' => $city,
            ]);
    }

    public function update(City $city, CityUpdateRequest $request): RedirectResponse
    {
        $city->update($request->all());

        if ($request->hasFile('artwork')) {
            ArtworkService::updateArtwork($request, $city);
        }

        return MessageHelper::redirectMessage('City successfully updated!', self::DEFAULT_ROUTE);
    }

    public function destroy(City $city): RedirectResponse
    {
        $city->delete();

        return MessageHelper::redirectMessage('City successfully deleted!', self::DEFAULT_ROUTE);
    }
}
