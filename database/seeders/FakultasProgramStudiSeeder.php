<?php

namespace Database\Seeders;

use App\Models\Fakultas;
use App\Models\ProgramStudi;
use Illuminate\Database\Seeder;

class FakultasProgramStudiSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            'FTI' => ['nama' => 'Fakultas Teknologi dan Informatika', 'prodi' => ['Teknik Informatika', 'Sistem Informasi', 'Manajemen Informatika']],
            'FDIK' => ['nama' => 'Fakultas Desain dan Industri Kreatif', 'prodi' => ['Desain Komunikasi Visual', 'Ilmu Komunikasi', 'Jurnalistik']],
            'FEB' => ['nama' => 'Fakultas Ekonomi dan Bisnis', 'prodi' => ['Akuntansi', 'Ekonomi Pembangunan', 'Manajemen']],
        ];

        foreach ($data as $kodeFakultas => $item) {
            $fakultas = Fakultas::updateOrCreate(['kode' => $kodeFakultas], ['nama' => $item['nama']]);

            foreach ($item['prodi'] as $namaProdi) {
                ProgramStudi::updateOrCreate(
                    ['fakultas_id' => $fakultas->id, 'nama' => $namaProdi],
                    ['kode' => 'PS-' . strtoupper(substr(md5($kodeFakultas . '|' . $namaProdi), 0, 12))],
                );
            }
        }
    }
}
