<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Event;
use App\Models\Faculty;
use App\Models\Major;
use Illuminate\Support\Arr;
use Illuminate\Database\Seeder;

class EventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            TagSeeder::class,
            UserSeeder::class
        ]); //Seeding untuk tabel tag
        $faculties = Faculty::all();
        $majors = Major::all();
        $scopes = [
            'major',
            'faculty',
            'university',
            'regional',
            'national',
            'international',
        ];
        Event::factory()->count(20)->create(function () use ($scopes, $faculties, $majors) {
            $scope = Arr::random($scopes);
            $faculty = $faculties->reject(fn ($faculties) => $faculties->name === 'Any')->random();
            $major = $faculties->reject(fn ($majors) => $majors->name === 'Any')->random();

            return [
                'event_level' => $scope,
                'faculty_id' => $scope === 'faculty' ? $faculty->id : $faculties->filter(fn ($faculties) => $faculties->name === 'Any')->first()->id,
                'major_id' => $scope === 'major' ? $major->id : $majors->filter(fn ($majors) => $majors->name === 'Any')->first()->id,
            ];

        });

    }
}
