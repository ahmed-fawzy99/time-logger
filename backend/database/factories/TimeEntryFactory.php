<?php

namespace Database\Factories;

use App\Models\TimeFrame;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TimeEntry>
 */
class TimeEntryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startTime = $this->faker->dateTimeBetween('-1 month', 'now');
        $endTime = (clone $startTime)->modify('+'.random_int(4, 10).' hours');

        return [
            // chose an existing timeframe or create a new one if none exist
            'time_frame_id' => TimeFrame::inRandomOrder()->first()?->id ?? TimeFrame::factory()->create()->id,
            'work_day' => $this->faker->date(),
            'start_time' => $startTime,
            'end_time' => $this->faker->passthrough($endTime),
            'description' => $this->faker->optional()->sentence(),
            'billable' => $this->faker->boolean(85),
        ];
    }
}
