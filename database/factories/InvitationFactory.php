<?php

namespace Database\Factories;

use App\Models\Event;
use App\Models\EventRole;
use App\Models\Invitation;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Invitation>
 */
class InvitationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(3),
            'body' => fake()->sentence(),
            'status' => 'pending',
            'event_id' => Event::factory(),
            'event_role_id' => function (array $attributes) {
                return EventRole::factory()->create([
                    'event_id' => $attributes['event_id'],
                ])->id;
            },
            'recipient_id' => User::factory(),
        ];
    }
}
