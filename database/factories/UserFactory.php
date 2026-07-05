<?php

namespace Database\Factories;

use App\Models\Faculty;
use App\Models\Major;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->name(),
            'nim' => fake()->unique()->numerify('2308561###'),
            'email' => fake()->unique()->numerify('2308561######').'@student.unud.ac.id',
            'type' => 'student',
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'faculty_id' => Faculty::factory(),
            'major_id' => function (array $attributes) {
                return Major::factory()->create([
                    'faculty_id' => $attributes['faculty_id'],
                ])->id;
            },
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * Indicate that the user is an organization account (no NIM).
     */
    public function organization(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'organization',
            'nim' => null,
            'email' => fake()->unique()->numerify('org######').'@unud.ac.id',
        ]);
    }
}
