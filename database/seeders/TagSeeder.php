<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Tag;
use Illuminate\Database\Seeder;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tags = ['seminar', 'committee', 'competition', 'volunteer', 'workshop', 'webinar', 'teknologi', 'pendidikan', 'musik', 'olahraga','video game'];
        foreach ($tags as $tagName) {
            Tag::Create(['name' => $tagName]);
        }
    }
}

