<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Event;
use App\Models\Tag;
use Illuminate\Support\Arr;
use Illuminate\Database\Seeder;

class EventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call(TagSeeder::class); //Seeding untuk tabel tag
        $tags = Tag::all();
        $scopes = [
            'major',
            'faculty',
            'university',
            'regional',
            'national',
            'international',
        ];
        Event::factory()->count(20)->create([
            'tag_id' => fn() => $tags->random()->id,
            'event_level' => fn() => Arr::random($scopes),
        ]);

    }
}
