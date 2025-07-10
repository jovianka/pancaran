<?php

namespace Database\Seeders;
use App\Models\Major;
use App\Models\Faculty;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FacultySeeder extends Seeder
{
    //Seeding untuk tabel faculty dan major
    public function run(): void
    {
        $fakultasProdi = [
            'Ilmu Budaya' => [
                'Sastra Indonesia',
                'Sastra Inggris',
                'Sastra Jepang',
                'Sastra Bali',
                'Ilmu Sejarah',
            ],
            'Kedokteran' => [
                'Pendidikan Dokter',
                'Ilmu Keperawatan',
                'Psikologi',
                'Kesehatan Masyarakat',
            ],
            'Hukum' => [
                'Ilmu Hukum',
            ],
            'Teknik' => [
                'Teknik Sipil',
                'Teknik Elektro',
                'Teknik Mesin',
                'Teknik Arsitektur',
                'Teknik Industri',
            ],
            'Pertanian' => [
                'Agroteknologi',
                'Agribisnis',
            ],
            'Peternakan' => [
                'Ilmu Peternakan',
            ],
            'Ekonomi dan Bisnis' => [
                'Manajemen',
                'Akuntansi',
                'Ilmu Ekonomi',
            ],
            'Matematika dan Ilmu Pengetahuan Alam' => [
                'Matematika',
                'Fisika',
                'Kimia',
                'Biologi',
            ],
            'Kedokteran Hewan' => [
                'Kedokteran Hewan',
            ],
            'Teknologi Pertanian' => [
                'Teknologi Pangan',
                'Teknik Pertanian',
            ],
            'Ilmu Sosial dan Ilmu Politik' => [
                'Ilmu Komunikasi',
                'Sosiologi',
                'Ilmu Administrasi Negara',
                'Hubungan Internasional',
            ],
            'Pariwisata' => [
                'Pariwisata',
                'Industri Perjalanan Wisata',
            ],
            'Kelautan dan Perikanan' => [
                'Ilmu Kelautan',
                'Manajemen Sumber Daya Perairan',
            ],
        ];

        foreach ($fakultasProdi as $namaFakultas => $daftarProdi) {
            $faculty = Faculty::create(['name' => $namaFakultas]);

            foreach ($daftarProdi as $namaProdi) {
                Major::create([
                    'faculty_id' => $faculty->id,
                    'name' => $namaProdi,
                ]);
            }
        }

        $faculty = Faculty::create(['name' => 'Any']);
        Major::create([
                    'faculty_id' => $faculty->id,
                    'name' => 'Any',
                ]);

    }
}
