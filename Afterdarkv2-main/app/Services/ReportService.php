<?php

namespace App\Services;

use App\Constants\StatusConstants;
use App\Models\Report;
use App\Models\Service;
use App\Models\MESubscription;
use App\Services\Backend\BackendService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use stdClass;

class ReportService
{
    public function store(array $data): ?Report
    {
        if (!$model = $data['type']::query()->withoutGlobalScopes()->whereUuid($data['uuid'])->first()) {
            return null;
        }

        return $model->reports()->create([
            'user_id' => auth()->id(),
            'type' => $data['report_type'],
            'message' => array_key_exists('message', $data) ? $data['message'] : null,
        ]);
    }

    public function getReportData(Carbon $fromDate, Carbon $toDate): stdClass
    {
        $data = new stdClass;

        $tmpSubTable = (new MESubscription())->getTable();

        $subscriptionsData = MESubscription::query()
            ->select(DB::raw('sum(amount) AS earnings'), DB::raw('DATE(created_at) as date'))
            ->whereDate('created_at', '<=', $toDate)
            ->whereDate('created_at', '>=', $fromDate)
            ->whereIn($tmpSubTable . '.status', StatusConstants::getAll())
            ->groupBy('date')
            ->get()
            ->toArray();

        $rows = BackendService::insertMissingData($subscriptionsData, ['earnings'], $fromDate, $toDate);

        $data->day = new stdClass;
        $data->day->earnings = [];
        $data->day->period = [];

        foreach ($rows as $item) {
            $item = (array) $item;
            $data->day->earnings[] = $item['earnings'];
            $data->day->period[] = Carbon::parse($item['date'])->format('F j');
        }

        $rows = MESubscription::query()
            ->select(DB::raw('sum(amount) AS earnings'), DB::raw('count(*) AS orders'), DB::raw('MONTH(created_at) as month'), DB::raw('YEAR(created_at) as year'))
            ->whereDate('created_at', '<=', $toDate)
            ->whereDate('created_at', '>=', $fromDate)
            ->whereIn('status', StatusConstants::getAll())
            ->groupBy('month')
            ->get();

        $data->month = new stdClass;

        $data->month->earnings = [];
        $data->month->orders = [];
        $data->month->period = [];

        foreach ($rows as $item) {
            $item = (array) $item;
            $data->month->earnings[] = $item['earnings'];
            $data->month->orders[] = $item['orders'];
            $data->month->period[] = $item['month'] . '/' . $item['year'];
        }

        /* get plan data */
        $data->plans = Service::query()
            ->where('created_at', '<=', $toDate)
            ->select('id', 'title')
            ->get();

        $data->success = MESubscription::query()
            ->whereIn('status', StatusConstants::getAll())
            ->whereBetween('created_at', [$fromDate, $toDate])
            ->count();
        $data->failed = MESubscription::query()
            ->whereNotIn('status', StatusConstants::getAll())
            ->whereBetween('created_at', [$fromDate, $toDate])
            ->count();
        $data->paypal = MESubscription::query()
            ->whereBetween('created_at', [$fromDate, $toDate])
            ->count();
        $data->stripe = MESubscription::query()
            ->whereBetween('created_at', [$fromDate, $toDate])
            ->count();

        $data->total = MESubscription::query()
            ->select(DB::raw('sum(amount) AS earnings'))
            ->where($tmpSubTable . '.created_at', '<=', $toDate)
            ->where($tmpSubTable . '.created_at', '>=', $fromDate)
            ->whereIn($tmpSubTable . '.status', StatusConstants::getAll())
            ->first();

        $data->orders = MESubscription::query()
            ->whereBetween('created_at', [$fromDate, $toDate])
            ->count();

        return $data;
    }

    private function formatDate(Carbon $date, string $format = 'Y/m/d H:i:s'): string
    {
        return $date->format($format);
    }
}
