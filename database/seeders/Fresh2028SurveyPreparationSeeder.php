<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Paket data mandiri untuk menyiapkan survei tahun 2028.
 *
 * Tidak dipanggil oleh DatabaseSeeder dan tidak membuat sesi survei. Jalankan
 * manual dengan: php artisan db:seed --class=Fresh2028SurveyPreparationSeeder
 */
class Fresh2028SurveyPreparationSeeder extends Seeder
{
    public function run(): void
    {
        $tahun = 2028;
        $judulInstrumen = 'Instrumen Mitra Industri Baru 2028';

        if (DB::table('instrumen')->where('judul', $judulInstrumen)->exists()) {
            $this->command->warn('Paket data mandiri 2028 sudah pernah dibuat; seeder dilewati.');
            return;
        }

        $prodi = DB::table('program_studi')
            ->join('fakultas', 'fakultas.id', '=', 'program_studi.fakultas_id')
            ->orderBy('fakultas.kode')
            ->orderBy('program_studi.nama')
            ->get(['program_studi.id as prodi_id', 'program_studi.nama as prodi_nama', 'fakultas.id as fakultas_id', 'fakultas.kode as fakultas_kode']);

        if ($prodi->isEmpty()) {
            throw new \RuntimeException('Data fakultas dan program studi belum tersedia. Jalankan FakultasProgramStudiSeeder terlebih dahulu.');
        }

        $now = now();
        $likert = [
            ['jawaban' => 'Sangat Baik', 'nilai' => 4, 'urutan' => 1],
            ['jawaban' => 'Baik', 'nilai' => 3, 'urutan' => 2],
            ['jawaban' => 'Cukup', 'nilai' => 2, 'urutan' => 3],
            ['jawaban' => 'Perlu Ditingkatkan', 'nilai' => 1, 'urutan' => 4],
        ];

        $kategoriBaru = [
            ['nama' => '2028 — Ketangguhan Profesional', 'deskripsi' => 'Kesiapan lulusan menghadapi ritme dan tanggung jawab kerja.', 'soal' => [
                ['kode' => 'P28-A1', 'teks' => 'Lulusan mampu menetapkan prioritas kerja secara mandiri.', 'jenis' => 'rating'],
                ['kode' => 'P28-A2', 'teks' => 'Lulusan dapat menjaga kualitas hasil kerja dalam tenggat yang ketat.', 'jenis' => 'rating'],
                ['kode' => 'P28-A3', 'teks' => 'Lulusan menerima umpan balik dan menindaklanjutinya secara konstruktif.', 'jenis' => 'rating'],
            ]],
            ['nama' => '2028 — Analisis dan Inovasi Praktis', 'deskripsi' => 'Kemampuan mengurai masalah dan menghasilkan perbaikan yang relevan.', 'soal' => [
                ['kode' => 'P28-B1', 'teks' => 'Lulusan mengidentifikasi akar masalah sebelum menentukan solusi.', 'jenis' => 'rating'],
                ['kode' => 'P28-B2', 'teks' => 'Lulusan mampu mengubah data kerja menjadi rekomendasi yang dapat dijalankan.', 'jenis' => 'rating'],
                ['kode' => 'P28-B3', 'teks' => 'Lulusan menawarkan gagasan perbaikan yang sesuai kebutuhan organisasi.', 'jenis' => 'rating'],
            ]],
            ['nama' => '2028 — Kolaborasi Lintas Peran', 'deskripsi' => 'Keterampilan bekerja bersama pihak dengan peran dan keahlian berbeda.', 'soal' => [
                ['kode' => 'P28-C1', 'teks' => 'Lulusan membangun koordinasi yang efektif dengan tim lintas fungsi.', 'jenis' => 'rating'],
                ['kode' => 'P28-C2', 'teks' => 'Lulusan mampu menjelaskan pekerjaan kepada rekan nonteknis.', 'jenis' => 'rating'],
                ['kode' => 'P28-C3', 'teks' => 'Lulusan menjaga komitmen kerja yang telah disepakati bersama tim.', 'jenis' => 'rating'],
            ]],
            ['nama' => '2028 — Kemitraan dan Masukan Industri', 'deskripsi' => 'Dokumentasi evaluasi serta peluang kemitraan dari pengguna lulusan.', 'soal' => [
                ['kode' => 'P28-D1', 'teks' => 'Area kompetensi apa yang paling perlu diperkuat pada lulusan berikutnya?', 'jenis' => 'essay'],
                ['kode' => 'P28-D2', 'teks' => 'Program kolaborasi apa yang relevan bagi organisasi Anda?', 'jenis' => 'multiple_choice', 'multiple' => true, 'custom' => true,
                    'pilihan' => ['Rekrutmen kampus', 'Proyek industri', 'Magang terstruktur', 'Mentoring praktisi']],
            ]],
        ];

        $namaLulusan = [
            'Alya Pramestari', 'Bima Kurniawan', 'Citra Mahadewi', 'Damar Wicaksana', 'Elara Nirmala',
            'Faris Adinata', 'Gita Larasati', 'Hadi Pranoto', 'Intan Puspitasari', 'Jovan Mahendra',
            'Kania Azzahra', 'Luthfi Ramadhan', 'Maya Kartikasari', 'Naufal Pradipta', 'Oline Savitri',
            'Prabu Santosa', 'Qanita Fadilah', 'Raka Dirgantara', 'Salsa Andriani', 'Tegar Wijaya',
        ];

        DB::transaction(function () use ($tahun, $judulInstrumen, $prodi, $now, $likert, $kategoriBaru, $namaLulusan) {
            DB::table('periode')->insert([
                'kode_periode' => '2028-MITRA-BARU',
                'nama_periode' => 'Periode Survei Mitra Baru 2028',
                'tanggal_mulai' => '2028-01-01',
                'tanggal_berakhir' => '2028-12-31',
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            $instrumenId = DB::table('instrumen')->insertGetId([
                'tahun' => $tahun,
                'judul' => $judulInstrumen,
                'deskripsi' => 'Instrumen baru dan terpisah untuk paket data manual 2028.',
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            foreach ($kategoriBaru as $index => $kategori) {
                $tujuan = $prodi[$index % $prodi->count()];
                $kategoriId = DB::table('kategoris')->insertGetId([
                    'nama_kategori' => $kategori['nama'],
                    'deskripsi' => $kategori['deskripsi'],
                    'status' => 'utama',
                    'is_active' => true,
                    'fakultas_id' => $tujuan->fakultas_id,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);

                foreach ($kategori['soal'] as $soal) {
                    $soalId = DB::table('soal')->insertGetId([
                        'instrumen_id' => $instrumenId,
                        'kategori_id' => $kategoriId,
                        'kode' => $soal['kode'],
                        'soal' => $soal['teks'],
                        'jenis_soal' => $soal['jenis'],
                        'allows_multiple_answers' => $soal['multiple'] ?? false,
                        'allows_custom_answer' => $soal['custom'] ?? false,
                        'is_required' => true,
                        'is_active' => true,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);

                    $pilihan = $soal['jenis'] === 'rating' ? $likert : ($soal['pilihan'] ?? []);
                    foreach ($pilihan as $urutan => $pilihanItem) {
                        $teks = is_array($pilihanItem) ? $pilihanItem['jawaban'] : $pilihanItem;
                        DB::table('jawaban')->insert([
                            'soal_id' => $soalId,
                            'jawaban' => $teks,
                            'nilai' => is_array($pilihanItem) ? $pilihanItem['nilai'] : $urutan + 1,
                            'urutan' => is_array($pilihanItem) ? $pilihanItem['urutan'] : $urutan + 1,
                            'created_at' => $now,
                            'updated_at' => $now,
                        ]);
                    }
                }
            }

            foreach ($namaLulusan as $index => $nama) {
                $nomor = $index + 1;
                $tujuan = $prodi[$index % $prodi->count()];
                $perusahaanId = DB::table('pengguna_lulusan')->insertGetId([
                    'nama_perusahaan' => sprintf('Mitra Simulasi Nusantara 2028 %02d', $nomor),
                    'nama_penyelia' => sprintf('Penyelia Mitra 2028 %02d', $nomor),
                    'jabatan_penyelia' => 'Koordinator Kemitraan',
                    'kontak_penyelia' => sprintf('08132828%04d', $nomor),
                    'email_penyelia' => sprintf('mitra2028.%02d@example.test', $nomor),
                    'jumlah_lulusan' => 1,
                    'durasi_lulusan_bekerja' => 0,
                    'nomor_badan_hukum' => sprintf('AHU-2028-%05d', $nomor),
                    'alamat_perusahaan' => sprintf('Jl. Mitra Baru No. %d, Surabaya', 100 + $nomor),
                    'kontak_perusahaan' => sprintf('031-828%04d', $nomor),
                    'jenis_perusahaan' => 'swasta',
                    'cabang_kota' => $nomor % 4,
                    'cabang_negara' => 0,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);

                DB::table('lulusan')->insert([
                    'pengguna_lulusan_id' => $perusahaanId,
                    'nama' => $nama,
                    'nim' => sprintf('2028001%03d', $nomor),
                    'program_studi' => $tujuan->prodi_nama,
                    'fakultas' => $tujuan->fakultas_kode,
                    'program_studi_id' => $tujuan->prodi_id,
                    'fakultas_id' => $tujuan->fakultas_id,
                    'tahun_lulus' => '2028-08-15',
                    'is_aggregate' => false,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        });

        $this->command->info('Paket data mandiri 2028 selesai: 1 periode, 4 kategori, 11 pertanyaan, 20 perusahaan, dan 20 lulusan.');
        $this->command->line('Tidak ada survei yang dibuat. Buat sesi survei dari menu aplikasi setelah seeder dijalankan.');
    }
}
