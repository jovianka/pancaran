<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Faculty;
use App\Models\Major;

class FacultyMajorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Faculty::upsert([
            ['name' => 'Any'], // Khusus tingkat universitas
            ['name' => 'Ilmu Budaya'],
            ['name' => 'Kedokteran'],
            ['name' => 'Hukum'],
            ['name' => 'Teknik'],
            ['name' => 'Pertanian'],
            ['name' => 'Ekonomi dan Bisnis'],
            ['name' => 'Peternakan'],
            ['name' => 'Matematika dan Ilmu Pengetahuan Alam'],
            ['name' => 'Kedokteran Hewan'],
            ['name' => 'Teknologi Pertanian'],
            ['name' => 'Pariwisata'],
            ['name' => 'Ilmu Sosial dan Ilmu Politik'],
            ['name' => 'Kelautan dan Perikanan'],
        ],
            ['name']
        );

        // Khusus tingkat universitas
        Major::upsert([
            [
                'name' => 'Any',
                'faculty_id' => Faculty::where('name', '=', 'Any')->value('id'),
            ],
        ],
            ['name']
        );

        Major::upsert([
            [
                'name' => 'Antropologi Budaya',
                'faculty_id' => Faculty::where('name', '=', 'Ilmu Budaya')->value('id'),
            ],
            [
                'name' => 'Arkeologi',
                'faculty_id' => Faculty::where('name', '=', 'Ilmu Budaya')->value('id'),
            ],
            [
                'name' => 'Ilmu Sejarah',
                'faculty_id' => Faculty::where('name', '=', 'Ilmu Budaya')->value('id'),
            ],
            [
                'name' => 'Sastra Bali',
                'faculty_id' => Faculty::where('name', '=', 'Ilmu Budaya')->value('id'),
            ],
            [
                'name' => 'Sastra Daerah',
                'faculty_id' => Faculty::where('name', '=', 'Ilmu Budaya')->value('id'),
            ],
            [
                'name' => 'Sastra Indonesia',
                'faculty_id' => Faculty::where('name', '=', 'Ilmu Budaya')->value('id'),
            ],
            [
                'name' => 'Sastra Inggris',
                'faculty_id' => Faculty::where('name', '=', 'Ilmu Budaya')->value('id'),
            ],
            [
                'name' => 'Sastra Jawa Kuno',
                'faculty_id' => Faculty::where('name', '=', 'Ilmu Budaya')->value('id'),
            ],
            [
                'name' => 'Sastra Jepang',
                'faculty_id' => Faculty::where('name', '=', 'Ilmu Budaya')->value('id'),
            ],
        ],
            ['name']
        );

        Major::upsert([
            [
                'name' => 'Fisioterapi',
                'faculty_id' => Faculty::where('name', '=', 'Kedokteran')->value('id'),
            ],
            [
                'name' => 'Ilmu Keperawatan',
                'faculty_id' => Faculty::where('name', '=', 'Kedokteran')->value('id'),
            ],
            [
                'name' => 'Ilmu Kesehatan Masyarakat',
                'faculty_id' => Faculty::where('name', '=', 'Kedokteran')->value('id'),
            ],
            [
                'name' => 'Kedokteran Gigi',
                'faculty_id' => Faculty::where('name', '=', 'Kedokteran')->value('id'),
            ],
            [
                'name' => 'Pendidikan Dokter',
                'faculty_id' => Faculty::where('name', '=', 'Kedokteran')->value('id'),
            ],
            [
                'name' => 'Psikologi',
                'faculty_id' => Faculty::where('name', '=', 'Kedokteran')->value('id'),
            ],
        ],
            ['name']
        );

        Major::upsert([
            [
                'name' => 'Ilmu Hukum',
                'faculty_id' => Faculty::where('name', '=', 'Hukum')->value('id'),
            ],
        ],
            ['name']
        );

        Major::upsert([
            [
                'name' => 'Teknik Arsitektur',
                'faculty_id' => Faculty::where('name', '=', 'Teknik')->value('id'),
            ],
            [
                'name' => 'Teknik Elektro',
                'faculty_id' => Faculty::where('name', '=', 'Teknik')->value('id'),
            ],
            [
                'name' => 'Teknik Mesin',
                'faculty_id' => Faculty::where('name', '=', 'Teknik')->value('id'),
            ],
            [
                'name' => 'Teknik Sipil',
                'faculty_id' => Faculty::where('name', '=', 'Teknik')->value('id'),
            ],
            [
                'name' => 'Teknologi Informasi',
                'faculty_id' => Faculty::where('name', '=', 'Teknik')->value('id'),
            ],
        ],
            ['name']
        );

        Major::upsert([
            [
                'name' => 'Agribisnis',
                'faculty_id' => Faculty::where('name', '=', 'Pertanian')->value('id'),
            ],
            [
                'name' => 'Agroteknologi',
                'faculty_id' => Faculty::where('name', '=', 'Pertanian')->value('id'),
            ],
            [
                'name' => 'Arsitektur Pertamanan',
                'faculty_id' => Faculty::where('name', '=', 'Pertanian')->value('id'),
            ],
        ],
            ['name']
        );

        Major::upsert([
            [
                'name' => 'Akuntansi',
                'faculty_id' => Faculty::where('name', '=', 'Ekonomi dan Bisnis')->value('id'),
            ],
            [
                'name' => 'D3 Pemasaran',
                'faculty_id' => Faculty::where('name', '=', 'Ekonomi dan Bisnis')->value('id'),
            ],
            [
                'name' => 'Ekonomi Pembangunan',
                'faculty_id' => Faculty::where('name', '=', 'Ekonomi dan Bisnis')->value('id'),
            ],
            [
                'name' => 'Manajemen',
                'faculty_id' => Faculty::where('name', '=', 'Ekonomi dan Bisnis')->value('id'),
            ],
            [
                'name' => 'Profesi Akuntan',
                'faculty_id' => Faculty::where('name', '=', 'Ekonomi dan Bisnis')->value('id'),
            ],
        ],
            ['name']
        );

        Major::upsert([
            [
                'name' => 'Peternakan',
                'faculty_id' => Faculty::where('name', '=', 'Peternakan')->value('id'),
            ],
        ],
            ['name']
        );

        Major::upsert([
            [
                'name' => 'Biologi',
                'faculty_id' => Faculty::where('name', '=', 'Matematika dan Ilmu Pengetahuan Alam')->value('id'),
            ],
            [
                'name' => 'Farmasi',
                'faculty_id' => Faculty::where('name', '=', 'Matematika dan Ilmu Pengetahuan Alam')->value('id'),
            ],
            [
                'name' => 'Fisika',
                'faculty_id' => Faculty::where('name', '=', 'Matematika dan Ilmu Pengetahuan Alam')->value('id'),
            ],
            [
                'name' => 'Informatika',
                'faculty_id' => Faculty::where('name', '=', 'Matematika dan Ilmu Pengetahuan Alam')->value('id'),
            ],
            [
                'name' => 'Kimia',
                'faculty_id' => Faculty::where('name', '=', 'Matematika dan Ilmu Pengetahuan Alam')->value('id'),
            ],
            [
                'name' => 'Matematika',
                'faculty_id' => Faculty::where('name', '=', 'Matematika dan Ilmu Pengetahuan Alam')->value('id'),
            ],
            [
                'name' => 'Profesi Apoteker',
                'faculty_id' => Faculty::where('name', '=', 'Matematika dan Ilmu Pengetahuan Alam')->value('id'),
            ],
        ],
            ['name']
        );

        Major::upsert([
            [
                'name' => 'Kedokteran Hewan',
                'faculty_id' => Faculty::where('name', '=', 'Kedokteran Hewan')->value('id'),
            ],
        ],
            ['name']
        );

        Major::upsert([
            [
                'name' => 'Teknik Pertanian dan Biosistem',
                'faculty_id' => Faculty::where('name', '=', 'Teknologi Pertanian')->value('id'),
            ],
            [
                'name' => 'Teknologi Industri Pangan',
                'faculty_id' => Faculty::where('name', '=', 'Teknologi Pertanian')->value('id'),
            ],
            [
                'name' => 'Teknologi Pangan',
                'faculty_id' => Faculty::where('name', '=', 'Teknologi Pertanian')->value('id'),
            ],
        ],
            ['name']
        );

        Major::upsert([
            [
                'name' => 'D4 Pariwisata',
                'faculty_id' => Faculty::where('name', '=', 'Pariwisata')->value('id'),
            ],
            [
                'name' => 'Destinasi Pariwisata',
                'faculty_id' => Faculty::where('name', '=', 'Pariwisata')->value('id'),
            ],
            [
                'name' => 'Industri Perjalanan Wisata',
                'faculty_id' => Faculty::where('name', '=', 'Pariwisata')->value('id'),
            ],
        ],
            ['name']
        );

        Major::upsert([
            [
                'name' => 'Administrasi Negara',
                'faculty_id' => Faculty::where('name', '=', 'Ilmu Sosial dan Ilmu Politik')->value('id'),
            ],
            [
                'name' => 'D3 Perpustakaan',
                'faculty_id' => Faculty::where('name', '=', 'Ilmu Sosial dan Ilmu Politik')->value('id'),
            ],
            [
                'name' => 'Hubungan Internasional',
                'faculty_id' => Faculty::where('name', '=', 'Ilmu Sosial dan Ilmu Politik')->value('id'),
            ],
            [
                'name' => 'Ilmu Komunikasi',
                'faculty_id' => Faculty::where('name', '=', 'Ilmu Sosial dan Ilmu Politik')->value('id'),
            ],
            [
                'name' => 'Ilmu Politik',
                'faculty_id' => Faculty::where('name', '=', 'Ilmu Sosial dan Ilmu Politik')->value('id'),
            ],
            [
                'name' => 'Ilmu Sosiologi',
                'faculty_id' => Faculty::where('name', '=', 'Ilmu Sosial dan Ilmu Politik')->value('id'),
            ],
        ],
            ['name']
        );

        Major::upsert([
            [
                'name' => 'Ilmu Kelautan',
                'faculty_id' => Faculty::where('name', '=', 'Kelautan dan Perikanan')->value('id'),
            ],
            [
                'name' => 'Manajemen Sumber Daya Perairan',
                'faculty_id' => Faculty::where('name', '=', 'Kelautan dan Perikanan')->value('id'),
            ],
        ],
            ['name']
        );
    }
}
