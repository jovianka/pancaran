<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class Certificate extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Certificate::factory()->count(20)->create();
    }
}
