<?php

namespace Database\Seeders;

use App\Models\Faculty;
use App\Models\Major;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory()->create([
            'nim' => null,
            'name' => 'Contoh UKM Universitas',
            'email' => 'ukm@unud.ac.id',
            'password' => Hash::make('123'),
            'avatar' => null,
            'faculty_id' => Faculty::where('name', '=', 'Any')->value('id'),
            'major_id' => Major::where('name', '=', 'Any')->value('id'),
            'type' => 'organization',
        ]);

        User::factory()->create([
            'nim' => '123456789',
            'name' => 'Contoh Mahasiswa',
            'email' => 'mahasiswa@student.unud.ac.id',
            'password' => Hash::make('123'),
            'avatar' => null,
            'faculty_id' => Faculty::where('name', '=', 'Matematika dan Ilmu Pengetahuan Alam')->value('id'),
            'major_id' => Major::where('name', '=', 'Matematika')->value('id'),
            'type' => 'student',
        ]);
    }
}
