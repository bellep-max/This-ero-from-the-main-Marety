<?php

namespace App\Console\Commands;

use App\Models\MEPlan;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Srmklive\PayPal\Services\PayPal;
use Throwable;

class PayPalInit extends Command
{
    protected $signature = 'paypal:init';

    protected $description = 'Create PayPal product & plan (site & user), if not exists';

    public PayPal $provider;

    /**
     * @throws Throwable
     */
    public function __construct()
    {
        $this->provider = new PayPal;
        $this->provider->setApiCredentials(config('paypal'));
        $this->provider->getAccessToken();
        parent::__construct();
    }

    /**
     * @throws Throwable
     */
    public function createOrGetProduct(?string $productId = null): array
    {
        $productTemplate = [
            'name' => 'Erocast',
            'description' => 'Erocast Default Product',
            'type' => 'SERVICE',
            'category' => 'ADULT',
        ];

        if (!$productId) {
            return $this->provider->createProduct($productTemplate);
        }

        $searchedProduct = $this->provider->showProductDetails($productId);

        return !empty($searchedProduct['error']) ? $this->provider->createProduct($productTemplate) : $searchedProduct;
    }

    public function collectPlanData(string $productId, string $type): array
    {
        $type = ucfirst($type);

        $name = $this->ask("[$type Subscription]: Set the plan name");
        $description = $this->ask("[$type Subscription]: Set the plan description (Optional)");
        $price = floatval($this->ask("[$type Subscription]: Set the plan price (Only numeric type)"));
        $percentage = intval($this->ask("[$type Subscription]: Set the plan percentage (Optional, only integer type 1-100)"));
        $percentageValue = $percentage ? $price * ($percentage / 100) : 0;

        return [
            'data' => [
                'product_id' => $productId,
                'name' => $name,
                'description' => $description,
                'billing_cycles' => [
                    [
                        'tenure_type' => 'REGULAR',
                        'sequence' => 1,
                        'total_cycles' => 0,
                        'frequency' => [
                            'interval_unit' => 'MONTH',
                            'price' => (string) ($price + $percentageValue),
                        ],
                        'pricing_scheme' => [
                            'fixed_price' => [
                                'value' => (string) ($price + $percentageValue),
                                'currency_code' => config('settings.currency', 'USD'),
                            ],
                        ],
                    ],
                ],
                'payment_preferences' => [
                    'auto_bill_outstanding' => true,
                    'setup_fee_failure_action' => 'CANCEL',
                    'payment_failure_threshold' => 0,
                ],
            ],
            'percentage' => $percentage,
            'price' => $price,
        ];
    }

    /**
     * @throws Throwable
     */
    public function updateOrCreatePlan(string $planId, string $type, ?float $price = null, ?int $percentage = null): MEPlan
    {
        $planDetails = $this->provider->showPlanDetails($planId);

        return MEPlan::updateOrCreate(['type' => strtolower($type)], [
            'role_id' => strtolower($type) === 'user' ? 3 : 4,
            'name' => $planDetails['name'],
            'description' => !empty($planDetails['description']) ? $planDetails['description'] : null,
            'paypal_id' => $planId,
            'price' => $price ?? floatval($planDetails['billing_cycles'][0]['pricing_scheme']['fixed_price']['value']),
            'percentage' => $percentage,
            'interval' => $planDetails['billing_cycles'][0]['frequency']['interval_unit'],
        ]);
    }

    public function handle(): int
    {
        try {
            if (config('app.env') !== 'local') {
                $webhooks = $this->provider->listWebHooks()['webhooks'];
                $webhook = !count($webhooks) ? $this->provider->createWebHook(config('app.url') . '/paypal/webhook', ['*']) : $webhooks[0];
                $this->info("Webhook ID: {$webhook['id']}");
            }

            $paypalProduct = $this->createOrGetProduct(config('paypal.product_id'));
            $this->info("PayPal Product ID: {$paypalProduct['id']}");

            $listPlans = $this->provider->listPlans();
            $totalPages = $listPlans['total_pages'];
            $plans = collect();

            collect($listPlans['plans'])->each(function (array $plan) use ($plans) {
                $plans->push($plan);
            });

            if ($totalPages > 1) {
                for ($page = 2; $page <= $totalPages; $page++) {
                    $pagePlans = $this->provider->setCurrentPage($page)->listPlans();
                    collect($pagePlans['plans'])->each(function (array $plan) use ($plans) {
                        $plans->push($plan);
                    });
                }
            }

            $filteredPlans = $plans->filter(function ($plan) use ($paypalProduct) {
                return $plan['product_id'] === $paypalProduct['id'] && $plan['status'] === 'ACTIVE';
            })->values();

            if ($filteredPlans->count() >= 2) {
                $filteredPlans->take(2)->each(function (array $paypalPlan, int $id) {
                    $type = ucfirst($id ? 'user' : 'site');
                    $plan = $this->updateOrCreatePlan($paypalPlan['id'], $type);
                    $this->info("$type Plan ID: $plan->paypal_id");
                });
            } else {
                $sitePlanData = $this->collectPlanData($paypalProduct['id'], 'site');
                $paypalSitePlan = $this->provider->createPlan($sitePlanData['data']);
                $sitePlan = $this->updateOrCreatePlan($paypalSitePlan['id'], 'site', $sitePlanData['price'], $sitePlanData['percentage']);
                $this->info("Site Plan ID: $sitePlan->paypal_id");

                $userPlanData = $this->collectPlanData($paypalProduct['id'], 'user');
                $paypalUserPlan = $this->provider->createPlan($userPlanData['data']);
                $userPlan = $this->updateOrCreatePlan($paypalUserPlan['id'], 'user', $userPlanData['price'], $userPlanData['percentage']);
                $this->info("User Plan ID: $userPlan->paypal_id");
            }

            return 0;
        } catch (Throwable $exception) {
            Log::error($exception->getMessage(), $exception->getTrace());
            $this->error($exception->getMessage());

            return 1;
        }
    }
}
