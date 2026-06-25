<?php

namespace Database\Seeders;

use App\Models\lulusan;
use App\Models\penggunalulusan;
use Illuminate\Database\Seeder;

class LulusanSeeder extends Seeder
{
    public function run(): void
    {
        $dataset = [
            // PT. Solusi Digital Nusantara → FTI
            'rendra@sdn.co.id' => [
                ['nama' => 'Andi Firmansyah',   'nim' => '2019001001', 'program_studi' => 'Teknik Informatika',    'fakultas' => 'FTI',  'tahun_lulus' => '2022-08-10'],
                ['nama' => 'Bella Ramadhani',    'nim' => '2019001002', 'program_studi' => 'Sistem Informasi',      'fakultas' => 'FTI',  'tahun_lulus' => '2022-09-05'],
                ['nama' => 'Chandra Wijaya',     'nim' => '2019001003', 'program_studi' => 'Manajemen Informatika', 'fakultas' => 'FTI',  'tahun_lulus' => '2022-07-20'],
                ['nama' => 'Aditya Pranata',     'nim' => '2022001004', 'program_studi' => 'Teknik Informatika',    'fakultas' => 'FTI',  'tahun_lulus' => '2025-08-12'],
            ],
            // CV. Kreasi Media Inovasi → FDIK
            'dewi.hartono@kmi.id' => [
                ['nama' => 'Dina Fitriani',      'nim' => '2020002001', 'program_studi' => 'Desain Komunikasi Visual', 'fakultas' => 'FDIK', 'tahun_lulus' => '2023-08-15'],
                ['nama' => 'Eko Prasetyo',        'nim' => '2020002002', 'program_studi' => 'Ilmu Komunikasi',          'fakultas' => 'FDIK', 'tahun_lulus' => '2023-09-01'],
                ['nama' => 'Farida Yusuf',        'nim' => '2020002003', 'program_studi' => 'Jurnalistik',              'fakultas' => 'FDIK', 'tahun_lulus' => '2023-07-30'],
                ['nama' => 'Citra Lestari',       'nim' => '2022002004', 'program_studi' => 'Desain Komunikasi Visual', 'fakultas' => 'FDIK', 'tahun_lulus' => '2025-09-03'],
            ],
            // Dinas Kominfo Kota Surabaya → FTI
            'gunawan@kominfo-sby.go.id' => [
                ['nama' => 'Hendra Saputra',     'nim' => '2020003001', 'program_studi' => 'Teknik Informatika',    'fakultas' => 'FTI',  'tahun_lulus' => '2023-08-20'],
                ['nama' => 'Indira Kusuma',       'nim' => '2020003002', 'program_studi' => 'Sistem Informasi',      'fakultas' => 'FTI',  'tahun_lulus' => '2023-10-01'],
            ],
            // PT. Finansial Mitra Sejahtera → FEB
            'joko.hartadi@fms.co.id' => [
                ['nama' => 'Kevin Adrianto',     'nim' => '2021004001', 'program_studi' => 'Akuntansi',              'fakultas' => 'FEB',  'tahun_lulus' => '2024-08-10'],
                ['nama' => 'Lina Marlina',        'nim' => '2021004002', 'program_studi' => 'Manajemen',              'fakultas' => 'FEB',  'tahun_lulus' => '2024-09-15'],
                ['nama' => 'Mario Tanaka',        'nim' => '2021004003', 'program_studi' => 'Ekonomi Pembangunan',   'fakultas' => 'FEB',  'tahun_lulus' => '2024-07-25'],
                ['nama' => 'Nina Agustina',       'nim' => '2021004004', 'program_studi' => 'Akuntansi',              'fakultas' => 'FEB',  'tahun_lulus' => '2024-10-05'],
                ['nama' => 'Dimas Prakoso',       'nim' => '2022004005', 'program_studi' => 'Manajemen',              'fakultas' => 'FEB',  'tahun_lulus' => '2025-07-28'],
            ],
            // Yayasan Pendidikan Cerdas Bangsa → FTI
            'oktavia@cerdas-bangsa.org' => [
                ['nama' => 'Putri Handayani',    'nim' => '2021005001', 'program_studi' => 'Teknik Informatika',    'fakultas' => 'FTI',  'tahun_lulus' => '2024-08-30'],
                ['nama' => 'Rizky Maulana',       'nim' => '2021005002', 'program_studi' => 'Sistem Informasi',      'fakultas' => 'FTI',  'tahun_lulus' => '2024-09-20'],
            ],
            // PT. Telekomunikasi Andalan → FTI
            'bagas@telka.co.id' => [
                ['nama' => 'Sandi Nugroho',      'nim' => '2020006001', 'program_studi' => 'Teknik Informatika',    'fakultas' => 'FTI',  'tahun_lulus' => '2023-09-10'],
                ['nama' => 'Tania Setiawati',     'nim' => '2020006002', 'program_studi' => 'Sistem Informasi',      'fakultas' => 'FTI',  'tahun_lulus' => '2023-11-05'],
            ],
            // Global Creative Studio → FDIK
            'rachel@globalcreative.id' => [
                ['nama' => 'Umar Fauzan',        'nim' => '2021007001', 'program_studi' => 'Desain Komunikasi Visual', 'fakultas' => 'FDIK', 'tahun_lulus' => '2024-08-05'],
                ['nama' => 'Vina Oktariani',      'nim' => '2021007002', 'program_studi' => 'Ilmu Komunikasi',          'fakultas' => 'FDIK', 'tahun_lulus' => '2024-09-25'],
            ],
            // PT. Akunting Profesional Indonesia → FEB
            'hendra@api.co.id' => [
                ['nama' => 'Wendi Kurniawan',    'nim' => '2020008001', 'program_studi' => 'Akuntansi',              'fakultas' => 'FEB',  'tahun_lulus' => '2023-08-01'],
                ['nama' => 'Xena Paramita',       'nim' => '2020008002', 'program_studi' => 'Manajemen',              'fakultas' => 'FEB',  'tahun_lulus' => '2023-10-15'],
                ['nama' => 'Yoga Pratama',        'nim' => '2020008003', 'program_studi' => 'Ekonomi Pembangunan',   'fakultas' => 'FEB',  'tahun_lulus' => '2023-07-22'],
            ],
        ];

        foreach ($dataset as $emailPenyelia => $lulusanList) {
            $perusahaan = penggunalulusan::where('email_penyelia', $emailPenyelia)->first();

            if (!$perusahaan) {
                $this->command->warn("Perusahaan dengan email {$emailPenyelia} tidak ditemukan, lewati.");
                continue;
            }

            foreach ($lulusanList as $l) {
                lulusan::updateOrCreate(
                    ['nim' => $l['nim']],
                    [
                        'pengguna_lulusan_id' => $perusahaan->id,
                        'nama'                => $l['nama'],
                        'program_studi'       => $l['program_studi'],
                        'fakultas'            => $l['fakultas'],
                        'tahun_lulus'         => $l['tahun_lulus'],
                        'status'              => true,
                    ]
                );
            }
        }

        $this->command->info('Lulusan: 24 data berhasil di-seed.');
        $this->command->line('  Tahun lulus: 2022 (3), 2023 (9), 2024 (9), 2025 (3)');
        $this->command->line('  Fakultas: FTI (11), FDIK (6), FEB (7)');
    }
}
