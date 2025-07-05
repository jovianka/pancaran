<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Event;
use App\Models\EventRole;
use App\Models\DetailSkp;
use App\Models\Certificate;
use Illuminate\Database\Seeder;

class CertificateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::pluck('id')->toArray();
        $events = Event::pluck('id')->toArray();
        $eventroles = EventRole::pluck('id')->toArray();
        $detailskps = DetailSkp::pluck('id')->toArray(); // ✅ ambil ID detail_skp

        Certificate::factory()->count(20)->create([
            'user_id' => fake()->randomElement($users),
            'event_id' => fake()->randomElement($events),
            'event_role_id' => fake()->randomElement($eventroles),
            'detail_skp_id' => fake()->randomElement($detailskps), // ✅ tambahkan ke seeder
        ]);
    }
}
