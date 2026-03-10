<?php

namespace App\Http\Controllers\Backend;

use App\Http\Requests\Backend\Report\ReportStoreRequest;
use App\Services\ReportService;
use Carbon\Carbon;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;

class ReportController
{
    private ReportService $reportService;

    public function __construct(ReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    public function index(): View|Application|Factory
    {
        $data = $this->reportService->getReportData(now()->subMonth(), now());

        return view('backend.reports.index')
            ->with([
                'day' => $data->day,
                'month' => $data->month,
                'plans' => $data->plans,
                'data' => $data,
            ]);
    }

    public function store(ReportStoreRequest $request): View|Application|Factory
    {
        $dateFrom = Carbon::parse($request->input('from'));
        $dateTo = Carbon::parse($request->input('to'));

        $data = $this->reportService->getReportData($dateFrom, $dateTo);

        return view('backend.reports.index')
            ->with([
                'day' => $data->day,
                'month' => $data->month,
                'plans' => $data->plans,
                'fromDate' => $dateFrom->format('Y/m/d H:i'),
                'toDate' => $dateTo->format('Y/m/d H:i'),
                'data' => $data,
            ]);
    }
}
