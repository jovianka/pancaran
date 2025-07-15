<?php

namespace Database\Factories;

use App\Models\Certificate;
use App\Models\User;
use App\Models\Event;
use App\Models\EventRole;
use App\Models\DetailSkp;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\Certificate>
 */
class CertificateFactory extends Factory
{
    protected $model = Certificate::class;

    public function definition(): array
    {
        return [
            'nomor_surat' => strtoupper($this->faker->bothify('SKP/###/???')),
            'file' => $this->faker->word() . '.pdf', // dummy filename
            'user_id' => User::inRandomOrder()->first()?->id ?? 1,
            'event_id' => Event::inRandomOrder()->first()?->id ?? 1,
            'event_role_id' => EventRole::inRandomOrder()->first()?->id ?? 1,
            'detail_skp_id' => DetailSkp::inRandomOrder()->first()->id,
        ];
    }
}
