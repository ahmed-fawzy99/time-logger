<?php

namespace Database\Factories;

use App\Enums\TimeFrameStatusEnum;
use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TimeFrame>
 */
class TimeFrameFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startDate = $this->faker->dateTimeBetween('-2 months', 'now');
        $endDate = (clone $startDate)->modify('+'.random_int(2, 8).' weeks');

        return [
            'project_id' => Project::inRandomOrder()->first()?->id ?? Project::factory()->create()->id,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'name' => $this->faker->sentence(3),
            'status' => $this->faker->randomElement([TimeFrameStatusEnum::IN_PROGRESS, TimeFrameStatusEnum::DONE]),
            'notes' => $this->faker->optional()->paragraph(),
        ];
    }
}
