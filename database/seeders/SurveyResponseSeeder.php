<?php

namespace Database\Seeders;

use App\Models\Survey;
use App\Models\SurveyArsip;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SurveyResponseSeeder extends Seeder
{
    private array $namaPenyelia = [
        'Budi Santoso', 'Dewi Rahayu', 'Ahmad Fauzi', 'Siti Nurhaliza',
        'Rendra Pratama', 'Oktavia Lestari', 'Hendra Wijaya', 'Joko Susilo',
    ];

    private array $jabatanList = [
        'HRD Manager', 'Direktur SDM', 'Kepala Divisi', 'Supervisor',
        'Manajer Operasional', 'General Manager', 'Staff Pengembangan SDM',
    ];

    private array $esaiMasukan = [
        'Lulusan sudah memiliki kompetensi yang baik, namun perlu peningkatan pada kemampuan komunikasi lintas departemen.',
        'Kami berharap lulusan lebih siap dengan praktik kerja nyata, terutama dalam penggunaan tools industri.',
        'Secara keseluruhan lulusan menunjukkan etos kerja yang baik dan mudah beradaptasi dengan lingkungan kerja.',
        'Perlu ditingkatkan kemampuan berpikir kritis dan inisiatif dalam menyelesaikan masalah tanpa harus selalu diarahkan.',
        'Lulusan cukup baik, kami menyarankan agar kurikulum lebih banyak menekankan proyek nyata dan kolaborasi tim.',
        'Kemampuan teknis sudah memadai, tetapi soft skill seperti manajemen waktu dan komunikasi perlu lebih diasah.',
        'Sangat memuaskan. Lulusan memiliki pemahaman yang kuat tentang bidang studi dan mampu menerapkannya di tempat kerja.',
        'Lulusan perlu lebih banyak terpapar dengan tantangan industri nyata selama masa studi.',
    ];

    public function run(): void
    {
        $surveys = Survey::with(['lulusan', 'penggunaLulusan', 'soals.jawaban', 'soals.kategori', 'soals.instrumen'])
            ->where('is_completed', false)
            ->where('is_active', true)
            ->get();

        if ($surveys->isEmpty()) {
            $this->command->warn('Tidak ada survey aktif yang belum diisi. Seeder dilewati.');

            return;
        }

        $this->command->info("Ditemukan {$surveys->count()} survey yang akan diisi...");

        $berhasil = 0;
        $gagal = 0;

        foreach ($surveys as $survey) {
            try {
                DB::transaction(function () use ($survey) {
                    $this->isiSurvey($survey);
                });
                $berhasil++;
                $this->command->line("  ✓ Survey #{$survey->id} ({$survey->access_code}) untuk {$survey->lulusan->nama}");
            } catch (\Exception $e) {
                $gagal++;
                $this->command->error("  ✗ Survey #{$survey->id} gagal: {$e->getMessage()}");
            }
        }

        $this->command->info("Selesai. Berhasil: {$berhasil}, Gagal: {$gagal}.");

        if ($gagal > 0) {
            throw new \RuntimeException("{$gagal} survey gagal di-seed. Periksa pesan kesalahan di atas.");
        }
    }

    private function isiSurvey(Survey $survey): void
    {
        $pengguna = $survey->penggunaLulusan;
        $namaPengisi = $pengguna?->nama_penyelia ?? $this->acak($this->namaPenyelia);
        $jabatan = $pengguna?->jabatan_penyelia ?? $this->acak($this->jabatanList);
        $jumlahBekerja = rand(1, 5);
        $now = $survey->lulusan?->tahun_lulus
            ? Carbon::parse($survey->lulusan->tahun_lulus)->addYear()
            : Carbon::create($survey->tahun ?? Carbon::now()->year, 1, 1);
        $isFirst = true;

        $fakultas = $survey->lulusan->fakultas ?? null;

        $soals = $survey->soals->filter(fn ($s) => $s->peruntukan_fakultas === 'Umum' || ($fakultas && $s->peruntukan_fakultas === $fakultas)
        );

        // Rekam pilihan jawaban untuk arsip
        $jawabanArsip = [];

        foreach ($soals as $soalItem) {
            $jawabanList = $soalItem->jawaban;
            $pilihanUntukArsip = null;

            switch ($soalItem->jenis_soal) {
                case 'rating':
                    $pilihan = $this->pilihRatingBerbobot($jawabanList);
                    if ($pilihan) {
                        DB::table('respon_jawaban')->insert([
                            'survey_id' => $survey->id,
                            'soal_id' => $soalItem->id,
                            'soal_text_snapshot' => $soalItem->soal,
                            'jawaban_id' => $pilihan->id,
                            'jawaban_text_snapshot' => $pilihan->jawaban,
                            'jawaban_text' => null,
                            'responden' => $namaPengisi,
                            'jumlah_lulusan_bekerja' => $isFirst ? $jumlahBekerja : null,
                            'created_at' => $now,
                            'updated_at' => $now,
                        ]);
                        $pilihanUntukArsip = ['teks' => $pilihan->jawaban, 'nilai' => $pilihan->nilai];
                    }
                    break;

                case 'essay':
                    $esai = $this->acak($this->esaiMasukan);
                    DB::table('respon_jawaban')->insert([
                        'survey_id' => $survey->id,
                        'soal_id' => $soalItem->id,
                        'soal_text_snapshot' => $soalItem->soal,
                        'jawaban_id' => null,
                        'jawaban_text_snapshot' => null,
                        'jawaban_text' => $esai,
                        'responden' => $namaPengisi,
                        'jumlah_lulusan_bekerja' => $isFirst ? $jumlahBekerja : null,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
                    $pilihanUntukArsip = $esai;
                    break;

                case 'multiple_choice':
                    $dipilih = $jawabanList->whereNotIn('jawaban', ['Lainnya'])
                        ->shuffle()->take(rand(1, min(3, $jawabanList->count())));
                    $firstRow = true;
                    $teksArr = [];
                    foreach ($dipilih as $jw) {
                        DB::table('respon_jawaban')->insert([
                            'survey_id' => $survey->id,
                            'soal_id' => $soalItem->id,
                            'soal_text_snapshot' => $soalItem->soal,
                            'jawaban_id' => $jw->id,
                            'jawaban_text_snapshot' => $jw->jawaban,
                            'jawaban_text' => null,
                            'responden' => $namaPengisi,
                            'jumlah_lulusan_bekerja' => ($isFirst && $firstRow) ? $jumlahBekerja : null,
                            'created_at' => $now,
                            'updated_at' => $now,
                        ]);
                        $teksArr[] = $jw->jawaban;
                        $firstRow = false;
                    }
                    $pilihanUntukArsip = $teksArr;
                    break;
            }

            $jawabanArsip[$soalItem->kode] = [
                'kode' => $soalItem->kode,
                'kategori' => $soalItem->kategori?->nama_kategori,
                'soal' => $soalItem->soal,
                'jenis' => $soalItem->jenis_soal,
                'jawaban' => is_array($pilihanUntukArsip)
                    ? (isset($pilihanUntukArsip['teks']) ? $pilihanUntukArsip['teks'] : $pilihanUntukArsip)
                    : $pilihanUntukArsip,
                'nilai' => is_array($pilihanUntukArsip) && isset($pilihanUntukArsip['nilai'])
                    ? $pilihanUntukArsip['nilai']
                    : null,
            ];

            $isFirst = false;
        }

        // Update data penyelia
        if ($pengguna) {
            $pengguna->update([
                'nama_penyelia' => $namaPengisi,
                'jabatan_penyelia' => $jabatan,
                'jumlah_lulusan' => $pengguna->jumlah_lulusan ?? $jumlahBekerja,
            ]);
        }

        $survey->update(['is_completed' => true]);

        // Tulis arsip permanen — tidak ada FK, data tidak bisa hilang
        ksort($jawabanArsip);
        $lulus = $survey->lulusan;

        SurveyArsip::create([
            'survey_id' => $survey->id,
            'access_code' => $survey->access_code,
            'judul' => $survey->judul,
            'submitted_at' => $now,
            'tahun_instrumen' => $survey->tahun,

            'lulusan_nama' => $lulus?->nama,
            'lulusan_nim' => $lulus?->nim,
            'lulusan_program_studi' => $lulus?->program_studi,
            'lulusan_fakultas' => $lulus?->fakultas,
            'lulusan_tahun_lulus' => $lulus?->tahun_lulus
                ? Carbon::parse($lulus->tahun_lulus)->format('Y')
                : null,

            'perusahaan_nama' => $pengguna?->nama_perusahaan,
            'perusahaan_jenis' => $pengguna?->jenis_perusahaan,
            'perusahaan_alamat' => $pengguna?->alamat_perusahaan,
            'perusahaan_kontak' => $pengguna?->kontak_perusahaan,
            'perusahaan_nomor_badan_hukum' => $pengguna?->nomor_badan_hukum,
            'perusahaan_cabang_kota' => $pengguna?->cabang_kota,
            'perusahaan_cabang_negara' => $pengguna?->cabang_negara,

            'penyelia_nama' => $namaPengisi,
            'penyelia_jabatan' => $jabatan,
            'penyelia_email' => $pengguna?->email_penyelia,
            'penyelia_kontak' => $pengguna?->kontak_penyelia,
            'jumlah_lulusan_bekerja' => (string) $jumlahBekerja,

            'jawaban_json' => array_values($jawabanArsip),
        ]);
    }

    private function pilihRatingBerbobot($jawabanList)
    {
        if ($jawabanList->isEmpty()) {
            return null;
        }

        $bobot = $jawabanList->mapWithKeys(fn ($j) => [$j->id => max(1, $j->nilai)]);
        $total = $bobot->sum();
        $rand = rand(1, $total);
        $kumulatif = 0;

        foreach ($bobot as $id => $b) {
            $kumulatif += $b;
            if ($rand <= $kumulatif) {
                return $jawabanList->firstWhere('id', $id);
            }
        }

        return $jawabanList->first();
    }

    private function acak(array $arr): string
    {
        return $arr[array_rand($arr)];
    }
}
