<?php

namespace Database\Seeders;

use App\Models\jawaban;
use App\Models\Kategori;
use App\Models\lulusan;
use App\Models\penggunalulusan;
use App\Models\soal;
use Illuminate\Database\Seeder;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedKategoriDanSoal();
        $this->seedPerusahaanDanLulusan();
    }

    // -------------------------------------------------------------------------
    // KATEGORI & SOAL
    // -------------------------------------------------------------------------
    private function seedKategoriDanSoal(): void
    {
        $pilihanLikert = [
            ['jawaban' => 'Sangat Baik',   'nilai' => 4, 'urutan' => 1],
            ['jawaban' => 'Baik',           'nilai' => 3, 'urutan' => 2],
            ['jawaban' => 'Kurang',         'nilai' => 2, 'urutan' => 3],
            ['jawaban' => 'Sangat Kurang',  'nilai' => 1, 'urutan' => 4],
        ];

        // peruntukan: 'Umum' = muncul di semua survey
        //             'FTI'  = hanya lulusan Fakultas Teknologi & Informatika
        //             'FDIK' = hanya lulusan Fak. Desain & Industri Kreatif
        //             'FEB'  = hanya lulusan Fak. Ekonomi & Bisnis
        $data = [
            [
                'nama_kategori' => 'A. Kepribadian & Etika',
                'deskripsi'     => 'Penilaian karakter dan sikap lulusan di lingkungan kerja.',
                'soal'          => [
                    ['kode' => 'A1', 'teks' => 'Kedisiplinan lulusan di lingkungan kerja',          'jenis' => 'rating', 'peruntukan' => 'Umum'],
                    ['kode' => 'A2', 'teks' => 'Tanggung jawab lulusan terhadap pekerjaan',          'jenis' => 'rating', 'peruntukan' => 'Umum'],
                    ['kode' => 'A3', 'teks' => 'Loyalitas lulusan terhadap perusahaan',              'jenis' => 'rating', 'peruntukan' => 'Umum'],
                    ['kode' => 'A4', 'teks' => 'Kepedulian sosial lulusan dalam lingkungan kerja',   'jenis' => 'rating', 'peruntukan' => 'Umum'],
                ],
            ],
            [
                'nama_kategori' => 'B. Kemampuan Teknis',
                'deskripsi'     => 'Kompetensi teknis sesuai bidang studi lulusan.',
                'soal'          => [
                    ['kode' => 'B1', 'teks' => 'Kemampuan memanfaatkan teknologi informasi dalam pekerjaan',           'jenis' => 'rating', 'peruntukan' => 'FTI'],
                    ['kode' => 'B2', 'teks' => 'Kemampuan lulusan dalam memberikan solusi permasalahan teknis / IT',   'jenis' => 'rating', 'peruntukan' => 'FTI'],
                    ['kode' => 'B3', 'teks' => 'Kemampuan lulusan dalam menyesuaikan kebutuhan teknologi terkini',     'jenis' => 'rating', 'peruntukan' => 'FTI'],
                    ['kode' => 'B4', 'teks' => 'Kemampuan lulusan dalam merancang dan mengembangkan perangkat lunak',  'jenis' => 'rating', 'peruntukan' => 'FTI'],
                ],
            ],
            [
                'nama_kategori' => 'C. Komunikasi & Kerjasama',
                'deskripsi'     => 'Kemampuan lulusan berinteraksi dan berkolaborasi.',
                'soal'          => [
                    ['kode' => 'C1', 'teks' => 'Kemampuan menyampaikan gagasan / pendapat secara efektif',             'jenis' => 'rating', 'peruntukan' => 'Umum'],
                    ['kode' => 'C2', 'teks' => 'Kemampuan lulusan dalam bekerja secara tim',                           'jenis' => 'rating', 'peruntukan' => 'Umum'],
                    ['kode' => 'C3', 'teks' => 'Kemampuan berkomunikasi dengan atasan, bawahan, dan teman sejawat',    'jenis' => 'rating', 'peruntukan' => 'Umum'],
                ],
            ],
            [
                'nama_kategori' => 'D. Kepemimpinan & Inisiatif',
                'deskripsi'     => 'Kemampuan memimpin dan mengambil tindakan proaktif.',
                'soal'          => [
                    ['kode' => 'D1', 'teks' => 'Keberanian lulusan dalam mengambil keputusan',                 'jenis' => 'rating', 'peruntukan' => 'Umum'],
                    ['kode' => 'D2', 'teks' => 'Kemampuan lulusan untuk memotivasi rekan kerja',               'jenis' => 'rating', 'peruntukan' => 'Umum'],
                    ['kode' => 'D3', 'teks' => 'Inisiatif lulusan dalam menyelesaikan pekerjaan tanpa arahan', 'jenis' => 'rating', 'peruntukan' => 'Umum'],
                ],
            ],
            [
                'nama_kategori' => 'E. Kreativitas & Desain',
                'deskripsi'     => 'Kompetensi kreatif dan desain khusus lulusan FDIK.',
                'soal'          => [
                    ['kode' => 'E1', 'teks' => 'Kemampuan lulusan dalam menghasilkan karya desain yang inovatif',      'jenis' => 'rating', 'peruntukan' => 'FDIK'],
                    ['kode' => 'E2', 'teks' => 'Kemampuan lulusan dalam memahami kebutuhan visual klien/pengguna',     'jenis' => 'rating', 'peruntukan' => 'FDIK'],
                    ['kode' => 'E3', 'teks' => 'Kemampuan lulusan dalam menggunakan perangkat desain profesional',     'jenis' => 'rating', 'peruntukan' => 'FDIK'],
                ],
            ],
            [
                'nama_kategori' => 'F. Kemampuan Bisnis & Keuangan',
                'deskripsi'     => 'Kompetensi bisnis dan keuangan khusus lulusan FEB.',
                'soal'          => [
                    ['kode' => 'F1', 'teks' => 'Kemampuan lulusan dalam menganalisis laporan keuangan',                        'jenis' => 'rating', 'peruntukan' => 'FEB'],
                    ['kode' => 'F2', 'teks' => 'Pemahaman lulusan terhadap prinsip akuntansi dan perpajakan',                  'jenis' => 'rating', 'peruntukan' => 'FEB'],
                    ['kode' => 'F3', 'teks' => 'Kemampuan lulusan dalam menyusun strategi bisnis dan pemasaran',               'jenis' => 'rating', 'peruntukan' => 'FEB'],
                ],
            ],
            [
                'nama_kategori' => 'G. Umpan Balik Terbuka',
                'deskripsi'     => 'Pertanyaan terbuka untuk masukan kualitatif.',
                'soal'          => [
                    ['kode' => 'G1', 'teks' => 'Apa kelebihan utama yang Anda lihat dari lulusan ini selama bekerja?',                         'jenis' => 'essay', 'peruntukan' => 'Umum'],
                    ['kode' => 'G2', 'teks' => 'Area atau kompetensi apa yang perlu ditingkatkan oleh lulusan ini?',                           'jenis' => 'essay', 'peruntukan' => 'Umum'],
                    ['kode' => 'G3', 'teks' => 'Saran untuk institusi pendidikan agar lulusan lebih siap menghadapi dunia kerja?',             'jenis' => 'essay', 'peruntukan' => 'Umum'],
                ],
            ],
        ];

        foreach ($data as $item) {
            $kategori = Kategori::firstOrCreate(
                ['nama_kategori' => $item['nama_kategori']],
                ['deskripsi' => $item['deskripsi']]
            );

            foreach ($item['soal'] as $s) {
                $soalModel = soal::firstOrCreate(
                    ['kode' => $s['kode']],
                    [
                        'soal'                => $s['teks'],
                        'kategori_id'         => $kategori->id,
                        'jenis_soal'          => $s['jenis'],
                        'peruntukan_fakultas' => $s['peruntukan'],
                        'is_required'         => true,
                        'is_active'           => true,
                    ]
                );

                // Tambah pilihan jawaban hanya untuk soal rating yang belum punya jawaban
                if ($s['jenis'] === 'rating' && $soalModel->jawaban()->count() === 0) {
                    foreach ($pilihanLikert as $pj) {
                        jawaban::create([
                            'soal_id' => $soalModel->id,
                            'jawaban' => $pj['jawaban'],
                            'nilai'   => $pj['nilai'],
                            'urutan'  => $pj['urutan'],
                        ]);
                    }
                }
            }
        }

        $this->command->info('Kategori & Soal berhasil di-seed.');
    }

    // -------------------------------------------------------------------------
    // PERUSAHAAN & LULUSAN
    // -------------------------------------------------------------------------
    private function seedPerusahaanDanLulusan(): void
    {
        // Setiap perusahaan membawa lulusan dari tahun yang berbeda-beda
        // agar fitur filter tahun lulus di bulk survey bisa diuji
        $dataset = [
            [
                'perusahaan' => [
                    'nama_perusahaan'    => 'PT. Solusi Digital Nusantara',
                    'nama_penyelia'      => 'Rendra Kusuma',
                    'email_penyelia'     => 'rendra@sdn.co.id',
                    'kontak_penyelia'    => '081234560001',
                    'jenis_perusahaan'   => 'Swasta',
                    'alamat_perusahaan'  => 'Jl. Sudirman No. 12, Jakarta Pusat',
                    'kontak_perusahaan'  => '02112340001',
                    'nomor_badan_hukum'  => 'AHU-00001.AH.01',
                    'cabang_kota'        => true,
                    'cabang_negara'      => false,
                ],
                'lulusan' => [
                    ['nama' => 'Andi Firmansyah',  'nim' => '2019001', 'prodi' => 'Teknik Informatika',     'fakultas' => 'FTI',  'tahun_lulus' => '2022-08-10'],
                    ['nama' => 'Bella Ramadhani',   'nim' => '2019002', 'prodi' => 'Sistem Informasi',       'fakultas' => 'FTI',  'tahun_lulus' => '2022-09-05'],
                    ['nama' => 'Chandra Wijaya',    'nim' => '2019003', 'prodi' => 'Manajemen Informatika',  'fakultas' => 'FTI',  'tahun_lulus' => '2022-07-20'],
                ],
            ],
            [
                'perusahaan' => [
                    'nama_perusahaan'    => 'CV. Kreasi Media Inovasi',
                    'nama_penyelia'      => 'Dewi Hartono',
                    'email_penyelia'     => 'dewi.hartono@kmi.id',
                    'kontak_penyelia'    => '081234560002',
                    'jenis_perusahaan'   => 'Startup',
                    'alamat_perusahaan'  => 'Jl. Gatot Subroto Km. 5, Bandung',
                    'kontak_perusahaan'  => '02212340002',
                    'nomor_badan_hukum'  => null,
                    'cabang_kota'        => false,
                    'cabang_negara'      => false,
                ],
                'lulusan' => [
                    ['nama' => 'Dina Fitriani',     'nim' => '2020001', 'prodi' => 'Desain Komunikasi Visual', 'fakultas' => 'FDIK', 'tahun_lulus' => '2023-08-15'],
                    ['nama' => 'Eko Prasetyo',       'nim' => '2020002', 'prodi' => 'Ilmu Komunikasi',          'fakultas' => 'FDIK', 'tahun_lulus' => '2023-09-01'],
                    ['nama' => 'Farida Yusuf',       'nim' => '2020003', 'prodi' => 'Jurnalistik',              'fakultas' => 'FDIK', 'tahun_lulus' => '2023-07-30'],
                ],
            ],
            [
                'perusahaan' => [
                    'nama_perusahaan'    => 'Dinas Kominfo Kota Surabaya',
                    'nama_penyelia'      => 'Ir. Gunawan Santoso',
                    'email_penyelia'     => 'gunawan@kominfo-sby.go.id',
                    'kontak_penyelia'    => '081234560003',
                    'jenis_perusahaan'   => 'BUMN/Instansi Pemerintah',
                    'alamat_perusahaan'  => 'Jl. Jimerto No. 25, Surabaya',
                    'kontak_perusahaan'  => '03112340003',
                    'nomor_badan_hukum'  => 'SK.KOMINFO/001/2020',
                    'cabang_kota'        => true,
                    'cabang_negara'      => false,
                ],
                'lulusan' => [
                    ['nama' => 'Hendra Saputra',    'nim' => '2020004', 'prodi' => 'Teknik Informatika',     'fakultas' => 'FTI',  'tahun_lulus' => '2023-08-20'],
                    ['nama' => 'Indira Kusuma',      'nim' => '2020005', 'prodi' => 'Sistem Informasi',       'fakultas' => 'FTI',  'tahun_lulus' => '2023-10-01'],
                ],
            ],
            [
                'perusahaan' => [
                    'nama_perusahaan'    => 'PT. Finansial Mitra Sejahtera',
                    'nama_penyelia'      => 'Joko Widodo Hartadi',
                    'email_penyelia'     => 'joko.hartadi@fms.co.id',
                    'kontak_penyelia'    => '081234560004',
                    'jenis_perusahaan'   => 'Swasta',
                    'alamat_perusahaan'  => 'Jl. Thamrin No. 88, Jakarta Pusat',
                    'kontak_perusahaan'  => '02112340004',
                    'nomor_badan_hukum'  => 'AHU-00004.AH.01',
                    'cabang_kota'        => true,
                    'cabang_negara'      => true,
                ],
                'lulusan' => [
                    ['nama' => 'Kevin Adrianto',    'nim' => '2021001', 'prodi' => 'Akuntansi',              'fakultas' => 'FEB',  'tahun_lulus' => '2024-08-10'],
                    ['nama' => 'Lina Marlina',       'nim' => '2021002', 'prodi' => 'Manajemen',              'fakultas' => 'FEB',  'tahun_lulus' => '2024-09-15'],
                    ['nama' => 'Mario Tanaka',       'nim' => '2021003', 'prodi' => 'Ekonomi Pembangunan',    'fakultas' => 'FEB',  'tahun_lulus' => '2024-07-25'],
                    ['nama' => 'Nina Agustina',      'nim' => '2021004', 'prodi' => 'Akuntansi',              'fakultas' => 'FEB',  'tahun_lulus' => '2024-10-05'],
                ],
            ],
            [
                'perusahaan' => [
                    'nama_perusahaan'    => 'Yayasan Pendidikan Cerdas Bangsa',
                    'nama_penyelia'      => 'Oktavia Sari',
                    'email_penyelia'     => 'oktavia@cerdas-bangsa.org',
                    'kontak_penyelia'    => '081234560005',
                    'jenis_perusahaan'   => 'Nirlaba/Yayasan',
                    'alamat_perusahaan'  => 'Jl. Pahlawan No. 3, Yogyakarta',
                    'kontak_perusahaan'  => '02712340005',
                    'nomor_badan_hukum'  => null,
                    'cabang_kota'        => false,
                    'cabang_negara'      => false,
                ],
                'lulusan' => [
                    ['nama' => 'Putri Handayani',   'nim' => '2021005', 'prodi' => 'Teknik Informatika',     'fakultas' => 'FTI',  'tahun_lulus' => '2024-08-30'],
                    ['nama' => 'Rizky Maulana',      'nim' => '2021006', 'prodi' => 'Sistem Informasi',       'fakultas' => 'FTI',  'tahun_lulus' => '2024-09-20'],
                ],
            ],
        ];

        foreach ($dataset as $item) {
            $perusahaan = penggunalulusan::firstOrCreate(
                ['email_penyelia' => $item['perusahaan']['email_penyelia']],
                $item['perusahaan']
            );

            foreach ($item['lulusan'] as $l) {
                lulusan::firstOrCreate(
                    ['nim' => $l['nim']],
                    [
                        'pengguna_lulusan_id' => $perusahaan->id,
                        'nama'                => $l['nama'],
                        'program_studi'       => $l['prodi'],
                        'fakultas'            => $l['fakultas'],
                        'tahun_lulus'         => $l['tahun_lulus'],
                        'status'              => true,
                    ]
                );
            }
        }

        $this->command->info('Perusahaan & Lulusan berhasil di-seed.');
        $this->command->line('  Tahun lulus tersedia: 2022 (3 lulusan), 2023 (5 lulusan), 2024 (6 lulusan)');
    }
}
