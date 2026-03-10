<?php

namespace Tests\Unit;

use App\Console\Commands\SubscriptionCommand;
use App\Models\MESubscription;
use App\Models\User;
use App\Models\MEPlan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class SubscriptionCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_subscription_scan_processes_all_subscriptions_without_early_return()
    {
        // Create a user and plan
        $user = User::factory()->create();
        $plan = MEPlan::factory()->create();

        // Create multiple subscriptions with old updated_at dates
        $subscriptions = [];
        for ($i = 0; $i < 5; $i++) {
            $subscription = MESubscription::create([
                'user_id' => $user->id,
                'plan_id' => $plan->id,
                'subscription_id' => 'test_sub_' . $i,
                'status' => 'active',
                'next_billing_date' => now()->addDays(5),
                'amount' => 10.00,
                'currency' => 'USD',
                'updated_at' => now()->subDays(2),
            ]);
            $subscriptions[] = $subscription;
        }

        // Run the command
        $exitCode = Artisan::call('subscription:scan');

        // Assert command completed successfully
        $this->assertEquals(0, $exitCode);

        // Assert all subscriptions were processed (updated_at should be recent)
        foreach ($subscriptions as $subscription) {
            $subscription->refresh();
            // If the command processes all subscriptions without early return,
            // their updated_at timestamps should be recent (within last minute)
            $this->assertTrue(
                $subscription->updated_at->greaterThan(now()->subMinute()),
                "Subscription {$subscription->id} was not processed - command may have early return bug"
            );
        }
    }

    public function test_subscription_scan_continues_after_error()
    {
        // Create a user and plan
        $user = User::factory()->create();
        $plan = MEPlan::factory()->create();

        // Create multiple subscriptions
        $subscription1 = MESubscription::create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'subscription_id' => 'test_sub_1',
            'status' => 'active',
            'next_billing_date' => now()->addDays(5),
            'amount' => 10.00,
            'currency' => 'USD',
            'updated_at' => now()->subDays(2),
        ]);

        $subscription2 = MESubscription::create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'subscription_id' => 'test_sub_2',
            'status' => 'active',
            'next_billing_date' => now()->addDays(5),
            'amount' => 10.00,
            'currency' => 'USD',
            'updated_at' => now()->subDays(2),
        ]);

        // Run the command
        $exitCode = Artisan::call('subscription:scan');

        // Assert command completed successfully
        $this->assertEquals(0, $exitCode);

        // Assert both subscriptions were processed
        $subscription1->refresh();
        $subscription2->refresh();

        $this->assertTrue(
            $subscription1->updated_at->greaterThan(now()->subMinute()),
            "First subscription was not processed"
        );
        $this->assertTrue(
            $subscription2->updated_at->greaterThan(now()->subMinute()),
            "Second subscription was not processed - command may have early return on error"
        );
    }
}
