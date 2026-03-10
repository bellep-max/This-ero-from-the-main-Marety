<?php

namespace App\Http\Controllers\Frontend\User;

use App\Enums\SubscriptionStatusEnum;
use App\Http\Controllers\Controller;
use App\Jobs\SendEmail;
use App\Models\MEPlan;
use App\Models\User;
use App\Models\MEUserSubscription;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Srmklive\PayPal\Services\PayPal;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class UserSubscriptionController extends Controller
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

    public function checkout(User $user, Request $request): Response
    {
        abort_if($request->user()->activeUserSubscription($user->id), 403, 'You are already subscribed to this user.');

        $productId = config('paypal.product_id');
        $plan = MEPlan::query()->firstWhere('type', 'user');

        abort_if(!$productId || !$plan, 403, 'PayPal Product or Plan does not exist.');

        try {
            $response = $this->provider
                ->addProductById(config('paypal.product_id'))
                ->addBillingPlanById($plan->paypal_id)
                ->setReturnAndCancelUrl(route('users.subscriptions.success', $user), route('users.subscriptions.checkout-cancel', $user))
                ->setupSubscription($request->user()->name, $request->user()->email, now()->addSeconds(10));

            return Inertia::location($response['links'][0]['href']);
        } catch (Throwable $exception) {
            abort($exception->getCode(), $exception->getMessage());

            return to_route('users.show', $user);
        }
    }

    public function success(User $user, Request $request): RedirectResponse
    {
        abort_if(!$request->has('subscription_id'), 403, 'Bad request');
        abort_if($request->user()->activeUserSubscription($user->id), 403, 'You are already subscribed to this user.');

        try {
            $paypalSubscription = $this->provider->showSubscriptionDetails($request->get('subscription_id'));

            abort_if(empty($paypalSubscription['id']), 403, 'Bad request');

            $plan = MEPlan::query()->firstWhere('paypal_id', $paypalSubscription['plan_id']);

            $subscription = MEUserSubscription::updateOrCreate([
                'subscription_id' => $request->get('subscription_id'),
            ], [
                'user_id' => $request->user()->id,
                'subscribed_user_id' => $user->id,
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
            abort(500, $exception->getMessage());
        }

        return to_route('users.show', $user);
    }

    public function checkoutCancel(User $user, Request $request): RedirectResponse
    {
        abort_if(!$request->has('subscription_id'), 403, 'Bad request');
        abort_if($request->user()->activeUserSubscription($user->id), 403, 'You are already subscribed to this user.');

        try {
            $paypalSubscription = $this->provider->showSubscriptionDetails($request->get('subscription_id'));

            $plan = MEPlan::query()
                ->firstWhere('paypal_id', $paypalSubscription['plan_id']);

            MEUserSubscription::updateOrCreate([
                'subscription_id' => $request->get('subscription_id'),
            ], [
                'plan_id' => $plan->id,
                'user_id' => $request->user()->id,
                'subscribed_user_id' => $user->id,
                'status' => strtolower($paypalSubscription['status']),
            ]);
        } catch (Throwable $exception) {
            abort($exception->getCode(), $exception->getMessage());
        }

        return to_route('users.show', $user);
    }

    public function suspend(User $user, Request $request): RedirectResponse
    {
        $activeSubscription = $request->user()->activeUserSubscription($user->id, false);
        abort_if($activeSubscription->pivot->status !== SubscriptionStatusEnum::Active, 403, "You don't have an active subscription for this user.");

        try {
            $this->provider->suspendSubscription($activeSubscription->pivot->subscription_id, 'Cancellation by user');
            $paypalSubscription = $this->provider->showSubscriptionDetails($activeSubscription->pivot->subscription_id);
            $request->user()->userSubscriptions()->updateExistingPivot($user->id, ['status' => strtolower($paypalSubscription['status'])]);
        } catch (Throwable $exception) {
            abort($exception->getCode(), $exception->getMessage());
        }

        return to_route('users.show', $user);
    }

    public function activate(User $user, Request $request): RedirectResponse
    {
        $activeSubscription = $request->user()->activeUserSubscription($user->id);
        abort_if($activeSubscription->pivot->status !== SubscriptionStatusEnum::Suspended, 403, "You don't have a suspended subscription for this user.");

        try {
            $this->provider->activateSubscription($activeSubscription->pivot->subscription_id, 'Cancellation by user');
            $paypalSubscription = $this->provider->showSubscriptionDetails($activeSubscription->pivot->subscription_id);
            $request->user()->userSubscriptions()->updateExistingPivot($user->id, ['status' => strtolower($paypalSubscription['status'])]);
        } catch (Throwable $exception) {
            abort($exception->getCode(), $exception->getMessage());
        }

        return to_route('users.show', $user);
    }

    public function cancel(User $user, Request $request): RedirectResponse
    {
        $activeSubscription = $request->user()->activeUserSubscription($user->id);
        abort_if(!$activeSubscription, 403, "You don't have a subscription for this user.");

        try {
            $this->provider->cancelSubscription($activeSubscription->pivot->subscription_id, 'Cancellation by user');
            $paypalSubscription = $this->provider->showSubscriptionDetails($activeSubscription->pivot->subscription_id);
            $request->user()->userSubscriptions()->updateExistingPivot($user->id, ['status' => strtolower($paypalSubscription['status'])]);
        } catch (Throwable $exception) {
            abort($exception->getCode(), $exception->getMessage());
        }

        return to_route('users.show', $user);
    }
}
