<?php

namespace Database\Factories;

use App\Models\Faculty;
use App\Models\Major;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Major>
 */
class MajorFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => 'Prodi '.fake()->unique()->words(2, true),
            'faculty_id' => Faculty::factory(),
        ];
    }
}
