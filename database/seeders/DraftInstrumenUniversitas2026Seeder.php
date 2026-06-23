<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DraftInstrumenUniversitas2026Seeder extends Seeder
{
    public function run(): void
    {
        DB::table('jawaban')->delete();
        DB::table('soal')->delete();
        DB::table('kategoris')->delete();

        $likert = [
            ['jawaban' => 'Sangat Baik', 'nilai' => 4, 'urutan' => 1],
            ['jawaban' => 'Baik',        'nilai' => 3, 'urutan' => 2],
            ['jawaban' => 'Cukup',       'nilai' => 2, 'urutan' => 3],
            ['jawaban' => 'Kurang',      'nilai' => 1, 'urutan' => 4],
        ];

        $instrument = [

            // ── Halaman 3 ───────────────────────────────────────────────────

            // ── Halaman 3-4 ─────────────────────────────────────────────────
            'B. Etika' => [
                'deskripsi' => 'Penilaian kejujuran dan profesionalisme lulusan di tempat kerja.',
                'soal' => [
                    'B1' => ['teks' => 'Lulusan bertindak jujur dalam bekerja',                              'jenis' => 'rating', 'pilihan' => $likert],
                    'B2' => ['teks' => 'Lulusan bersikap profesional dalam berinteraksi di tempat kerja',    'jenis' => 'rating', 'pilihan' => $likert],
                ],
            ],

            'C. Keahlian Berdasarkan Bidang Ilmu' => [
                'deskripsi' => 'Kompetensi dan kemampuan lulusan sesuai bidang studi yang ditempuh.',
                'soal' => [
                    'C1' => ['teks' => 'Kesesuaian kompetensi lulusan dengan kebutuhan pekerjaan',                                    'jenis' => 'rating', 'pilihan' => $likert],
                    'C2' => ['teks' => 'Kemampuan lulusan dalam menyelesaikan pekerjaan sesuai bidangnya',                             'jenis' => 'rating', 'pilihan' => $likert],
                    'C3' => ['teks' => 'Kemampuan lulusan dalam menerapkan pengetahuan dan keterampilannya dalam pekerjaan',           'jenis' => 'rating', 'pilihan' => $likert],
                ],
            ],

            'D. Kemampuan Berbahasa Asing' => [
                'deskripsi' => 'Kemampuan lulusan dalam berkomunikasi menggunakan bahasa asing.',
                'soal' => [
                    'D1' => ['teks' => 'Kemampuan lulusan dalam memahami komunikasi dalam bahasa asing',      'jenis' => 'rating', 'pilihan' => $likert],
                    'D2' => ['teks' => 'Kemampuan lulusan dalam berkomunikasi menggunakan bahasa asing',       'jenis' => 'rating', 'pilihan' => $likert],
                ],
            ],

            'E. Penggunaan Teknologi Informasi' => [
                'deskripsi' => 'Kemampuan lulusan dalam memanfaatkan dan beradaptasi dengan teknologi.',
                'soal' => [
                    'E1' => ['teks' => 'Kemampuan lulusan dalam menggunakan teknologi atau aplikasi yang mendukung pekerjaan', 'jenis' => 'rating', 'pilihan' => $likert],
                    'E2' => ['teks' => 'Kemampuan lulusan dalam mempelajari dan beradaptasi dengan teknologi baru',            'jenis' => 'rating', 'pilihan' => $likert],
                ],
            ],

            // ── Halaman 4 ───────────────────────────────────────────────────
            'F. Kemampuan Berkomunikasi' => [
                'deskripsi' => 'Kemampuan lulusan menyampaikan informasi dan berkomunikasi di lingkungan kerja.',
                'soal' => [
                    'F1' => ['teks' => 'Kemampuan lulusan dalam menyampaikan ide atau informasi dengan jelas',          'jenis' => 'rating', 'pilihan' => $likert],
                    'F2' => ['teks' => 'Kemampuan lulusan dalam berkomunikasi secara efektif di lingkungan kerja',       'jenis' => 'rating', 'pilihan' => $likert],
                ],
            ],

            'G. Kerjasama Tim' => [
                'deskripsi' => 'Kemampuan lulusan bekerja sama dan berkontribusi dalam tim.',
                'soal' => [
                    'G1' => ['teks' => 'Kemampuan lulusan dalam bekerja sama dalam tim',                     'jenis' => 'rating', 'pilihan' => $likert],
                    'G2' => ['teks' => 'Kemampuan lulusan dalam berkontribusi untuk mencapai tujuan tim',    'jenis' => 'rating', 'pilihan' => $likert],
                ],
            ],

            'H. Pengembangan Diri' => [
                'deskripsi' => 'Kemauan dan kemampuan lulusan untuk terus berkembang dan beradaptasi.',
                'soal' => [
                    'H1' => ['teks' => 'Kemauan lulusan untuk terus belajar dan meningkatkan kompetensi',                 'jenis' => 'rating', 'pilihan' => $likert],
                    'H2' => ['teks' => 'Kemampuan lulusan dalam beradaptasi terhadap perubahan di lingkungan kerja',      'jenis' => 'rating', 'pilihan' => $likert],
                ],
            ],

            'I. Kepemimpinan' => [
                'deskripsi' => 'Kemampuan lulusan mengambil inisiatif dan mengoordinasikan rekan kerja.',
                'soal' => [
                    'I1' => ['teks' => 'Kemampuan lulusan dalam mengambil inisiatif dalam pekerjaan',                    'jenis' => 'rating', 'pilihan' => $likert],
                    'I2' => ['teks' => 'Kemampuan lulusan dalam mengarahkan dan mengoordinasikan rekan kerja',           'jenis' => 'rating', 'pilihan' => $likert],
                ],
            ],

            'J. Etos Kerja' => [
                'deskripsi' => 'Kedisiplinan, tanggung jawab, dan inisiatif lulusan dalam menjalankan tugas.',
                'soal' => [
                    'J1' => ['teks' => 'Disiplin kerja lulusan dalam menjalankan tugas',                    'jenis' => 'rating', 'pilihan' => $likert],
                    'J2' => ['teks' => 'Tanggung jawab lulusan terhadap pekerjaan yang diberikan',          'jenis' => 'rating', 'pilihan' => $likert],
                    'J3' => ['teks' => 'Inisiatif lulusan dalam menyelesaikan pekerjaan',                   'jenis' => 'rating', 'pilihan' => $likert],
                ],
            ],

            // ── Halaman 5 ───────────────────────────────────────────────────
            'K. Evaluasi Umum' => [
                'deskripsi' => 'Penilaian umum terhadap keseluruhan kualitas lulusan.',
                'soal' => [
                    'K1' => [
                        'teks'  => 'Secara umum, bagaimana penilaian Anda terhadap kualitas lulusan kami?',
                        'jenis' => 'multiple_choice',
                        'pilihan' => $likert,
                    ],
                ],
            ],

            'L. Masukan dan Saran' => [
                'deskripsi' => 'Masukan kualitatif untuk peningkatan kualitas lulusan.',
                'soal' => [
                    'L1' => [
                        'teks'    => 'Apa yang perlu ditingkatkan dari lulusan kami agar lebih sesuai dengan kebutuhan industri?',
                        'jenis'   => 'essay',
                        'pilihan' => [],
                    ],
                ],
            ],

            'M. Kerjasama Lanjutan' => [
                'deskripsi' => 'Potensi bentuk kerja sama antara perusahaan dan kampus.',
                'soal' => [
                    'M1' => [
                        'teks'  => 'Bentuk kerja sama apa yang berpotensi dapat dilakukan antara perusahaan Anda dengan kampus kami?',
                        'jenis' => 'multiple_choice',
                        'pilihan' => [
                            ['jawaban' => 'Rekrutmen Lulusan',                          'nilai' => 1, 'urutan' => 1],
                            ['jawaban' => 'Internship / Magang Mahasiswa',              'nilai' => 2, 'urutan' => 2],
                            ['jawaban' => 'Kuliah tamu / narasumber praktisi',          'nilai' => 3, 'urutan' => 3],
                            ['jawaban' => 'Keterlibatan pengembangan kurikulum',        'nilai' => 4, 'urutan' => 4],
                            ['jawaban' => 'Kunjungan industri / company visit',         'nilai' => 5, 'urutan' => 5],
                            ['jawaban' => 'Penelitian atau proyek bersama',             'nilai' => 6, 'urutan' => 6],
                            ['jawaban' => 'Kerjasama Corporate Social Responsibility (CSR)', 'nilai' => 7, 'urutan' => 7],
                            ['jawaban' => 'Riset Pengembangan Produk',                  'nilai' => 8, 'urutan' => 8],
                            ['jawaban' => 'Lainnya',                                    'nilai' => 9, 'urutan' => 9],
                        ],
                    ],
                ],
            ],
        ];

        DB::beginTransaction();

        try {
            $now = Carbon::now();

            foreach ($instrument as $kategoriNama => $kategoriData) {

                $kategoriId = DB::table('kategoris')->insertGetId([
                    'nama_kategori' => $kategoriNama,
                    'deskripsi'     => $kategoriData['deskripsi'],
                    'created_at'    => $now,
                    'updated_at'    => $now,
                ]);

                foreach ($kategoriData['soal'] as $kode => $soalData) {

                    $soalId = DB::table('soal')->insertGetId([
                        'soal'                => $soalData['teks'],
                        'kode'                => $kode,
                        'kategori_id'         => $kategoriId,
                        'jenis_soal'          => $soalData['jenis'],
                        'peruntukan_fakultas' => 'Umum',
                        'is_required'         => true,
                        'is_active'           => true,
                        'created_at'          => $now,
                        'updated_at'          => $now,
                    ]);

                    if (!empty($soalData['pilihan'])) {
                        $jawabanRows = [];
                        foreach ($soalData['pilihan'] as $p) {
                            $jawabanRows[] = [
                                'soal_id'    => $soalId,
                                'jawaban'    => $p['jawaban'],
                                'nilai'      => $p['nilai'],
                                'urutan'     => $p['urutan'],
                                'created_at' => $now,
                                'updated_at' => $now,
                            ];
                        }
                        DB::table('jawaban')->insert($jawabanRows);
                    }
                }
            }

            DB::commit();
            $this->command->info('Draft Instrumen Universitas 2026 berhasil di-seed!');

        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('Gagal melakukan seeder: ' . $e->getMessage());
        }
    }
}
