<?php

namespace Database\Factories;

use App\Models\Group;
use App\Models\MEPlan;
use Illuminate\Database\Eloquent\Factories\Factory;

class MEPlanFactory extends Factory
{
    protected $model = MEPlan::class;

    public function definition(): array
    {
        return [
            'type' => 'site',
            'paypal_id' => 'P-' . $this->faker->unique()->uuid(),
            'group_id' => Group::first()?->id ?? 1,
            'name' => $this->faker->words(3, true),
            'description' => $this->faker->sentence(),
            'price' => $this->faker->randomFloat(2, 5, 100),
            'percentage' => $this->faker->numberBetween(10, 90),
            'interval' => $this->faker->randomElement(['day', 'week', 'month', 'year']),
            'status' => 'active',
        ];
    }
}
