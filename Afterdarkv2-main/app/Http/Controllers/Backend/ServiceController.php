<?php

/**
 * Created by NiNaCoder.
 * Date: 2019-05-25
 * Time: 09:02.
 */

namespace App\Http\Controllers\Backend;

use App\Helpers\MessageHelper;
use App\Http\Requests\Backend\Service\ServiceStoreRequest;
use App\Http\Requests\Backend\Service\ServiceUpdateRequest;
use App\Models\Service;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ServiceController
{
    private const DEFAULT_ROUTE = 'backend.services.index';

    public function index(Request $request)
    {
        $term = $request->input('q') ?? '';

        return view('backend.services.index')
            ->with([
                'total' => Service::query()->count(),
                'term' => $term,
                'services' => $term
                    ? Service::query()->where('title', $term)->paginate(20)
                    : Service::query()->paginate(20),
            ]);
    }

    public function create()
    {
        return view('backend.services.create');
    }

    public function store(ServiceStoreRequest $request): RedirectResponse
    {
        Service::create($request->validated());

        return MessageHelper::redirectMessage('Plan successfully added!', self::DEFAULT_ROUTE);
    }

    public function edit(Service $service)
    {
        return view('backend.services.form')
            ->with([
                'service' => $service,
            ]);
    }

    public function update(Service $service, ServiceUpdateRequest $request): RedirectResponse
    {
        $service->update($request->validated());

        return MessageHelper::redirectMessage('Plan successfully updated!', 'backend.plans.index');
    }

    public function destroy(Service $service): RedirectResponse
    {
        $service->delete();

        return MessageHelper::redirectMessage('Plan successfully deleted!');
    }
}
