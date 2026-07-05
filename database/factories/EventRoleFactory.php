<?php

namespace Database\Factories;

use App\Models\Event;
use App\Models\EventRole;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<EventRole>
 */
class EventRoleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->jobTitle(),
            'quota' => 10,
            'certificate_schema' => null,
            'certificate_basepdf' => null,
            'detail_skp_id' => null,
            'event_id' => Event::factory(),
        ];
    }

    /**
     * The event's Admin role.
     */
    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Admin',
            'quota' => 1,
        ]);
    }
}
