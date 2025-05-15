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
        $tags = ['Seminar', 'Committee', 'Competition', 'Volunteer'];
        foreach ($tags as $tagName) {
            Tag::Create(['name' => $tagName]);
        }
    }
}
