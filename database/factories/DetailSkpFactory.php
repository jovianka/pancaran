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
            'category'     => $this->faker->randomElement(['Pelatihan', 'Seminar', 'Workshop']),
            'description'  => $this->faker->sentence(),
            'role'         => $this->faker->randomElement(['Peserta', 'Pemateri', 'Panitia']),
            'event_level'  => $this->faker->randomElement(['Nasional', 'Internasional', 'Lokal']),
            'skp'          => $this->faker->numberBetween(1, 20),
        ];
    }
}
