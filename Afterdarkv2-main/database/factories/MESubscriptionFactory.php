<?php

namespace Database\Factories;

use App\Models\MEPlan;
use App\Models\MESubscription;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class MESubscriptionFactory extends Factory
{
    protected $model = MESubscription::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'plan_id' => MEPlan::factory(),
            'subscription_id' => 'sub_' . $this->faker->unique()->uuid(),
            'status' => $this->faker->randomElement(['active', 'cancelled', 'expired']),
            'last_payment_date' => $this->faker->dateTimeBetween('-1 month', 'now'),
            'next_billing_date' => $this->faker->dateTimeBetween('now', '+1 month'),
            'amount' => $this->faker->randomFloat(2, 5, 100),
            'currency' => 'USD',
        ];
    }
}
