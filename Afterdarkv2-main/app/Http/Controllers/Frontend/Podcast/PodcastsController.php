<?php

namespace App\Http\Controllers\Frontend\Podcast;

use App\Http\Controllers\Controller;
use App\Models\Channel;
use App\Models\City;
use App\Models\Country;
use App\Models\CountryLanguage;
use App\Models\Podcast;
use App\Models\PodcastCategory;
use App\Models\Region;
use App\Models\Slide;
use App\Services\AjaxViewService;
use App\Services\MetatagService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use stdClass;

class PodcastsController extends Controller
{
    private Request $request;

    private AjaxViewService $ajaxViewService;

    private MetatagService $metatagService;

    public function __construct(Request $request, AjaxViewService $ajaxViewService, MetatagService $metatagService)
    {
        $this->request = $request;
        $this->ajaxViewService = $ajaxViewService;
        $this->metatagService = $metatagService;
    }

    public function browse(): \Illuminate\Contracts\View\View|string|null
    {
        $browse = new stdClass;

        $podcasts = Podcast::withoutGlobalScopes();

        if ($this->request->route()->getName() == 'frontend.podcasts.browse.languages') {
            $browse->languages = CountryLanguage::query()
                ->groupBy('name')
                ->get();
        } elseif ($this->request->route()->getName() == 'frontend.podcasts.browse.by.language') {
            $browse->language = CountryLanguage::findOrFail($this->request->route('id'));
            $browse->countries = Country::query()
                ->leftJoin('country_languages', 'country_languages.country_id', '=', 'countries.id')
                ->select('countries.*', 'country_languages.id as host_id', 'country_languages.name as host_name')
                ->groupBy('countries.id')
                ->orderBy('countries.fixed', 'desc')
                ->where('country_languages.name', $browse->language->name)
                ->get();
            $podcasts = $podcasts->where('language_id', $this->request->route('id'));
        } elseif ($this->request->route()->getName() == 'frontend.podcasts.browse.regions') {
            $browse->regions = Region::all();
        } elseif ($this->request->route()->getName() == 'frontend.podcasts.browse.by.region') {
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
        } elseif ($this->request->route()->getName() == 'frontend.podcasts.browse.countries') {
            $browse->countries = Country::all();
        } elseif ($this->request->route()->getName() == 'frontend.podcasts.browse.by.country') {
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
            $podcasts = $podcasts->where('country_code', $this->request->route('code'));
        } elseif ($this->request->route()->getName() == 'frontend.podcasts.browse.by.city') {
            $browse->city = City::findOrFail($this->request->route('id'));
            $podcasts = $podcasts->where('city_id', $this->request->route('id'));
        } elseif ($this->request->route()->getName() == 'frontend.podcasts.browse.category') {
            $browse->category = PodcastCategory::query()
                ->where('alt_name', $this->request->route('slug'))
                ->first();
            $browse->channels = json_decode(json_encode(Channel::query()->where('podcast', 'REGEXP', '(^|,)(' . $browse->category->id . ')(,|$)')->orderBy('priority', 'asc')->get()));
            $browse->slides = json_decode(json_encode(Slide::query()->where('podcast', 'REGEXP', '(^|,)(' . $browse->category->id . ')(,|$)')->orderBy('priority', 'asc')->get()));
            $podcasts = $podcasts->where('category', 'REGEXP', '(^|,)(' . $browse->category->id . ')(,|$)');

            $this->metatagService->setMetatags($browse->category);
        } else {
            abort(404);
        }

        if ($this->request->has('country')) {
            $podcasts = $podcasts->where('country_code', $this->request->input('country'));
        }

        if ($this->request->has('language_id')) {
            $podcasts = $podcasts->where('language_id', $this->request->input('language_id'));
        }

        $browse->podcasts = $podcasts->paginate(20);

        $view = View::make('podcasts.browse')
            ->with([
                'browse' => $browse,
            ]);

        return $this->request->ajax()
            ? $this->ajaxViewService->getRenderedSections($view, $this->request, $this->ajaxViewService::FULL_CHECK)
            : $view;
    }
}
