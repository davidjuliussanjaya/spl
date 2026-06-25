<?php

namespace Database\Seeders;

use App\Models\penggunalulusan;
use Illuminate\Database\Seeder;

class PenggunaLulusanSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            [
                'nama_perusahaan'       => 'PT. Solusi Digital Nusantara',
                'nama_penyelia'         => 'Rendra Kusuma',
                'jabatan_penyelia'      => 'HRD Manager',
                'kontak_penyelia'       => '081234560001',
                'email_penyelia'        => 'rendra@sdn.co.id',
                'jumlah_lulusan'        => 4,
                'durasi_lulusan_bekerja'=> 24,
                'nomor_badan_hukum'     => 'AHU-00001.AH.01',
                'alamat_perusahaan'     => 'Jl. Sudirman No. 12, Jakarta Pusat',
                'kontak_perusahaan'     => '02112340001',
                'jenis_perusahaan'      => 'Swasta',
                'cabang_kota'           => 2,
                'cabang_negara'         => 0,
            ],
            [
                'nama_perusahaan'       => 'CV. Kreasi Media Inovasi',
                'nama_penyelia'         => 'Dewi Hartono',
                'jabatan_penyelia'      => 'CEO',
                'kontak_penyelia'       => '081234560002',
                'email_penyelia'        => 'dewi.hartono@kmi.id',
                'jumlah_lulusan'        => 4,
                'durasi_lulusan_bekerja'=> 18,
                'nomor_badan_hukum'     => null,
                'alamat_perusahaan'     => 'Jl. Gatot Subroto Km. 5, Bandung',
                'kontak_perusahaan'     => '02212340002',
                'jenis_perusahaan'      => 'Startup',
                'cabang_kota'           => 0,
                'cabang_negara'         => 0,
            ],
            [
                'nama_perusahaan'       => 'Dinas Kominfo Kota Surabaya',
                'nama_penyelia'         => 'Ir. Gunawan Santoso',
                'jabatan_penyelia'      => 'Kepala Bidang Informatika',
                'kontak_penyelia'       => '081234560003',
                'email_penyelia'        => 'gunawan@kominfo-sby.go.id',
                'jumlah_lulusan'        => 2,
                'durasi_lulusan_bekerja'=> 30,
                'nomor_badan_hukum'     => 'SK.KOMINFO/001/2020',
                'alamat_perusahaan'     => 'Jl. Jimerto No. 25, Surabaya',
                'kontak_perusahaan'     => '03112340003',
                'jenis_perusahaan'      => 'BUMN/Instansi Pemerintah',
                'cabang_kota'           => 3,
                'cabang_negara'         => 0,
            ],
            [
                'nama_perusahaan'       => 'PT. Finansial Mitra Sejahtera',
                'nama_penyelia'         => 'Joko Widodo Hartadi',
                'jabatan_penyelia'      => 'Finance Director',
                'kontak_penyelia'       => '081234560004',
                'email_penyelia'        => 'joko.hartadi@fms.co.id',
                'jumlah_lulusan'        => 5,
                'durasi_lulusan_bekerja'=> 12,
                'nomor_badan_hukum'     => 'AHU-00004.AH.01',
                'alamat_perusahaan'     => 'Jl. Thamrin No. 88, Jakarta Pusat',
                'kontak_perusahaan'     => '02112340004',
                'jenis_perusahaan'      => 'Swasta',
                'cabang_kota'           => 5,
                'cabang_negara'         => 2,
            ],
            [
                'nama_perusahaan'       => 'Yayasan Pendidikan Cerdas Bangsa',
                'nama_penyelia'         => 'Oktavia Sari',
                'jabatan_penyelia'      => 'Direktur Program',
                'kontak_penyelia'       => '081234560005',
                'email_penyelia'        => 'oktavia@cerdas-bangsa.org',
                'jumlah_lulusan'        => 2,
                'durasi_lulusan_bekerja'=> 20,
                'nomor_badan_hukum'     => null,
                'alamat_perusahaan'     => 'Jl. Pahlawan No. 3, Yogyakarta',
                'kontak_perusahaan'     => '02712340005',
                'jenis_perusahaan'      => 'Nirlaba/Yayasan',
                'cabang_kota'           => 1,
                'cabang_negara'         => 0,
            ],
            [
                'nama_perusahaan'       => 'PT. Telekomunikasi Andalan',
                'nama_penyelia'         => 'Bagas Praditya',
                'jabatan_penyelia'      => 'IT Manager',
                'kontak_penyelia'       => '081234560006',
                'email_penyelia'        => 'bagas@telka.co.id',
                'jumlah_lulusan'        => 2,
                'durasi_lulusan_bekerja'=> 36,
                'nomor_badan_hukum'     => 'AHU-00006.AH.01',
                'alamat_perusahaan'     => 'Jl. Asia Afrika No. 140, Bandung',
                'kontak_perusahaan'     => '02212340006',
                'jenis_perusahaan'      => 'BUMN/Instansi Pemerintah',
                'cabang_kota'           => 10,
                'cabang_negara'         => 1,
            ],
            [
                'nama_perusahaan'       => 'Global Creative Studio',
                'nama_penyelia'         => 'Rachel Dewanti',
                'jabatan_penyelia'      => 'Creative Director',
                'kontak_penyelia'       => '081234560007',
                'email_penyelia'        => 'rachel@globalcreative.id',
                'jumlah_lulusan'        => 2,
                'durasi_lulusan_bekerja'=> 15,
                'nomor_badan_hukum'     => null,
                'alamat_perusahaan'     => 'Jl. Kemang Raya No. 5, Jakarta Selatan',
                'kontak_perusahaan'     => '02112340007',
                'jenis_perusahaan'      => 'Startup',
                'cabang_kota'           => 0,
                'cabang_negara'         => 0,
            ],
            [
                'nama_perusahaan'       => 'PT. Akunting Profesional Indonesia',
                'nama_penyelia'         => 'Hendra Budiman',
                'jabatan_penyelia'      => 'Chief Accountant',
                'kontak_penyelia'       => '081234560008',
                'email_penyelia'        => 'hendra@api.co.id',
                'jumlah_lulusan'        => 3,
                'durasi_lulusan_bekerja'=> 28,
                'nomor_badan_hukum'     => 'AHU-00008.AH.01',
                'alamat_perusahaan'     => 'Jl. Kuningan Mulia No. 9, Jakarta Selatan',
                'kontak_perusahaan'     => '02112340008',
                'jenis_perusahaan'      => 'Swasta',
                'cabang_kota'           => 4,
                'cabang_negara'         => 1,
            ],
        ];

        foreach ($data as $item) {
            penggunalulusan::updateOrCreate(
                ['email_penyelia' => $item['email_penyelia']],
                $item
            );
        }

        $this->command->info('PenggunaLulusan: 8 perusahaan berhasil di-seed.');
    }
}
