<?php

namespace Database\Factories;

use App\Models\Event;
use App\Models\EventRole;
use App\Models\EventUser;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<EventUser>
 */
class EventUserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'status' => 'active',
            'user_id' => User::factory(),
            'event_id' => Event::factory(),
            'event_role_id' => function (array $attributes) {
                return EventRole::factory()->create([
                    'event_id' => $attributes['event_id'],
                ])->id;
            },
        ];
    }
}
