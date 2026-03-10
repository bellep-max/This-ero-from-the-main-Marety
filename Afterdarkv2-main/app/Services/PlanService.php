<?php

namespace App\Services;

use App\Models\MEPlan;
use Illuminate\Support\Facades\Log;
use Srmklive\PayPal\Services\PayPal;
use Throwable;

class PlanService
{
    public function updatePlan(string $type, array $data): ?MEPlan
    {
        $plan = MEPlan::query()->firstWhere('type', $type);

        try {
            $provider = new PayPal;
            $provider->getAccessToken();

            $provider->updatePlan($plan->paypal_id, [
                'name' => $data['name'],
                'description' => $data['description'],
            ]);

            $percentage = !empty($data['percentage']) ? intval($data['percentage']) : 0;

            if ($data['price'] !== (string) $plan->price || ($type === 'site' && $percentage !== $plan->percentage)) {
                $price = floatval($data['price']) + floatval($data['price']) * ($percentage / 100);

                $provider->updatePlanPricing($plan->paypal_id, [
                    [
                        'billing_cycle_sequence' => 1,
                        'pricing_scheme' => [
                            'fixed_price' => [
                                'value' => (string) $price,
                                'currency_code' => config('settings.currency', 'USD'),
                            ],
                        ],
                    ],
                ]);
            }

            $plan->update($data);
        } catch (Throwable $exception) {
            Log::error($exception->getMessage());

            return null;
        }

        return $plan;
    }
}
