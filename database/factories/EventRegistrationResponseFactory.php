<?php

namespace Database\Factories;

use App\Models\EventRegistration;
use App\Models\EventRegistrationResponse;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<EventRegistrationResponse>
 */
class EventRegistrationResponseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'date_submitted' => now(),
            'details' => [
                ['question_id' => 1, 'answer' => fake()->name()],
            ],
            'user_id' => User::factory(),
            'event_registration_id' => EventRegistration::factory(),
        ];
    }
}
