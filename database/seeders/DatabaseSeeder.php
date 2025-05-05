<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Event;
use App\Models\EventDivision;
use App\Models\EventDivisionUser;
use App\Models\EventTimeline;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Database\Seeders\FacultyMajorSeeder;
use Database\Seeders\UserSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $facultyMajorSeeder = new FacultyMajorSeeder();
        $facultyMajorSeeder->run();

        // $userSeeder = new UserSeeder();
        // $userSeeder->run();
    }
}
