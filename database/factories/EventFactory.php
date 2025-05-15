<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

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
        return [
            'name'=>fake()->sentence(2),
            'description'=>fake()->paragraph(4),
            'poster'=>'https://images.unsplash.com/photo-1584448141569-69f342da535c',
            'requirements'=>fake()->paragraph(2),
            'start_date'=>fake()->dateTimeBetween('-1 month', 'now'),
            'end_date'=>fake()->dateTimeBetween('now', '+1 month' ),
            'job_description'=>'https://docs.google.com/document/d/1WmGE0q_I9R5Ih0mQZvIf5-Ew1mFkkZJIpGYMcgEDlsI',
        ];
    }

}
