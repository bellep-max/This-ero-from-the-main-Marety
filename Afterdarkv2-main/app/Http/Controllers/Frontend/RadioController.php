<?php

namespace App\Http\Controllers\Frontend;

use App\Constants\DefaultConstants;
use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\Channel;
use App\Models\City;
use App\Models\Country;
use App\Models\CountryLanguage;
use App\Models\RadioCategory;
use App\Models\Region;
use App\Models\Slide;
use App\Models\Station;
use App\Services\AjaxViewService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use stdClass;
use Torann\LaravelMetaTags\Facades\MetaTag;

class RadioController extends Controller
{
    private Request $request;

    private AjaxViewService $ajaxViewService;

    public function __construct(Request $request, AjaxViewService $ajaxViewService)
    {
        $this->request = $request;
        $this->ajaxViewService = $ajaxViewService;
    }

    public function index(): \Illuminate\Contracts\View\View|string|null
    {
        $channels = Channel::query()
            ->where('allow_radio', DefaultConstants::TRUE)
            ->orderBy('priority', 'asc')
            ->get();
        $slides = Slide::query()
            ->where('allow_radio', DefaultConstants::TRUE)
            ->orderBy('priority', 'asc')
            ->get();
        $radio = RadioCategory::query()
            ->orderBy('priority', 'asc')
            ->get();
        $radio->countries = Country::query()
            ->where('fixed', DefaultConstants::TRUE)
            ->get();
        $radio->cities = City::query()
            ->where('fixed', DefaultConstants::TRUE)
            ->get();
        $radio->languages = CountryLanguage::query()
            ->where('fixed', DefaultConstants::TRUE)
            ->get();

        $radio->regions = Region::all();

        $view = View::make('radio.index')
            ->with([
                'slides' => json_decode(json_encode($slides)),
                'channels' => json_decode(json_encode($channels)),
                'radio' => $radio,
            ]);

        if ($this->request->ajax()) {
            return $this->ajaxViewService->getRenderedSections($view, $this->request, $this->ajaxViewService::NO_CHECK);
        }

        Helper::getMetatags();

        return $view;
    }

    public function browse(): \Illuminate\Contracts\View\View|string|null
    {
        $browse = new stdClass;

        $stations = Station::query()->withoutGlobalScopes();

        if ($this->request->route()->getName() == 'frontend.radio.browse.languages') {
            $browse->languages = CountryLanguage::query()
                ->groupBy('name')
                ->get();
        } elseif ($this->request->route()->getName() == 'frontend.radio.browse.by.language') {
            $browse->language = CountryLanguage::findOrFail($this->request->route('id'));
            $browse->countries = Country::query()
                ->leftJoin('country_languages', 'country_languages.country_id', '=', 'countries.id')
                ->select('countries.*', 'country_languages.id as host_id', 'country_languages.name as host_name')
                ->groupBy('countries.code')
                ->orderBy('countries.fixed', 'desc')
                ->where('country_languages.name', $browse->language->name)
                ->get();
            $stations = $stations->where('language_id', $this->request->route('id'));
        } elseif ($this->request->route()->getName() == 'frontend.radio.browse.regions') {
            $browse->regions = Region::all();
        } elseif ($this->request->route()->getName() == 'frontend.radio.browse.by.region') {
            $browse->region = Region::query()
                ->where('alt_name', $this->request->route('slug'))
                ->first();
            $browse->countries = Country::query()
                ->where('region_id', $browse->region->id)
                ->get();
            $browse->languages = CountryLanguage::query()
                ->leftJoin('countries', 'country_languages.country_id', '=', 'countries.id')
                ->select('country_languages.*', 'countries.id as host_id', 'countries.name as host_name')
                ->groupBy('country_languages.name')
                ->orderBy('country_languages.fixed', 'desc')
                ->where('countries.region_id', $browse->region->id)
                ->get();
        } elseif ($this->request->route()->getName() == 'frontend.radio.browse.countries') {
            $browse->countries = Country::all();
        } elseif ($this->request->route()->getName() == 'frontend.radio.browse.by.country') {
            $browse->country = Country::query()
                ->where('code', $this->request->route('code'))
                ->firstOrFail();
            $browse->languages = CountryLanguage::query()
                ->leftJoin('countries', 'country_languages.country_id', '=', 'countries.id')
                ->select('country_languages.*', 'countries.id as host_id', 'countries.name as host_name')
                ->groupBy('country_languages.name')
                ->orderBy('country_languages.fixed', 'desc')
                ->where('countries.id', $browse->country->id)
                ->get();
            $browse->cities = City::query()
                ->where('country_code', $browse->country->code)
                ->get();
            $stations = $stations->where('country_code', $this->request->route('code'));
        } elseif ($this->request->route()->getName() == 'frontend.radio.browse.by.city') {
            $browse->city = City::findOrFail($this->request->route('id'));
            $stations = $stations->where('city_id', $this->request->route('id'));
        } elseif ($this->request->route()->getName() == 'frontend.radio.browse.category') {
            $browse->category = RadioCategory::query()
                ->where('alt_name', $this->request->route('slug'))
                ->first();
            $browse->channels = json_decode(json_encode(Channel::query()->where('radio', 'REGEXP', '(^|,)(' . $browse->category->id . ')(,|$)')->orderBy('priority', 'asc')->get()));
            $browse->slides = json_decode(json_encode(Slide::query()->where('radio', 'REGEXP', '(^|,)(' . $browse->category->id . ')(,|$)')->orderBy('priority', 'asc')->get()));
            $stations = $stations->where('category', 'REGEXP', '(^|,)(' . $browse->category->id . ')(,|$)');

            MetaTag::set('title', $browse->category->meta_title ? $browse->category->meta_title : $browse->category->name);
            MetaTag::set('description', $browse->category->meta_description ? $browse->category->meta_description : $browse->category->description);
            MetaTag::set('keywords', $browse->category->meta_keywords);
            MetaTag::set('image', $browse->category->artwork);
        } else {
            abort(404);
        }

        if ($this->request->has('country')) {
            $stations = $stations->where('country_code', $this->request->input('country'));
        }

        if ($this->request->has('city_id')) {
            $stations = $stations->where('city_id', $this->request->input('city_id'));
        }

        if ($this->request->has('language_id')) {
            $stations = $stations->where('language_id', $this->request->input('language_id'));
        }

        $browse->stations = $stations->paginate(20);

        $view = View::make('radio.browse')
            ->with([
                'browse' => $browse,
            ]);

        return $this->request->ajax()
            ? $this->ajaxViewService->getRenderedSections($view, $this->request, $this->ajaxViewService::FULL_CHECK)
            : $view;
    }

    public function cityByCountryCode(): string
    {
        $this->request->validate([
            'countryCode' => 'required|string|max:3',
        ]);

        return Helper::makeCityDropDown($this->request->input('countryCode'), 'city_id', 'toolbar-filter-city-select2', $selected = null);
    }

    public function languageByCountryCode(): string
    {
        $this->request->validate([
            'countryCode' => 'required|string|max:3',
        ]);

        return Helper::makeCountryLanguageDropDown($this->request->input('countryCode'), 'language_id', 'toolbar-filter-language-select2', $selected = null);
    }
}
