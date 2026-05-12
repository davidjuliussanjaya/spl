<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class PenggunaLulusanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Data Perusahaan (Pengguna Lulusan)
        $perusahaan = [
            [
                'nama_perusahaan' => 'PT. Teknologi Maju Jaya',
                'nama_penyelia' => 'Budi Santoso',
                'email_penyelia' => 'budi.santoso@tmj.com',
                'jenis_perusahaan' => 'Swasta',
                'cabang_kota' => true,
                'cabang_negara' => false,
            ],
            [
                'nama_perusahaan' => 'Startup Cepat Tumbuh',
                'nama_penyelia' => 'Siska Amelia',
                'email_penyelia' => 'siska@cepat-tumbuh.io',
                'jenis_perusahaan' => 'Startup',
                'cabang_kota' => false,
                'cabang_negara' => false,
            ],
            [
                'nama_perusahaan' => 'Dinas Komunikasi dan Informatika',
                'nama_penyelia' => 'Ir. Ahmad Fauzi',
                'email_penyelia' => 'ahmad.fauzi@gov.go.id',
                'jenis_perusahaan' => 'BUMN/Instansi Pemerintah',
                'cabang_kota' => true,
                'cabang_negara' => false,
            ],
            [
                'nama_perusahaan' => 'Global Solution Corp',
                'nama_penyelia' => 'Michael Chen',
                'email_penyelia' => 'm.chen@globalsol.com',
                'jenis_perusahaan' => 'Swasta',
                'cabang_kota' => true,
                'cabang_negara' => true,
            ],
            [
                'nama_perusahaan' => 'Yayasan Indonesia Hijau',
                'nama_penyelia' => 'Siti Aminah',
                'email_penyelia' => 'siti@hijau.org',
                'jenis_perusahaan' => 'Nirlaba/Yayasan',
                'cabang_kota' => false,
                'cabang_negara' => false,
            ],
        ];

        foreach ($perusahaan as $p) {
            $perusahaanId = DB::table('pengguna_lulusan')->insertGetId(array_merge($p, [
                'alamat_perusahaan' => 'Jl. Sampel No. ' . rand(1, 100),
                'kontak_penyelia' => '0812345678' . rand(10, 99),
                'jumlah_lulusan' => 2,
                'durasi_lulusan_bekerja' => rand(12, 36),
                'created_at' => now(),
                'updated_at' => now(),
            ]));

            // 2. Data Lulusan (2 Mahasiswa per Perusahaan)
            $this->seedLulusan($perusahaanId, $p['nama_perusahaan']);
        }
    }
    private function seedLulusan($perusahaanId, $namaPT)
    {
        $mahasiswa = [
            ['nama' => 'Andi Pratama', 'nim' => '20190001'],
            ['nama' => 'Rina Wijaya', 'nim' => '20190002'],
        ];

        // Contoh sederhana: Mengubah nama sedikit berdasarkan perusahaan agar variatif
        foreach ($mahasiswa as $m) {
            DB::table('lulusan')->insert([
                'pengguna_lulusan_id' => $perusahaanId,
                'nama' => $m['nama'] . " (" . $namaPT . ")",
                'nim' => rand(100000, 999999),
                'program_studi' => 'Informatika',
                'tahun_lulus' => Carbon::create(2023, rand(1, 12), rand(1, 28)),
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
