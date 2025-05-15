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
            'Fakultas Ilmu Budaya' => [
                'Sastra Indonesia',
                'Sastra Inggris',
                'Sastra Jepang',
                'Sastra Bali',
                'Ilmu Sejarah',
            ],
            'Fakultas Kedokteran' => [
                'Pendidikan Dokter',
                'Ilmu Keperawatan',
                'Psikologi',
                'Kesehatan Masyarakat',
            ],
            'Fakultas Hukum' => [
                'Ilmu Hukum',
            ],
            'Fakultas Teknik' => [
                'Teknik Sipil',
                'Teknik Elektro',
                'Teknik Mesin',
                'Teknik Arsitektur',
                'Teknik Industri',
            ],
            'Fakultas Pertanian' => [
                'Agroteknologi',
                'Agribisnis',
            ],
            'Fakultas Peternakan' => [
                'Ilmu Peternakan',
            ],
            'Fakultas Ekonomi dan Bisnis' => [
                'Manajemen',
                'Akuntansi',
                'Ilmu Ekonomi',
            ],
            'Fakultas MIPA' => [
                'Matematika',
                'Fisika',
                'Kimia',
                'Biologi',
            ],
            'Fakultas Kedokteran Hewan' => [
                'Pendidikan Dokter Hewan',
            ],
            'Fakultas Teknologi Pertanian' => [
                'Teknologi Pangan',
                'Teknik Pertanian',
            ],
            'Fakultas Ilmu Sosial dan Ilmu Politik' => [
                'Ilmu Komunikasi',
                'Sosiologi',
                'Ilmu Administrasi Negara',
                'Hubungan Internasional',
            ],
            'Fakultas Pariwisata' => [
                'Pariwisata',
                'Industri Perjalanan Wisata',
            ],
            'Fakultas Kelautan dan Perikanan' => [
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
    }
}
