<?php

namespace App\Http\Controllers\Settings;

use App\Enums\SubscriptionStatusEnum;
use App\Http\Controllers\Controller;
use App\Jobs\SendEmail;
use App\Models\MEPlan;
use App\Models\Service;
use App\Models\MESubscription;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;
use Srmklive\PayPal\Services\PayPal;
use Throwable;

class SubscriptionController extends Controller
{
    protected PayPal $provider;

    public function __construct()
    {
        try {
            $this->provider = new PayPal;
            $this->provider->setApiCredentials(config('paypal'));
            $this->provider->getAccessToken();
        } catch (Throwable $exception) {
            abort($exception->getCode(), $exception->getMessage());
        }
    }

    public function edit(): Response
    {
        Gate::authorize('edit', MESubscription::class);

        return Inertia::render('Settings/Subscription', [
            'plans' => Service::all(),
            'subscription' => auth()->user()->activeSubscription(),
        ]);
    }

    public function checkout(Request $request): \Symfony\Component\HttpFoundation\Response
    {
        $productId = config('paypal.product_id');

        $plan = MEPlan::query()->firstWhere('type', 'site');

        abort_if(!$productId || !$plan, 403, "PayPal Product or Plan doesn't exist.");

        try {
            $response = $this->provider->addProductById(config('paypal.product_id'))
                ->addBillingPlanById($plan->paypal_id)
                ->setReturnAndCancelUrl(route('settings.subscription.success'), route('settings.subscription.cancel'))
                ->setupSubscription($request->user()->name, $request->user()->email, now()->addSeconds(10));

            if (array_key_exists('error', $response)) {
                session()->flash('message', [
                    'level' => 'danger',
                    'content' => $response['error']['message'],
                ]);

                return to_route('settings.subscription.edit');
            }

            return Inertia::location($response['links'][0]['href']);
        } catch (Throwable $exception) {
            session()->flash('message', [
                'level' => 'danger',
                'content' => $exception->getMessage(),
            ]);

            return to_route('settings.subscription.edit');
        }
    }

    public function success(Request $request): RedirectResponse
    {
        abort_if($request->user()->activeSubscription(), 403, 'You are already have a subscription.');
        abort_if(!$request->has('subscription_id'), 403, 'Bad request');

        try {
            $paypalSubscription = $this->provider->showSubscriptionDetails($request->get('subscription_id'));
            abort_if(empty($paypalSubscription['id']), 403, 'Bad request');

            $plan = MEPlan::query()
                ->firstWhere('paypal_id', $paypalSubscription['plan_id']);

            $subscription = MESubscription::updateOrCreate([
                'subscription_id' => $request->get('subscription_id'),
            ], [
                'user_id' => $request->user()->id,
                'plan_id' => $plan->id,
                'status' => strtolower($paypalSubscription['status']),
                'last_payment_date' => Carbon::parse($paypalSubscription['billing_info']['last_payment']['time']),
                'next_billing_date' => Carbon::parse($paypalSubscription['billing_info']['next_billing_time']),
                'amount' => $paypalSubscription['billing_info']['last_payment']['amount']['value'],
                'currency' => $paypalSubscription['billing_info']['last_payment']['amount']['currency_code'],
            ]);

            $data = [
                'name' => $request->user()->name,
                'username' => $request->user()->username,
                'plan' => $subscription->plan->name,
                'plan_price' => in_array(config('settings.currency', 'USD'), config('payment.currency_decimals')) ? $subscription->plan->price : intval($subscription->plan->price),
                'plan_frequency' => $subscription->plan->interval,
                'invoice_id' => $subscription->subscription_id,
                'receipt_id' => $subscription->id . '_RECEIPT',
                'currency' => trans('symbol.' . config('settings.currency', 'USD')),
                'amount' => $subscription->last_payment_date ? (in_array(config('settings.currency', 'USD'), config('payment.currency_decimals')) ? $subscription->amount : intval($subscription->amount)) : 0.00,
                'issued_date' => $subscription->last_payment_date ? Carbon::parse($subscription->last_payment_date)->format('M d, Y') : Carbon::parse($subscription->created_at)->format('M d, Y'),
                'next_billing' => Carbon::parse($subscription->next_billing_date)->format('M d, Y'),
                'payment_gate' => 'Paypal',
            ];

            dispatch(new SendEmail('subscriptionReceipt', $request->user()->email, $data));
        } catch (Throwable $exception) {
            abort($exception->getCode(), $exception->getMessage());
        }

        return redirect()->route('settings.subscription.edit');
    }

    public function checkoutCancel(Request $request): RedirectResponse
    {
        abort_if(!$request->has('subscription_id'), 403, 'Bad request');
        abort_if($request->user()->activeSubscription(), 403, 'You are already have a subscription.');

        try {
            $paypalSubscription = $this->provider->showSubscriptionDetails($request->get('subscription_id'));

            $plan = MEPlan::query()->firstWhere('paypal_id', $paypalSubscription['plan_id']);

            MESubscription::updateOrCreate([
                'subscription_id' => $request->get('subscription_id'),
            ], [
                'user_id' => $request->user()->id,
                'plan_id' => $plan->id,
                'status' => strtolower($paypalSubscription['status']),
            ]);

        } catch (Throwable $exception) {
            abort($exception->getCode(), $exception->getMessage());
        }

        return redirect()->route('settings.subscription.edit');
    }

    public function suspend(Request $request): RedirectResponse
    {
        $activeSubscription = $request->user()->activeSubscription(false);

        abort_if(!$activeSubscription, 403, "You don't have an active subscription.");

        try {
            $this->provider->suspendSubscription($activeSubscription->subscription_id, 'suspended by user');
            $paypalSubscription = $this->provider->showSubscriptionDetails($activeSubscription->subscription_id);
            $activeSubscription->update(['status' => strtolower($paypalSubscription['status'])]);
        } catch (Throwable $exception) {
            abort($exception->getCode(), $exception->getMessage());
        }

        return redirect()->back();
    }

    public function activate(Request $request): RedirectResponse
    {
        $suspendedSubscription = $request->user()->activeSubscription();

        abort_if($suspendedSubscription->status !== SubscriptionStatusEnum::Suspended, 403, "You don't have a suspended subscription.");

        try {
            $this->provider->activateSubscription($suspendedSubscription->subscription_id, 'activation by user');
            $paypalSubscription = $this->provider->showSubscriptionDetails($suspendedSubscription->subscription_id);
            $suspendedSubscription->update(['status' => strtolower($paypalSubscription['status'])]);
        } catch (Throwable $exception) {
            abort($exception->getCode(), $exception->getMessage());
        }

        return redirect()->back();
    }

    public function cancel(Request $request): RedirectResponse
    {
        $activeSubscription = $request->user()->activeSubscription();
        abort_if(!$activeSubscription, 403, "You don't have a subscription.");

        try {
            $this->provider->cancelSubscription($activeSubscription->subscription_id, 'cancellation by user');
            $paypalSubscription = $this->provider->showSubscriptionDetails($activeSubscription->subscription_id);
            $activeSubscription->update(['status' => strtolower($paypalSubscription['status'])]);
        } catch (Throwable $exception) {
            abort($exception->getCode(), $exception->getMessage());
        }

        return redirect()->back();
    }
}
