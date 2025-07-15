<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DetailSkp>
 */
class DetailSkpFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'category'     => $this->faker->randomElement(['seminar', 'committee', 'competition', 'volunteer', 'workshop', 'webinar', 'teknologi', 'pendidikan', 'musik', 'olahraga','video game']),
            'description'  => $this->faker->sentence(),
            'role'         => $this->faker->randomElement(['admin', 'ketua', 'wakil', 'sekretaris', 'bendahara', 'coordinator', 'peserta', 'anggota']),
            'event_level'  => $this->faker->randomElement(['major', 'faculty', 'university', 'regional', 'national', 'international']),
            'skp'          => $this->faker->numberBetween(1, 20),
        ];
    }
}
