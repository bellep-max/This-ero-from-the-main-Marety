<?php

namespace App\Http\Controllers\Backend;

use App\Constants\PlanConstants;
use App\Helpers\MessageHelper;
use App\Http\Requests\Backend\Plan\PlanUpdateRequest;
use App\Models\MEPlan;
use App\Services\PlanService;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;

class PlanController
{
    private PlanService $planService;

    public function __construct(PlanService $planService)
    {
        $this->planService = $planService;
    }

    public function index(): View|Application|Factory
    {
        return view('backend.plans.index')
            ->with([
                'sitePlan' => MEPlan::query()->firstWhere('type', PlanConstants::TYPE_SITE),
                'userPlan' => MEPlan::query()->firstWhere('type', PlanConstants::TYPE_USER),
            ]);
    }

    public function update(PlanUpdateRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $this->planService->updatePlan(PlanConstants::TYPE_SITE, $data['site']);
        $this->planService->updatePlan(PlanConstants::TYPE_USER, $data['user']);

        return MessageHelper::redirectMessage('Plans successfully updated!', 'backend.plans.index');
    }
}
