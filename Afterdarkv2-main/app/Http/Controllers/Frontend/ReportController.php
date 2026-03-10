<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Frontend\Report\ReportStoreRequest;
use App\Mail\ReportCreated;
use App\Services\ReportService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Mail;

class ReportController extends Controller
{
    protected ReportService $reportService;

    public function __construct(ReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    public function __invoke(ReportStoreRequest $request): RedirectResponse
    {
        if ($report = $this->reportService->store($request->validated())) {
            Mail::to(config('mail.admin_mail.address'))->send(new ReportCreated($report));

            session()->flash('message', [
                'level' => 'success',
                'content' => 'Your report has been accepted!',
            ]);

            return redirect()->back(303);
        }

        session()->flash('message', [
            'level' => 'danger',
            'content' => 'Something went wrong!',
        ]);

        return redirect()->back(303);
    }
}
