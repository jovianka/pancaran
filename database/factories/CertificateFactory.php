<?php

namespace Database\Factories;

use App\Models\Certificate;
use App\Models\DetailSkp;
use App\Models\Event;
use App\Models\EventRole;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Certificate>
 */
class CertificateFactory extends Factory
{
    protected $model = Certificate::class;

    public function definition(): array
    {
        return [
            'nomor_surat' => strtoupper($this->faker->bothify('SKP/###/???')),
            'file' => $this->faker->unique()->word().'.pdf',
            'user_id' => User::factory(),
            'event_id' => Event::factory(),
            'event_role_id' => function (array $attributes) {
                return EventRole::factory()->create([
                    'event_id' => $attributes['event_id'],
                ])->id;
            },
            'detail_skp_id' => DetailSkp::factory(),
        ];
    }
}
