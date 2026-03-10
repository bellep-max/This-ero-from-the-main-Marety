<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\MESubscription;
use App\Models\MEUserSubscription;
use App\Models\WebhookEvent;
use Exception;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Srmklive\PayPal\Services\PayPal;
use Throwable;

class PayPalWebhook extends Controller
{
    public function handle(Request $request): Application|Response|ResponseFactory
    {
        if (!$this->isValidWebhook()) {
            return response('Invalid webhook', 400);
        }

        $data = $request->all();
        $eventId = $data['id'] ?? null;

        if (!$eventId) {
            return response('Missing event ID', 400);
        }

        $existingEvent = WebhookEvent::where('event_id', $eventId)->first();
        if ($existingEvent) {
            if ($existingEvent->status === 'processed') {
                Log::info("Webhook event {$eventId} already processed, skipping");

                return response('', 200);
            }
        } else {
            $existingEvent = WebhookEvent::create([
                'event_id' => $eventId,
                'provider' => 'paypal',
                'event_type' => $data['event_type'],
                'payload' => $data,
                'status' => 'pending',
            ]);
        }

        Log::info($data['event_type'], $data);

        try {
            // Handle the event based on its type
            switch ($data['event_type']) {
                case 'PAYMENT.SALE.COMPLETED':
                    $this->onPaymentCompleted($data);
                    break;
                case 'BILLING.SUBSCRIPTION.PAYMENT.FAILED':
                    $this->onPaymentFailed($data);
                    break;
            }

            $existingEvent->markAsProcessed();
        } catch (Throwable $e) {
            $existingEvent->markAsFailed($e->getMessage());
            Log::error('Webhook processing failed: ' . $e->getMessage(), ['exception' => $e]);

            return response('Webhook processing failed', 500);
        }

        return response('', 201);
    }

    protected function isValidWebhook(): bool
    {
        try {
            $headers = apache_request_headers();

            $body = file_get_contents('php://input');

            $data = $headers['Paypal-Transmission-Id'] . '|' . $headers['Paypal-Transmission-Time'] . '|' . config('paypal.webhook_id') . '|' . crc32($body);

            // load certificate and extract public key
            $pubKey = openssl_pkey_get_public(file_get_contents($headers['Paypal-Cert-Url']));
            $key = openssl_pkey_get_details($pubKey)['key'];

            // verify data against provided signature
            $result = openssl_verify($data, base64_decode($headers['Paypal-Transmission-Sig']), $key, 'sha256WithRSAEncryption');

            return boolval($result);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return false;
        }
    }

    protected function onPaymentCompleted(array $data): void
    {
        $subscriptionId = $data['resource']['billing_agreement_id'];
        $subscription = MESubscription::query()
            ->firstWhere('subscription_id', $subscriptionId) ?? MEUserSubscription::firstWhere('subscription_id', $subscriptionId);

        if ($subscription) {
            try {
                $provider = new PayPal;
                $provider->setApiCredentials(config('paypal'));
                $provider->getAccessToken();
                $paypalSubscription = $provider->showSubscriptionDetails($subscription->subscription_id);
                $subscription->update([
                    'last_payment_date' => $paypalSubscription['billing_info']['last_payment']['time'],
                    'next_billing_date' => $paypalSubscription['billing_info']['next_billing_time'],
                ]);
            } catch (Throwable $exception) {
                Log::error($exception->getMessage(), $exception->getTrace());
                abort($exception->getCode(), $exception->getMessage());
            }
        }
    }

    protected function onPaymentFailed(array $data): void
    {
        $subscription_id = $data['resource']['id'];

        Log::info($data['event_type'], $data['resource']);
    }
}
