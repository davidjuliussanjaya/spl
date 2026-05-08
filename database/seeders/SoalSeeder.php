<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SoalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Definisikan Pilihan Jawaban Skala Likert (Rating)
        $pilihanJawaban = [
            ['jawaban' => 'Sangat Baik', 'nilai' => 4, 'urutan' => 1],
            ['jawaban' => 'Baik', 'nilai' => 3, 'urutan' => 2],
            ['jawaban' => 'Kurang', 'nilai' => 2, 'urutan' => 3],
            ['jawaban' => 'Sangat Kurang', 'nilai' => 1, 'urutan' => 4],
        ];

        // 2. Data Master Pertanyaan Berdasarkan Gambar
        $kumpulanSoal = [
            'B. Kepemimpinan' => [
                'B1' => 'Kemampuan lulusan untuk menyusun perencanaan',
                'B2' => 'Keberanian lulusan dalam mengambil keputusan',
                'B3' => 'Kemampuan lulusan untuk memotivasi rekan kerja',
                'B4' => 'Kemampuan untuk membuat lingkungan kerja yang menyenangkan',
            ],
            'C. Etos Kerja' => [
                'C1' => 'Disiplin Kerja',
                'C2' => 'Inisiatif',
                'C3' => 'Tanggung Jawab',
            ],
            'D. Kemampuan Berbahasa Asing' => [
                'D1' => 'Kemampuan lulusan dalam menggunakan bahasa asing secara lisan',
                'D2' => 'Kemampuan lulusan dalam menggunakan bahasa asing secara tulis',
            ],
            'E. Keahlian Bidang Infokom' => [
                'E1' => 'Kemampuan memanfaatkan teknologi informasi dalam pekerjaan',
                'E2' => 'Kemampuan lulusan dalam menyesuaikan kebutuhan teknologi informasi terkini untuk pekerjaan',
                'E3' => 'Kemampuan lulusan dalam memberikan solusi permasalahan TI',
            ],
            'F. Kemampuan Berkomunikasi' => [
                'F1' => 'Kemampuan menyampaikan gagasan / pendapat',
                'F2' => 'Kemampuan lulusan dalam berkomunikasi secara santun',
                'F3' => 'Kemampuan lulusan untuk berkomunikasi dengan atasan / bawahan / teman sejawat.',
                'F4' => 'Kemampuan menyampaikan pendapat secara terstruktur dan efektif',
            ],
            'G. Kerjasama Tim' => [
                'G1' => 'Kemampuan lulusan dalam bekerja secara tim',
                'G2' => 'Kemampuan dalam menerima pendapat kelompok untuk kepentingan Tim',
                'G3' => 'Kemampuan dalam memberi kontribusi untuk kepentingan tim',
            ],
            'H. Pengembangan Diri' => [
                'H1' => 'Kemampuan lulusan untuk beradaptasi dengan kebutuhan kerja',
                'H2' => 'Kemampuan lulusan untuk memecahkan masalah di tempat kerja',
                'H3' => 'Kesadaran untuk mau belajar',
                'H4' => 'Kemauan untuk explore di luar disiplin ilmu',
            ],
            'I.1 Kepribadian' => [
                'I1_1' => 'Kepedulian sosial lulusan dalam lingkungan kerja',
                'I1_2' => 'Kepedulian lulusan dengan kondisi lingkungan kerja',
                'I1_3' => 'Tanggung jawab lulusan terhadap pekerjaan',
                'I1_4' => 'Kedisiplinan lulusan di lingkungan kerja',
                'I1_5' => 'Loyalitas lulusan terhadap perusahaan',
            ],
            'I.2 Tuntutan Industri 4.0' => [
                'I2_1' => 'Kemampuan lulusan berpikir kritis',
                'I2_2' => 'Kemampuan lulusan dalam mengembangkan kreatifitas di lingkungan kerja',
                'I2_3' => 'Kemampuan lulusan dalam menciptakan inovasi',
                'I2_4' => 'Kemampuan lulusan dalam berkolaborasi di lingkungan kerja',
            ],
        ];

        DB::beginTransaction();

        try {
            $now = Carbon::now();

            foreach ($kumpulanSoal as $kategoriNama => $soalArray) {
                
                // Insert ke tabel kategoris dan ambil ID-nya
                $kategoriId = DB::table('kategoris')->insertGetId([
                    'nama_kategori' => $kategoriNama,
                    'deskripsi'     => null,
                    'created_at'    => $now,
                    'updated_at'    => $now,
                ]);

                foreach ($soalArray as $kode => $teksSoal) {
                    
                    // Insert ke tabel soal dan ambil ID-nya
                    $soalId = DB::table('soal')->insertGetId([
                        'soal'        => $teksSoal,
                        'kode'        => $kode,
                        'kategori_id' => $kategoriId,
                        'jenis_soal'  => 'rating',
                        'is_required' => true,
                        'is_active'   => true,
                        'created_at'  => $now,
                        'updated_at'  => $now,
                    ]);

                    // Siapkan array jawaban untuk soal yang baru saja diinsert
                    $jawabanData = [];
                    foreach ($pilihanJawaban as $pj) {
                        $jawabanData[] = [
                            'soal_id'    => $soalId,
                            'jawaban'    => $pj['jawaban'],
                            'nilai'      => $pj['nilai'],
                            'urutan'     => $pj['urutan'],
                            'created_at' => $now,
                            'updated_at' => $now,
                        ];
                    }

                    // Bulk insert ke tabel jawaban
                    DB::table('jawaban')->insert($jawabanData);
                }
            }

            DB::commit();
            $this->command->info('Data Soal & Jawaban berhasil di-seed!');
            
        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('Gagal melakukan seeder: ' . $e->getMessage());
        }
    }
}