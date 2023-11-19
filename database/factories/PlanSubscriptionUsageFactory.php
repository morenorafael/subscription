<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class PlanSubscriptionUsageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->word,
            'subscription_id' => factory(PlanSubscription::class)->create()->id,
            'code' => $this->faker->word,
            'used' => rand(1, 50),
            'valid_until' => $this->faker->dateTime()
        ];
    }
}
