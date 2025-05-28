<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Event;
use App\Models\EventRole;
use Illuminate\Database\Seeder;
use App\Models\Tag;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class, //Seeding untuk tabel event_role
        ]);

        $events = Event::all();
        $tagcollection = Tag::all();
        $users = User::all();

        foreach ($events as $event){
            $tagnumber = rand(2, 5);
            $tags = $tagcollection->random($tagnumber);
            $user = $users->where('type', 'organization')->random();
            $role = EventRole::where('name', 'admin')->first();


            $user->events()->attach($event->id, [
                'event_role_id' => $role->id,
                'status' => 'active',
            ]);

            $tagIds = $tags->pluck('id');
            $event->tags()->attach($tagIds);

        };
    }
}



