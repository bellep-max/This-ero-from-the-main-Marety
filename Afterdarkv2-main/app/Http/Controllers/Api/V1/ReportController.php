<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\Frontend\Report\ReportStoreRequest;
use App\Mail\ReportCreated;
use App\Services\ReportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Mail;

class ReportController extends ApiController
{
    public function __construct(private readonly ReportService $reportService) {}

    public function store(ReportStoreRequest $request): JsonResponse
    {
        if ($report = $this->reportService->store($request->validated())) {
            Mail::to(config('mail.admin_mail.address'))->send(new ReportCreated($report));

            return $this->success(null, 'Your report has been accepted!', 201);
        }

        return $this->error('Something went wrong!', 500);
    }
}
