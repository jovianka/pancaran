<?php

namespace Database\Seeders;

use App\Models\User;
// use App\Models\Faculty;
// use App\Models\Major;
use App\Models\Event;
use App\Models\EventRole;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            // FacultySeeder::class,  // Seeding untuk tabel faculty dan major
            UserSeeder::class, // Seeding untuk tabel user
            RoleSeeder::class, //Seeding untuk tabel event_role
            // EventSeeder::class, //Seeding untuk tabel tag dan event
        ]);

        $events = Event::all();
        $users = User::all();

        // $users->each(function ($user) {
        //     //Mengisi nilai faculty_id dan major_id di tabel user

        //     $faculty = Faculty::inRandomOrder()->first();
        //     $major = Major::where('faculty_id', $faculty->id)->inRandomOrder()->first();

        //     $user->update([
        //         'faculty_id' => $faculty->id,
        //         'major_id' => $major->id,
        //     ]);

        // });


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



