<?php

namespace Database\Factories;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Preference>
 */
class PreferenceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Seed Jsonb
        return [
            'user_id' => User::inRandomOrder()->first()?->id ?? User::factory()->create()->id,
            'week_start' => strtolower($this->faker->randomElement(Carbon::getDays())),
            'currency' => $this->faker->currencyCode(),
            'hourly_rate' => $this->faker->randomNumber(3),
            'additional_properties' => [
                'roundDurationTo' => 0,
                'roundMethod' => $this->faker->randomElement(['up', 'down', 'nearest']),
                'invoiceName' => $this->faker->company(),
                'invoiceTitle' => $this->faker->title(),
                'invoiceAddress' => $this->faker->address(),
            ],
        ];
    }
}
