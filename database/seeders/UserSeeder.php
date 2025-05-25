<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Faculty;
use App\Models\Major;

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
            'type' => 'student',
            'nim' => fn () => fake()->unique()->numerify('2308561###'),
            'email' => fn () => fake()->unique()->numerify('2308561###'). '@student.unud.ac.id',
            'faculty_id' => fn()=>Faculty::inRandomOrder()->first()->id,
            'major_id' => fn(array $attributes) => Major::where('faculty_id',$attributes['faculty_id'])->inRandomOrder()->first()->id,
        ]);

        User::factory()->count(5)->create([
            'email' => fn () => fake()->unique()->numerify('2308561###'). '@unud.ac.id',
            'type' => 'organization',
            'nim' => null,
            'faculty_id' => fn()=>Faculty::inRandomOrder()->first()->id,
            'major_id' => fn(array $attributes) => Major::where('faculty_id',$attributes['faculty_id'])->inRandomOrder()->first()->id,
        ]);

    }
}
