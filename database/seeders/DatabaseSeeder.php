<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Event;
use App\Models\EventRole;
use Illuminate\Database\Seeder;
use Database\Seeders\UserSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class, // Seeding untuk tabel user
            RoleSeeder::class, //Seeding untuk tabel event_role
        ]);

        $events = Event::all();
        $users = User::all();

        foreach ($events as $event){
            $user = $users->where('type', 'organization')->random();
            $role = EventRole::where('name', 'admin')->first();


            $user->events()->attach($event->id, [
                'event_role_id' => $role->id,
                'faculty_id' => $user->faculty_id,
                'major_id' => $user->major_id,
                'status' => 'active',
            ]);

        };
    }
}



