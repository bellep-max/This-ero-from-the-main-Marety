<?php

namespace App\Console\Commands;

use App\Models\MESubscription;
use Carbon\Carbon;
use Illuminate\Console\Command;
use PayPal\Api\Agreement;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Rest\ApiContext;
use Stripe\StripeClient;

class SubscriptionCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscription:scan';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scan and review auto billing payment';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $subscriptions = MESubscription::query()
            ->whereDate('updated_at', '<', now()->subDay())
            ->whereDate('next_billing_date', '>=', now())
            ->get();

        $processedCount = 0;
        $errorCount = 0;

        foreach ($subscriptions as $subscription) {
            try {
                if ($subscription->gate == 'stripe') {
                    $stripe = new StripeClient(config('settings.payment_stripe_test_mode') ? config('settings.payment_stripe_test_key') : env('STRIPE_SECRET_API'));

                    $stripe_subscription = $stripe->subscriptions->retrieve($subscription->token);
                    $subscription->payment_status = $stripe_subscription->status == 'active' ? 1 : 0;
                    $subscription->next_billing_date = Carbon::parse($stripe_subscription->current_period_end);
                    $subscription->amount = $stripe_subscription->plan->amount / 100;
                    $subscription->currency = config('settings.currency', 'USD');
                    if ($stripe_subscription->status == 'active') {
                        $subscription->cycles = $stripe_subscription->plan->interval_count;
                        $subscription->last_payment_date = Carbon::now();
                    }
                    $subscription->save();
                    $processedCount++;
                } elseif ($subscription->gate == 'paypal') {
                    $apiContext = new ApiContext(
                        new OAuthTokenCredential(
                            env('PAYPAL_APP_CLIENT_ID'),
                            env('PAYPAL_APP_SECRET')
                        )
                    );

                    $agreement = Agreement::get($subscription->token, $apiContext);
                    $subscription->payment_status = Carbon::parse($agreement->getAgreementDetails()->next_billing_date) > Carbon::now() ? 1 : 0;
                    $plan = $agreement->getPlan();
                    $subscription->payment = $plan->getPaymentDefinitions()[0];
                    $subscription->next_billing_date = Carbon::parse($agreement->getAgreementDetails()->next_billing_date);
                    $subscription->cycles = $agreement->getAgreementDetails()->cycles_completed;
                    $subscription->amount = $plan->getPaymentDefinitions()[0]->amount->value;
                    $subscription->currency = $plan->getPaymentDefinitions()[0]->amount->currency;

                    $subscription->save();
                    $processedCount++;
                }
            } catch (\Exception $ex) {
                $errorCount++;
                $this->error("Failed to process subscription ID {$subscription->id}: " . $ex->getMessage());
            }
        }

        $this->info("Subscriptions scan completed! Processed: {$processedCount}, Errors: {$errorCount}, Total: " . $subscriptions->count());
    }
}
