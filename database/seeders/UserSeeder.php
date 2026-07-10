<?php

namespace Database\Seeders;

use App\Models\Faculty;
use App\Models\Major;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call(
            FacultySeeder::class,  // Seeding untuk tabel faculty dan major
        );

        User::factory()->count(30)->create([
            'faculty_id' => fn () => Faculty::inRandomOrder()->first()->id,
            'major_id' => fn (array $attributes) => Major::where('faculty_id', $attributes['faculty_id'])->inRandomOrder()->first()->id,
        ]);

        User::factory()->count(5)->organization()->create([
            'faculty_id' => fn () => Faculty::inRandomOrder()->first()->id,
            'major_id' => fn (array $attributes) => Major::where('faculty_id', $attributes['faculty_id'])->inRandomOrder()->first()->id,
        ]);

    }
}
