<?php

namespace Database\Factories;

use App\Models\Event;
use App\Models\Faculty;
use App\Models\Major;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Event>
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
        return [
            'name' => fake()->sentence(2),
            'description' => fake()->paragraph(4),
            'event_level' => 'university',
            'poster' => null,
            'requirements' => null,
            'start_date' => fake()->dateTimeBetween('-1 month', 'now'),
            'end_date' => fake()->dateTimeBetween('now', '+1 month'),
            'job_description' => fake()->word().'.pdf',
            'status' => 'ongoing',
            'parent_id' => null,
            'faculty_id' => Faculty::factory(),
            'major_id' => function (array $attributes) {
                return Major::factory()->create([
                    'faculty_id' => $attributes['faculty_id'],
                ])->id;
            },
        ];
    }
}
