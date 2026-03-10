<?php

/**
 * Created by NiNaCoder.
 * Date: 2019-05-26
 * Time: 10:54.
 */

namespace App\Http\Controllers\Backend;

use App\Constants\DefaultConstants;
use App\Helpers\MessageHelper;
use App\Http\Requests\Backend\Station\StationIndexRequest;
use App\Http\Requests\Backend\Station\StationStoreRequest;
use App\Http\Requests\Backend\Station\StationUpdateRequest;
use App\Models\Station;
use App\Services\ArtworkService;
use Carbon\Carbon;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class StationController
{
    private const DEFAULT_ROUTE = 'backend.stations.index';

    public function index(StationIndexRequest $request): View|Application|Factory
    {
        $stations = Station::query()
            ->withoutGlobalScopes()
            ->when($request->has('term'), function ($query) use ($request) {
                switch ($request->input('location')) {
                    case 0:
                        $query->search($request->input('term'));
                        break;
                    case 1:
                        $query->where('title', 'LIKE', '%' . $request->input('term') . '%');
                        break;
                    case 2:
                        $query->where('description', 'LIKE', '%' . $request->input('term') . '%');
                        break;
                }
            }, function ($query) use ($request) {
                $query->where('title', 'LIKE', '%' . $request->input('term') . '%');
            })
            ->when($request->input('userIds'), function ($query) use ($request) {
                $query->whereIn('user_id', $request->input('userIds'));
            })
            ->when($request->input('category'), function ($query) use ($request) {
                $query->where('category', 'REGEXP', '(^|,)(' . implode(',', $request->input('category')) . ')(,|$)');
            })
            ->when($request->input('created_from'), function ($query) use ($request) {
                $query->where('created_at', '>=', Carbon::parse($request->input('created_from')));
            })
            ->when($request->input('created_until'), function ($query) use ($request) {
                $query->where('created_at', '<=', Carbon::parse($request->input('created_until')));
            })
            ->when($request->input('comment_count_from'), function ($query) use ($request) {
                $query->where('comment_count', '>=', $request->input('comment_count_from'));
            })
            ->when($request->input('comment_count_until'), function ($query) use ($request) {
                $query->where('comment_count', '<=', $request->input('comment_count_until'));
            })
            ->when($request->has('comment_disabled'), function ($query) {
                $query->where('allow_comments', DefaultConstants::FALSE);
            })
            ->when($request->has('hidden'), function ($query) {
                $query->where('is_visible', DefaultConstants::FALSE);
            })
            ->when($request->has('country'), function ($query) use ($request) {
                $query->where('country_code', $request->input('country'));
            })
            ->when($request->has('city'), function ($query) use ($request) {
                $query->where('city_id', $request->input('city'));
            })
            ->when($request->has('language'), function ($query) use ($request) {
                $query->where('language_id', $request->input('language'));
            });

        return view('backend.stations.index')
            ->with([
                'stations' => $request->has('results_per_page')
                    ? $stations->paginate($request->input('results_per_page'))
                    : $stations->paginate(20),
            ]);
    }

    public function create(): View|Application|Factory
    {
        return view('backend.stations.create');
    }

    public function store(StationStoreRequest $request): RedirectResponse
    {
        $station = Station::create($request->all());

        if ($request->hasFile('artwork')) {
            ArtworkService::updateArtwork($request, $station);
        }

        return MessageHelper::redirectMessage('Station successfully edited!', self::DEFAULT_ROUTE);
    }

    public function edit(Station $station): View|Application|Factory
    {
        return view('backend.stations.edit')
            ->with([
                'station' => $station,
            ]);
    }

    public function update(Station $station, StationUpdateRequest $request): RedirectResponse
    {
        $station->update($request->all());

        if ($request->hasFile('artwork')) {
            ArtworkService::updateArtwork($request, $station);
        }

        return MessageHelper::redirectMessage('Station successfully edited!', self::DEFAULT_ROUTE);
    }

    public function destroy(Station $station): RedirectResponse
    {
        $station->delete();

        return MessageHelper::redirectMessage('Station successfully deleted!', self::DEFAULT_ROUTE);
    }

    public function search(Request $request): JsonResponse
    {
        $result = Station::query()
            ->where('title', 'LIKE', "%{$request->input('q')}%")
            ->paginate(20);

        return response()->json($result);
    }
}
