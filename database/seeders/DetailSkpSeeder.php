<?php

namespace Database\Seeders;

use App\Models\DetailSkp;
use App\Models\EventRole;
use Illuminate\Database\Seeder;

class DetailSkpSeeder extends Seeder
{
    /**
     * Seed detail_skp.
     */
    public function run(): void
    {
        // BIDANG PENALARAN/ILMIAH
            // Lomba Tingkat Prodi
            DetailSkp::upsert([
                [
                    'category' => 'Bidang Penalaran/Ilmiah',
                    'description' => 'Mengikuti Lomba Tingkat Prodi',
                    'role' => 'Juara 1',
                    'event_level' => 'major',
                    'skp' => 8
                ],
                [
                    'category' => 'Bidang Penalaran/Ilmiah',
                    'description' => 'Mengikuti Lomba Tingkat Prodi',
                    'role' => 'Juara 2',
                    'event_level' => 'major',
                    'skp' => 5
                ],
                [
                    'category' => 'Bidang Penalaran/Ilmiah',
                    'description' => 'Mengikuti Lomba Tingkat Prodi',
                    'role' => 'Juara 3',
                    'event_level' => 'major',
                    'skp' => 3
                ],
                [
                    'category' => 'Bidang Penalaran/Ilmiah',
                    'description' => 'Mengikuti Lomba Tingkat Prodi',
                    'role' => 'Finalis',
                    'event_level' => 'major',
                    'skp' => 1
                ],
            ], 'id');

            // Lomba Tingkat Fakultas
            DetailSkp::upsert([
                [
                    'category' => 'Bidang Penalaran/Ilmiah',
                    'description' => 'Mengikuti Lomba Tingkat Fakultas',
                    'role' => 'Juara 1',
                    'event_level' => 'faculty',
                    'skp' => 8
                ],
                [
                    'category' => 'Bidang Penalaran/Ilmiah',
                    'description' => 'Mengikuti Lomba Tingkat Fakultas',
                    'role' => 'Juara 2',
                    'event_level' => 'faculty',
                    'skp' => 5
                ],
                [
                    'category' => 'Bidang Penalaran/Ilmiah',
                    'description' => 'Mengikuti Lomba Tingkat Fakultas',
                    'role' => 'Juara 3',
                    'event_level' => 'faculty',
                    'skp' => 3
                ],
                [
                    'category' => 'Bidang Penalaran/Ilmiah',
                    'description' => 'Mengikuti Lomba Tingkat Fakultas',
                    'role' => 'Finalis',
                    'event_level' => 'faculty',
                    'skp' => 1
                ],
            ], 'id');

            // Lomba Tingkat Universitas
            DetailSkp::upsert([
                [
                    'category' => 'Bidang Penalaran/Ilmiah',
                    'description' => 'Mengikuti Lomba Tingkat Universitas',
                    'role' => 'Juara 1',
                    'event_level' => 'university',
                    'skp' => 8
                ],
                [
                    'category' => 'Bidang Penalaran/Ilmiah',
                    'description' => 'Mengikuti Lomba Tingkat Universitas',
                    'role' => 'Juara 2',
                    'event_level' => 'university',
                    'skp' => 5
                ],
                [
                    'category' => 'Bidang Penalaran/Ilmiah',
                    'description' => 'Mengikuti Lomba Tingkat Universitas',
                    'role' => 'Juara 3',
                    'event_level' => 'university',
                    'skp' => 3
                ],
                [
                    'category' => 'Bidang Penalaran/Ilmiah',
                    'description' => 'Mengikuti Lomba Tingkat Universitas',
                    'role' => 'Finalis',
                    'event_level' => 'university',
                    'skp' => 1
                ],
            ], 'id');

            // Lomba Tingkat Regional
            DetailSkp::upsert([
                [
                    'category' => 'Bidang Penalaran/Ilmiah',
                    'description' => 'Mengikuti Lomba Tingkat Regional (Provinsi)',
                    'role' => 'Juara 1',
                    'event_level' => 'regional',
                    'skp' => 15
                ],
                [
                    'category' => 'Bidang Penalaran/Ilmiah',
                    'description' => 'Mengikuti Lomba Tingkat Regional (Provinsi)',
                    'role' => 'Juara 2',
                    'event_level' => 'regional',
                    'skp' => 12
                ],
                [
                    'category' => 'Bidang Penalaran/Ilmiah',
                    'description' => 'Mengikuti Lomba Tingkat Regional (Provinsi)',
                    'role' => 'Juara 3',
                    'event_level' => 'regional',
                    'skp' => 10
                ],
                [
                    'category' => 'Bidang Penalaran/Ilmiah',
                    'description' => 'Mengikuti Lomba Tingkat Regional (Provinsi)',
                    'role' => 'Finalis',
                    'event_level' => 'regional',
                    'skp' => 5
                ],
            ], 'id');

            // Lomba Tingkat Nasional
            DetailSkp::upsert([
                [
                    'category' => 'Bidang Penalaran/Ilmiah',
                    'description' => 'Mengikuti Lomba Tingkat Nasional',
                    'role' => 'Juara 1',
                    'event_level' => 'national',
                    'skp' => 20
                ],
                [
                    'category' => 'Bidang Penalaran/Ilmiah',
                    'description' => 'Mengikuti Lomba Tingkat Nasional',
                    'role' => 'Juara 2',
                    'event_level' => 'national',
                    'skp' => 17
                ],
                [
                    'category' => 'Bidang Penalaran/Ilmiah',
                    'description' => 'Mengikuti Lomba Tingkat Nasional',
                    'role' => 'Juara 3',
                    'event_level' => 'national',
                    'skp' => 14
                ],
                [
                    'category' => 'Bidang Penalaran/Ilmiah',
                    'description' => 'Mengikuti Lomba Tingkat Nasional',
                    'role' => 'Finalis',
                    'event_level' => 'national',
                    'skp' => 8
                ],
            ], 'id');

            // Lomba Tingkat International
            DetailSkp::upsert([
                [
                    'category' => 'Bidang Penalaran/Ilmiah',
                    'description' => 'Mengikuti Lomba Tingkat Internasional',
                    'role' => 'Juara 1',
                    'event_level' => 'international',
                    'skp' => 25
                ],
                [
                    'category' => 'Bidang Penalaran/Ilmiah',
                    'description' => 'Mengikuti Lomba Tingkat Internasional',
                    'role' => 'Juara 2',
                    'event_level' => 'international',
                    'skp' => 22
                ],
                [
                    'category' => 'Bidang Penalaran/Ilmiah',
                    'description' => 'Mengikuti Lomba Tingkat Internasional',
                    'role' => 'Juara 3',
                    'event_level' => 'international',
                    'skp' => 18
                ],
                [
                    'category' => 'Bidang Penalaran/Ilmiah',
                    'description' => 'Mengikuti Lomba Tingkat Internasional',
                    'role' => 'Finalis',
                    'event_level' => 'international',
                    'skp' => 10
                ],
            ], 'id');

            // Kreativitas Mahasiswa (PKM)
            DetailSkp::upsert([
                [
                    'category' => 'Bidang Penalaran/Ilmiah',
                    'description' => 'Mengikuti Program Kreativitas Mahasiswa',
                    'role' => 'Juara PIMNAS (Ketua)',
                    'event_level' => 'national',
                    'skp' => 40
                ],
                [
                    'category' => 'Bidang Penalaran/Ilmiah',
                    'description' => 'Mengikuti Program Kreativitas Mahasiswa',
                    'role' => 'Juara PIMNAS (Anggota)',
                    'event_level' => 'national',
                    'skp' => 35
                ],
                [
                    'category' => 'Bidang Penalaran/Ilmiah',
                    'description' => 'Mengikuti Program Kreativitas Mahasiswa',
                    'role' => 'Lolos PIMNAS (Ketua)',
                    'event_level' => 'national',
                    'skp' => 35
                ],
                [
                    'category' => 'Bidang Penalaran/Ilmiah',
                    'description' => 'Mengikuti Program Kreativitas Mahasiswa',
                    'role' => 'Lolos PIMNAS (Anggota)',
                    'event_level' => 'national',
                    'skp' => 30
                ],
                [
                    'category' => 'Bidang Penalaran/Ilmiah',
                    'description' => 'Mengikuti Program Kreativitas Mahasiswa',
                    'role' => 'Lolos Didanai (Ketua)',
                    'event_level' => 'university',
                    'skp' => 20
                ],
                [
                    'category' => 'Bidang Penalaran/Ilmiah',
                    'description' => 'Mengikuti Program Kreativitas Mahasiswa',
                    'role' => 'Lolos Didanai (Anggota)',
                    'event_level' => 'university',
                    'skp' => 15
                ],
                [
                    'category' => 'Bidang Penalaran/Ilmiah',
                    'description' => 'Mengikuti Program Kreativitas Mahasiswa',
                    'role' => 'Upload PKM (Ketua)',
                    'event_level' => 'university',
                    'skp' => 15
                ],
                [
                    'category' => 'Bidang Penalaran/Ilmiah',
                    'description' => 'Mengikuti Program Kreativitas Mahasiswa',
                    'role' => 'Upload PKM (Anggota)',
                    'event_level' => 'university',
                    'skp' => 10
                ],
            ], 'id');

            // Seminar/Workshop/Pelatihan
            DetailSkp::upsert([
                [
                    'category' => 'Bidang Penalaran/Ilmiah',
                    'description' => 'Mengikuti Seminar/Workshop/Pelatihan',
                    'role' => 'Pembicara',
                    'event_level' => 'international',
                    'skp' => 25
                ],
                [
                    'category' => 'Bidang Penalaran/Ilmiah',
                    'description' => 'Mengikuti Seminar/Workshop/Pelatihan',
                    'role' => 'Peserta',
                    'event_level' => 'international',
                    'skp' => 5
                ],
                [
                    'category' => 'Bidang Penalaran/Ilmiah',
                    'description' => 'Mengikuti Seminar/Workshop/Pelatihan',
                    'role' => 'Pembicara',
                    'event_level' => 'national',
                    'skp' => 20
                ],
                [
                    'category' => 'Bidang Penalaran/Ilmiah',
                    'description' => 'Mengikuti Seminar/Workshop/Pelatihan',
                    'role' => 'Peserta',
                    'event_level' => 'national',
                    'skp' => 5
                ],
                [
                    'category' => 'Bidang Penalaran/Ilmiah',
                    'description' => 'Mengikuti Seminar/Workshop/Pelatihan',
                    'role' => 'Pembicara',
                    'event_level' => 'regional',
                    'skp' => 15
                ],
                [
                    'category' => 'Bidang Penalaran/Ilmiah',
                    'description' => 'Mengikuti Seminar/Workshop/Pelatihan',
                    'role' => 'Peserta',
                    'event_level' => 'regional',
                    'skp' => 5
                ],
                [
                    'category' => 'Bidang Penalaran/Ilmiah',
                    'description' => 'Mengikuti Seminar/Workshop/Pelatihan',
                    'role' => 'Pembicara',
                    'event_level' => 'university',
                    'skp' => 10
                ],
                [
                    'category' => 'Bidang Penalaran/Ilmiah',
                    'description' => 'Mengikuti Seminar/Workshop/Pelatihan',
                    'role' => 'Peserta',
                    'event_level' => 'university',
                    'skp' => 3
                ],
                [
                    'category' => 'Bidang Penalaran/Ilmiah',
                    'description' => 'Mengikuti Seminar/Workshop/Pelatihan',
                    'role' => 'Pembicara',
                    'event_level' => 'faculty',
                    'skp' => 10
                ],
                [
                    'category' => 'Bidang Penalaran/Ilmiah',
                    'description' => 'Mengikuti Seminar/Workshop/Pelatihan',
                    'role' => 'Peserta',
                    'event_level' => 'faculty',
                    'skp' => 3
                ],
                [
                    'category' => 'Bidang Penalaran/Ilmiah',
                    'description' => 'Mengikuti Seminar/Workshop/Pelatihan',
                    'role' => 'Pembicara',
                    'event_level' => 'major',
                    'skp' => 10
                ],
                [
                    'category' => 'Bidang Penalaran/Ilmiah',
                    'description' => 'Mengikuti Seminar/Workshop/Pelatihan',
                    'role' => 'Peserta',
                    'event_level' => 'major',
                    'skp' => 3
                ],
            ], 'id');



        // BIDANG MINAT DAN BAKAT
            // Lomba Tingkat Prodi
            DetailSkp::upsert([
                [
                    'category' => 'Bidang Minat dan Bakat',
                    'description' => 'Mengikuti Lomba Olahraga/Seni Tingkat Prodi',
                    'role' => 'Juara 1',
                    'event_level' => 'major',
                    'skp' => 8
                ],
                [
                    'category' => 'Bidang Minat dan Bakat',
                    'description' => 'Mengikuti Lomba Olahraga/Seni Tingkat Prodi',
                    'role' => 'Juara 2',
                    'event_level' => 'major',
                    'skp' => 5
                ],
                [
                    'category' => 'Bidang Minat dan Bakat',
                    'description' => 'Mengikuti Lomba Olahraga/Seni Tingkat Prodi',
                    'role' => 'Juara 3',
                    'event_level' => 'major',
                    'skp' => 3
                ],
                [
                    'category' => 'Bidang Minat dan Bakat',
                    'description' => 'Mengikuti Lomba Olahraga/Seni Tingkat Prodi',
                    'role' => 'Finalis',
                    'event_level' => 'major',
                    'skp' => 1
                ],
            ], 'id');

            // Lomba Tingkat Fakultas
            DetailSkp::upsert([
                [
                    'category' => 'Bidang Minat dan Bakat',
                    'description' => 'Mengikuti Lomba Olahraga/Seni Tingkat Fakultas',
                    'role' => 'Juara 1',
                    'event_level' => 'faculty',
                    'skp' => 8
                ],
                [
                    'category' => 'Bidang Minat dan Bakat',
                    'description' => 'Mengikuti Lomba Olahraga/Seni Tingkat Fakultas',
                    'role' => 'Juara 2',
                    'event_level' => 'faculty',
                    'skp' => 5
                ],
                [
                    'category' => 'Bidang Minat dan Bakat',
                    'description' => 'Mengikuti Lomba Olahraga/Seni Tingkat Fakultas',
                    'role' => 'Juara 3',
                    'event_level' => 'faculty',
                    'skp' => 3
                ],
                [
                    'category' => 'Bidang Minat dan Bakat',
                    'description' => 'Mengikuti Lomba Olahraga/Seni Tingkat Fakultas',
                    'role' => 'Finalis',
                    'event_level' => 'faculty',
                    'skp' => 1
                ],
            ], 'id');

            // Lomba Tingkat Universitas
            DetailSkp::upsert([
                [
                    'category' => 'Bidang Minat dan Bakat',
                    'description' => 'Mengikuti Lomba Olahraga/Seni Tingkat Universitas',
                    'role' => 'Juara 1',
                    'event_level' => 'university',
                    'skp' => 8
                ],
                [
                    'category' => 'Bidang Minat dan Bakat',
                    'description' => 'Mengikuti Lomba Olahraga/Seni Tingkat Universitas',
                    'role' => 'Juara 2',
                    'event_level' => 'university',
                    'skp' => 5
                ],
                [
                    'category' => 'Bidang Minat dan Bakat',
                    'description' => 'Mengikuti Lomba Olahraga/Seni Tingkat Universitas',
                    'role' => 'Juara 3',
                    'event_level' => 'university',
                    'skp' => 3
                ],
                [
                    'category' => 'Bidang Minat dan Bakat',
                    'description' => 'Mengikuti Lomba Olahraga/Seni Tingkat Universitas',
                    'role' => 'Finalis',
                    'event_level' => 'university',
                    'skp' => 1
                ],
            ], 'id');

            // Lomba Tingkat Regional
            DetailSkp::upsert([
                [
                    'category' => 'Bidang Minat dan Bakat',
                    'description' => 'Mengikuti Lomba Olahraga/Seni Tingkat Regional (Provinsi)',
                    'role' => 'Juara 1',
                    'event_level' => 'regional',
                    'skp' => 15
                ],
                [
                    'category' => 'Bidang Minat dan Bakat',
                    'description' => 'Mengikuti Lomba Olahraga/Seni Tingkat Regional (Provinsi)',
                    'role' => 'Juara 2',
                    'event_level' => 'regional',
                    'skp' => 12
                ],
                [
                    'category' => 'Bidang Minat dan Bakat',
                    'description' => 'Mengikuti Lomba Olahraga/Seni Tingkat Regional (Provinsi)',
                    'role' => 'Juara 3',
                    'event_level' => 'regional',
                    'skp' => 10
                ],
                [
                    'category' => 'Bidang Minat dan Bakat',
                    'description' => 'Mengikuti Lomba Olahraga/Seni Tingkat Regional (Provinsi)',
                    'role' => 'Finalis',
                    'event_level' => 'regional',
                    'skp' => 5
                ],
            ], 'id');

            // Lomba Tingkat Nasional
            DetailSkp::upsert([
                [
                    'category' => 'Bidang Minat dan Bakat',
                    'description' => 'Mengikuti Lomba Olahraga/Seni Tingkat Nasional',
                    'role' => 'Juara 1',
                    'event_level' => 'national',
                    'skp' => 20
                ],
                [
                    'category' => 'Bidang Minat dan Bakat',
                    'description' => 'Mengikuti Lomba Olahraga/Seni Tingkat Nasional',
                    'role' => 'Juara 2',
                    'event_level' => 'national',
                    'skp' => 17
                ],
                [
                    'category' => 'Bidang Minat dan Bakat',
                    'description' => 'Mengikuti Lomba Olahraga/Seni Tingkat Nasional',
                    'role' => 'Juara 3',
                    'event_level' => 'national',
                    'skp' => 14
                ],
                [
                    'category' => 'Bidang Minat dan Bakat',
                    'description' => 'Mengikuti Lomba Olahraga/Seni Tingkat Nasional',
                    'role' => 'Finalis',
                    'event_level' => 'national',
                    'skp' => 8
                ],
            ], 'id');

            // Lomba Tingkat International
            DetailSkp::upsert([
                [
                    'category' => 'Bidang Minat dan Bakat',
                    'description' => 'Mengikuti Lomba Olahraga/Seni Tingkat Internasional',
                    'role' => 'Juara 1',
                    'event_level' => 'international',
                    'skp' => 25
                ],
                [
                    'category' => 'Bidang Minat dan Bakat',
                    'description' => 'Mengikuti Lomba Olahraga/Seni Tingkat Internasional',
                    'role' => 'Juara 2',
                    'event_level' => 'international',
                    'skp' => 22
                ],
                [
                    'category' => 'Bidang Minat dan Bakat',
                    'description' => 'Mengikuti Lomba Olahraga/Seni Tingkat Internasional',
                    'role' => 'Juara 3',
                    'event_level' => 'international',
                    'skp' => 18
                ],
                [
                    'category' => 'Bidang Minat dan Bakat',
                    'description' => 'Mengikuti Lomba Olahraga/Seni Tingkat Internasional',
                    'role' => 'Finalis',
                    'event_level' => 'international',
                    'skp' => 10
                ],
            ], 'id');



        // BIDANG ORGANISASI DAN KEPANITIAA
            // Pengurus BEM-PM
            DetailSkp::upsert([
                [
                    'category' => 'Bidang Organisasi dan Kepanitiaan',
                    'description' => 'Pengurus BEM-PM',
                    'role' => 'Presiden',
                    'event_level' => 'university',
                    'skp' => 20
                ],
                [
                    'category' => 'Bidang Organisasi dan Kepanitiaan',
                    'description' => 'Pengurus BEM-PM',
                    'role' => 'Wakil Presiden',
                    'event_level' => 'university',
                    'skp' => 18
                ],
                [
                    'category' => 'Bidang Organisasi dan Kepanitiaan',
                    'description' => 'Pengurus BEM-PM',
                    'role' => 'Sekretaris',
                    'event_level' => 'university',
                    'skp' => 17
                ],
                [
                    'category' => 'Bidang Organisasi dan Kepanitiaan',
                    'description' => 'Pengurus BEM-PM',
                    'role' => 'Bendahara',
                    'event_level' => 'university',
                    'skp' => 17
                ],
                [
                    'category' => 'Bidang Organisasi dan Kepanitiaan',
                    'description' => 'Pengurus BEM-PM',
                    'role' => 'Menteri',
                    'event_level' => 'university',
                    'skp' => 15
                ],
                [
                    'category' => 'Bidang Organisasi dan Kepanitiaan',
                    'description' => 'Pengurus BEM-PM',
                    'role' => 'Wakil Menteri',
                    'event_level' => 'university',
                    'skp' => 14
                ],
                [
                    'category' => 'Bidang Organisasi dan Kepanitiaan',
                    'description' => 'Pengurus BEM-PM',
                    'role' => 'Anggota',
                    'event_level' => 'university',
                    'skp' => 10
                ],
            ], 'id');

            // Pengurus DPM
            DetailSkp::upsert([
                [
                    'category' => 'Bidang Organisasi dan Kepanitiaan',
                    'description' => 'Pengurus DPM',
                    'role' => 'Ketua',
                    'event_level' => 'university',
                    'skp' => 15
                ],
                [
                    'category' => 'Bidang Organisasi dan Kepanitiaan',
                    'description' => 'Pengurus DPM',
                    'role' => 'Wakil Ketua',
                    'event_level' => 'university',
                    'skp' => 13
                ],
                [
                    'category' => 'Bidang Organisasi dan Kepanitiaan',
                    'description' => 'Pengurus DPM',
                    'role' => 'Sekretaris',
                    'event_level' => 'university',
                    'skp' => 12
                ],
                [
                    'category' => 'Bidang Organisasi dan Kepanitiaan',
                    'description' => 'Pengurus DPM',
                    'role' => 'Bendahara',
                    'event_level' => 'university',
                    'skp' => 12
                ],
                [
                    'category' => 'Bidang Organisasi dan Kepanitiaan',
                    'description' => 'Pengurus DPM',
                    'role' => 'Anggota',
                    'event_level' => 'university',
                    'skp' => 10
                ],
            ], 'id');

            // Pengurus BEM Fakultas
            DetailSkp::upsert([
                [
                    'category' => 'Bidang Organisasi dan Kepanitiaan',
                    'description' => 'Pengurus BEM Fakultas',
                    'role' => 'Gubernur',
                    'event_level' => 'faculty',
                    'skp' => 15
                ],
                [
                    'category' => 'Bidang Organisasi dan Kepanitiaan',
                    'description' => 'Pengurus BEM Fakultas',
                    'role' => 'Wakil Gubernur',
                    'event_level' => 'faculty',
                    'skp' => 13
                ],
                [
                    'category' => 'Bidang Organisasi dan Kepanitiaan',
                    'description' => 'Pengurus BEM Fakultas',
                    'role' => 'Sekretaris',
                    'event_level' => 'faculty',
                    'skp' => 12
                ],
                [
                    'category' => 'Bidang Organisasi dan Kepanitiaan',
                    'description' => 'Pengurus BEM Fakultas',
                    'role' => 'Bendahara',
                    'event_level' => 'faculty',
                    'skp' => 12
                ],
                [
                    'category' => 'Bidang Organisasi dan Kepanitiaan',
                    'description' => 'Pengurus BEM Fakultas',
                    'role' => 'Ketua Bidang',
                    'event_level' => 'faculty',
                    'skp' => 10
                ],
                [
                    'category' => 'Bidang Organisasi dan Kepanitiaan',
                    'description' => 'Pengurus BEM Fakultas',
                    'role' => 'Wakil Ketua Bidang',
                    'event_level' => 'faculty',
                    'skp' => 9
                ],
                [
                    'category' => 'Bidang Organisasi dan Kepanitiaan',
                    'description' => 'Pengurus BEM Fakultas',
                    'role' => 'Anggota',
                    'event_level' => 'faculty',
                    'skp' => 5
                ],
            ], 'id');

            // Pengurus BPM
            DetailSkp::upsert([
                [
                    'category' => 'Bidang Organisasi dan Kepanitiaan',
                    'description' => 'Pengurus BPM',
                    'role' => 'Ketua',
                    'event_level' => 'faculty',
                    'skp' => 10
                ],
                [
                    'category' => 'Bidang Organisasi dan Kepanitiaan',
                    'description' => 'Pengurus BPM',
                    'role' => 'Wakil Ketua',
                    'event_level' => 'faculty',
                    'skp' => 8
                ],
                [
                    'category' => 'Bidang Organisasi dan Kepanitiaan',
                    'description' => 'Pengurus BPM',
                    'role' => 'Sekretaris',
                    'event_level' => 'faculty',
                    'skp' => 7
                ],
                [
                    'category' => 'Bidang Organisasi dan Kepanitiaan',
                    'description' => 'Pengurus BPM',
                    'role' => 'Bendahara',
                    'event_level' => 'faculty',
                    'skp' => 7
                ],
                [
                    'category' => 'Bidang Organisasi dan Kepanitiaan',
                    'description' => 'Pengurus BPM',
                    'role' => 'Anggota',
                    'event_level' => 'faculty',
                    'skp' => 5
                ],
            ], 'id');

            // Pengurus Himpunan Mahasiswa
            DetailSkp::upsert([
                [
                    'category' => 'Bidang Organisasi dan Kepanitiaan',
                    'description' => 'Pengurus Himpunan Mahasiswa',
                    'role' => 'Ketua',
                    'event_level' => 'major',
                    'skp' => 10
                ],
                [
                    'category' => 'Bidang Organisasi dan Kepanitiaan',
                    'description' => 'Pengurus Himpunan Mahasiswa',
                    'role' => 'Wakil Ketua',
                    'event_level' => 'major',
                    'skp' => 8
                ],
                [
                    'category' => 'Bidang Organisasi dan Kepanitiaan',
                    'description' => 'Pengurus Himpunan Mahasiswa',
                    'role' => 'Sekretaris',
                    'event_level' => 'major',
                    'skp' => 7
                ],
                [
                    'category' => 'Bidang Organisasi dan Kepanitiaan',
                    'description' => 'Pengurus Himpunan Mahasiswa',
                    'role' => 'Bendahara',
                    'event_level' => 'major',
                    'skp' => 7
                ],
                [
                    'category' => 'Bidang Organisasi dan Kepanitiaan',
                    'description' => 'Pengurus Himpunan Mahasiswa',
                    'role' => 'Anggota',
                    'event_level' => 'major',
                    'skp' => 5
                ],
            ], 'id');

            // Pengurus UKM
            DetailSkp::upsert([
                [
                    'category' => 'Bidang Organisasi dan Kepanitiaan',
                    'description' => 'Pengurus UKM',
                    'role' => 'Ketua',
                    'event_level' => 'university',
                    'skp' => 15
                ],
                [
                    'category' => 'Bidang Organisasi dan Kepanitiaan',
                    'description' => 'Pengurus UKM',
                    'role' => 'Wakil Ketua',
                    'event_level' => 'university',
                    'skp' => 14
                ],
                [
                    'category' => 'Bidang Organisasi dan Kepanitiaan',
                    'description' => 'Pengurus UKM',
                    'role' => 'Sekretaris',
                    'event_level' => 'university',
                    'skp' => 10
                ],
                [
                    'category' => 'Bidang Organisasi dan Kepanitiaan',
                    'description' => 'Pengurus UKM',
                    'role' => 'Bendahara',
                    'event_level' => 'university',
                    'skp' => 10
                ],
                [
                    'category' => 'Bidang Organisasi dan Kepanitiaan',
                    'description' => 'Pengurus UKM',
                    'role' => 'Anggota',
                    'event_level' => 'university',
                    'skp' => 7
                ],
            ], 'id');

            // Kepanitiaan tingkat internasional
            DetailSkp::upsert([
                [
                    'category' => 'Bidang Organisasi dan Kepanitiaan',
                    'description' => 'Mengikuti Kepanitiaan Internasional',
                    'role' => 'Ketua',
                    'event_level' => 'international',
                    'skp' => 15
                ],
                [
                    'category' => 'Bidang Organisasi dan Kepanitiaan',
                    'description' => 'Mengikuti Kepanitiaan Internasional',
                    'role' => 'Wakil Ketua',
                    'event_level' => 'international',
                    'skp' => 14
                ],
                [
                    'category' => 'Bidang Organisasi dan Kepanitiaan',
                    'description' => 'Mengikuti Kepanitiaan Internasional',
                    'role' => 'Sekretaris',
                    'event_level' => 'international',
                    'skp' => 10
                ],
                [
                    'category' => 'Bidang Organisasi dan Kepanitiaan',
                    'description' => 'Mengikuti Kepanitiaan Internasional',
                    'role' => 'Bendahara',
                    'event_level' => 'international',
                    'skp' => 10
                ],
                [
                    'category' => 'Bidang Organisasi dan Kepanitiaan',
                    'description' => 'Mengikuti Kepanitiaan Internasional',
                    'role' => 'Koordinator Seksi',
                    'event_level' => 'international',
                    'skp' => 9
                ],
                [
                    'category' => 'Bidang Organisasi dan Kepanitiaan',
                    'description' => 'Mengikuti Kepanitiaan Internasional',
                    'role' => 'Anggota',
                    'event_level' => 'international',
                    'skp' => 7
                ],
            ], 'id');

            // Kepanitiaan tingkat nasional
            DetailSkp::upsert([
                [
                    'category' => 'Bidang Organisasi dan Kepanitiaan',
                    'description' => 'Mengikuti Kepanitiaan Nasional',
                    'role' => 'Ketua',
                    'event_level' => 'national',
                    'skp' => 15
                ],
                [
                    'category' => 'Bidang Organisasi dan Kepanitiaan',
                    'description' => 'Mengikuti Kepanitiaan Nasional',
                    'role' => 'Wakil Ketua',
                    'event_level' => 'national',
                    'skp' => 14
                ],
                [
                    'category' => 'Bidang Organisasi dan Kepanitiaan',
                    'description' => 'Mengikuti Kepanitiaan Nasional',
                    'role' => 'Sekretaris',
                    'event_level' => 'national',
                    'skp' => 10
                ],
                [
                    'category' => 'Bidang Organisasi dan Kepanitiaan',
                    'description' => 'Mengikuti Kepanitiaan Nasional',
                    'role' => 'Bendahara',
                    'event_level' => 'national',
                    'skp' => 10
                ],
                [
                    'category' => 'Bidang Organisasi dan Kepanitiaan',
                    'description' => 'Mengikuti Kepanitiaan Nasional',
                    'role' => 'Koordinator Seksi',
                    'event_level' => 'national',
                    'skp' => 9
                ],
                [
                    'category' => 'Bidang Organisasi dan Kepanitiaan',
                    'description' => 'Mengikuti Kepanitiaan Nasional',
                    'role' => 'Anggota',
                    'event_level' => 'national',
                    'skp' => 7
                ],
            ], 'id');

            // Kepanitiaan tingkat regional (provinsi)
            DetailSkp::upsert([
                [
                    'category' => 'Bidang Organisasi dan Kepanitiaan',
                    'description' => 'Mengikuti Kepanitiaan Regional (Provinsi)',
                    'role' => 'Ketua',
                    'event_level' => 'regional',
                    'skp' => 15
                ],
                [
                    'category' => 'Bidang Organisasi dan Kepanitiaan',
                    'description' => 'Mengikuti Kepanitiaan Regional (Provinsi)',
                    'role' => 'Wakil Ketua',
                    'event_level' => 'regional',
                    'skp' => 14
                ],
                [
                    'category' => 'Bidang Organisasi dan Kepanitiaan',
                    'description' => 'Mengikuti Kepanitiaan Regional (Provinsi)',
                    'role' => 'Sekretaris',
                    'event_level' => 'regional',
                    'skp' => 10
                ],
                [
                    'category' => 'Bidang Organisasi dan Kepanitiaan',
                    'description' => 'Mengikuti Kepanitiaan Regional (Provinsi)',
                    'role' => 'Bendahara',
                    'event_level' => 'regional',
                    'skp' => 10
                ],
                [
                    'category' => 'Bidang Organisasi dan Kepanitiaan',
                    'description' => 'Mengikuti Kepanitiaan Regional (Provinsi)',
                    'role' => 'Koordinator Seksi',
                    'event_level' => 'regional',
                    'skp' => 9
                ],
                [
                    'category' => 'Bidang Organisasi dan Kepanitiaan',
                    'description' => 'Mengikuti Kepanitiaan Regional (Provinsi)',
                    'role' => 'Anggota',
                    'event_level' => 'regional',
                    'skp' => 7
                ],
            ], 'id');

            // Kepanitiaan tingkat universitas
            DetailSkp::upsert([
                [
                    'category' => 'Bidang Organisasi dan Kepanitiaan',
                    'description' => 'Mengikuti Kepanitiaan Universitas',
                    'role' => 'Ketua',
                    'event_level' => 'university',
                    'skp' => 10
                ],
                [
                    'category' => 'Bidang Organisasi dan Kepanitiaan',
                    'description' => 'Mengikuti Kepanitiaan Universitas',
                    'role' => 'Wakil Ketua',
                    'event_level' => 'university',
                    'skp' => 9
                ],
                [
                    'category' => 'Bidang Organisasi dan Kepanitiaan',
                    'description' => 'Mengikuti Kepanitiaan Universitas',
                    'role' => 'Sekretaris',
                    'event_level' => 'university',
                    'skp' => 8
                ],
                [
                    'category' => 'Bidang Organisasi dan Kepanitiaan',
                    'description' => 'Mengikuti Kepanitiaan Universitas',
                    'role' => 'Bendahara',
                    'event_level' => 'university',
                    'skp' => 8
                ],
                [
                    'category' => 'Bidang Organisasi dan Kepanitiaan',
                    'description' => 'Mengikuti Kepanitiaan Universitas',
                    'role' => 'Koordinator Seksi',
                    'event_level' => 'university',
                    'skp' => 7
                ],
                [
                    'category' => 'Bidang Organisasi dan Kepanitiaan',
                    'description' => 'Mengikuti Kepanitiaan Universitas',
                    'role' => 'Anggota',
                    'event_level' => 'university',
                    'skp' => 5
                ],
            ], 'id');

            // Kepanitiaan tingkat fakultas
            DetailSkp::upsert([
                [
                    'category' => 'Bidang Organisasi dan Kepanitiaan',
                    'description' => 'Mengikuti Kepanitiaan Fakultas',
                    'role' => 'Ketua',
                    'event_level' => 'faculty',
                    'skp' => 10
                ],
                [
                    'category' => 'Bidang Organisasi dan Kepanitiaan',
                    'description' => 'Mengikuti Kepanitiaan Fakultas',
                    'role' => 'Wakil Ketua',
                    'event_level' => 'faculty',
                    'skp' => 9
                ],
                [
                    'category' => 'Bidang Organisasi dan Kepanitiaan',
                    'description' => 'Mengikuti Kepanitiaan Fakultas',
                    'role' => 'Sekretaris',
                    'event_level' => 'faculty',
                    'skp' => 8
                ],
                [
                    'category' => 'Bidang Organisasi dan Kepanitiaan',
                    'description' => 'Mengikuti Kepanitiaan Fakultas',
                    'role' => 'Bendahara',
                    'event_level' => 'faculty',
                    'skp' => 8
                ],
                [
                    'category' => 'Bidang Organisasi dan Kepanitiaan',
                    'description' => 'Mengikuti Kepanitiaan Fakultas',
                    'role' => 'Koordinator Seksi',
                    'event_level' => 'faculty',
                    'skp' => 7
                ],
                [
                    'category' => 'Bidang Organisasi dan Kepanitiaan',
                    'description' => 'Mengikuti Kepanitiaan Fakultas',
                    'role' => 'Anggota',
                    'event_level' => 'faculty',
                    'skp' => 5
                ],
            ], 'id');

            // Kepanitiaan tingkat prodi
            DetailSkp::upsert([
                [
                    'category' => 'Bidang Organisasi dan Kepanitiaan',
                    'description' => 'Mengikuti Kepanitiaan Prodi',
                    'role' => 'Ketua',
                    'event_level' => 'major',
                    'skp' => 8
                ],
                [
                    'category' => 'Bidang Organisasi dan Kepanitiaan',
                    'description' => 'Mengikuti Kepanitiaan Prodi',
                    'role' => 'Wakil Ketua',
                    'event_level' => 'major',
                    'skp' => 7
                ],
                [
                    'category' => 'Bidang Organisasi dan Kepanitiaan',
                    'description' => 'Mengikuti Kepanitiaan Prodi',
                    'role' => 'Sekretaris',
                    'event_level' => 'major',
                    'skp' => 6
                ],
                [
                    'category' => 'Bidang Organisasi dan Kepanitiaan',
                    'description' => 'Mengikuti Kepanitiaan Prodi',
                    'role' => 'Bendahara',
                    'event_level' => 'major',
                    'skp' => 6
                ],
                [
                    'category' => 'Bidang Organisasi dan Kepanitiaan',
                    'description' => 'Mengikuti Kepanitiaan Prodi',
                    'role' => 'Koordinator Seksi',
                    'event_level' => 'major',
                    'skp' => 5
                ],
                [
                    'category' => 'Bidang Organisasi dan Kepanitiaan',
                    'description' => 'Mengikuti Kepanitiaan Prodi',
                    'role' => 'Anggota',
                    'event_level' => 'major',
                    'skp' => 3
                ],
            ], 'id');



        // BIDANG PENGABDIAN KEPADA MASYARAKAT
            // Pengabdian
            DetailSkp::upsert([
                [
                    'category' => 'Bidang Pengabdian Kepada Masyarakat',
                    'description' => 'Mengikuti Pengabdian',
                    'role' => 'Peserta',
                    'event_level' => 'international',
                    'skp' => 5
                ],
                [
                    'category' => 'Bidang Pengabdian Kepada Masyarakat',
                    'description' => 'Mengikuti Pengabdian',
                    'role' => 'Peserta',
                    'event_level' => 'national',
                    'skp' => 5
                ],
                [
                    'category' => 'Bidang Pengabdian Kepada Masyarakat',
                    'description' => 'Mengikuti Pengabdian',
                    'role' => 'Peserta',
                    'event_level' => 'regional',
                    'skp' => 4
                ],
                [
                    'category' => 'Bidang Pengabdian Kepada Masyarakat',
                    'description' => 'Mengikuti Pengabdian',
                    'role' => 'Peserta',
                    'event_level' => 'university',
                    'skp' => 3
                ],
                [
                    'category' => 'Bidang Pengabdian Kepada Masyarakat',
                    'description' => 'Mengikuti Pengabdian',
                    'role' => 'Peserta',
                    'event_level' => 'faculty',
                    'skp' => 3
                ],
                [
                    'category' => 'Bidang Pengabdian Kepada Masyarakat',
                    'description' => 'Mengikuti Pengabdian',
                    'role' => 'Peserta',
                    'event_level' => 'major',
                    'skp' => 3
                ],
            ], 'id');
    }
}
