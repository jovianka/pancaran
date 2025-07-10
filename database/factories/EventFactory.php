<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Event>
 */
class EventFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $status = ['ongoing', 'finished'];
        return [
            'name' => fake()->sentence(2),
            'description' => fake()->paragraph(4),
            'poster' => null,
            'requirements' => fake()->paragraph(2),
            'start_date' => fake()->dateTimeBetween('-1 month', 'now'),
            'end_date' => fake()->dateTimeBetween('now', '+1 month'),
            'job_description' => '',
            'status' => 'ongoing'
            // 'status' => Arr::random($status), //for random status
        ];
    }

}
