<?php

namespace App\Http\Controllers\Frontend;

use App\Constants\DefaultConstants;
use App\Constants\StripeConstants;
use App\Http\Controllers\Controller;
use App\Jobs\SendEmail;
use App\Models\Order;
use App\Models\Service;
use App\Models\MESubscription;
use Carbon\Carbon;
use Cart;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Stripe\StripeClient;

class StripeController extends Controller
{
    private $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function subscription()
    {
        if (auth()->user()->subscription) {
            abort(403, 'You are already have a subscription.');
        }

        $this->request->validate([
            'planId' => 'required|integer',
            'stripeToken' => 'required|string',
        ]);

        $service = Service::findOrFail($this->request->input('planId'));

        $stripe = new StripeClient(config('settings.payment_stripe_test_mode') ? config('settings.payment_stripe_test_key') : env('STRIPE_SECRET_API'));

        $product = $stripe->products->create([
            'name' => $service->title,
        ]);

        $plan = $stripe->plans->create([
            'amount' => in_array(config('settings.currency', 'USD'), config('payment.currency_decimals')) ? ($service->price * 100) : (intval($service->price) * 100),
            'interval' => 'month',
            'product' => $product->id,
            'currency' => config('settings.currency', 'USD'),
        ]);

        $customer = $stripe->customers->create([
            'email' => auth()->user()->email,
            'source' => config('settings.payment_stripe_test_mode') ? 'tok_visa' : $this->request->input('stripeToken'),
        ]);

        if ($service->trial) {
            $trialEnd = match ($service->trial_period_format) {
                StripeConstants::DAY => now()->addDays($service->trial_period),
                StripeConstants::WEEK => now()->addWeeks($service->trial_period),
                StripeConstants::MONTH => now()->addMonths($service->trial_period),
                StripeConstants::YEAR => now()->addYears($service->trial_period),
                default => 'now',
            };
        } else {
            $trialEnd = 'now';
        }

        $stripe_subscription = $stripe->subscriptions->create([
            'customer' => $customer->id,
            'items' => [
                ['plan' => $plan->id],
            ],
            'trial_end' => ($trialEnd == 'now' ? $trialEnd : $trialEnd->timestamp),
        ]);

        if ($stripe_subscription->id) {
            $subscription = new MESubscription;
            $subscription->gate = 'stripe';
            $subscription->user_id = auth()->id();
            $subscription->service_id = $service->id;
            $subscription->payment_status = DefaultConstants::TRUE;
            $subscription->transaction_id = $stripe_subscription->id;
            $subscription->token = $stripe_subscription->id;
            $subscription->next_billing_date = Carbon::parse($stripe_subscription->current_period_end);
            $subscription->trial_end = ($trialEnd == 'now' ? Carbon::now() : $trialEnd);
            $subscription->amount = $service->price;
            $subscription->currency = config('settings.currency', 'USD');

            if ($stripe_subscription->status == 'active') {
                $subscription->cycles = $stripe_subscription->plan->interval_count;
                $subscription->last_payment_date = Carbon::now();
            }

            $subscription->save();

            dispatch(new SendEmail('subscriptionReceipt', auth()->user()->email, $subscription));

            return response()->json($subscription);
        } else {
            abort(500, 'Payment failed!');
        }
    }

    public function purchase(): JsonResponse
    {
        if (Cart::isEmpty()) {
            abort(500, 'Cart is empty');
        }

        $this->request->validate([
            'stripeToken' => 'required|string',
        ]);

        $description = '';

        foreach (Cart::getContent() as $item) {
            $description .= $item->id . '|';
        }

        $stripe = new StripeClient(config('settings.payment_stripe_test_mode') ? config('settings.payment_stripe_test_key') : env('STRIPE_SECRET_API'));

        $charge = $stripe->charges->create([
            'amount' => (Cart::getTotal() * 100),
            'currency' => config('settings.currency', 'USD'),
            'source' => config('settings.payment_stripe_test_mode') ? 'tok_visa' : $this->request->input('stripeToken'),
            'description' => $description,
        ]);

        if (!$charge->id) {
            abort(500, 'Payment failed!');
        }

        foreach (Cart::getContent() as $item) {
            Order::create([
                'user_id' => auth()->id(),
                'orderable_id' => $item->attributes->orderable_id,
                'orderable_type' => $item->attributes->orderable_type,
                'payment' => 'stripe',
                'amount' => $item->price,
                'currency' => config('settings.currency', 'USD'),
                'payment_status' => $charge->captured ? DefaultConstants::TRUE : DefaultConstants::FALSE,
                'transaction_id' => $charge->id,
            ]);
        }

        Cart::clear();

        return response()->json($charge);
    }
}
