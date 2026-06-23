<?php

namespace Database\Seeders;

use App\Models\lulusan;
use App\Models\penggunalulusan;
use App\Models\ResponJawaban;
use App\Models\soal;
use App\Models\Survey;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SurveyResponseSeeder extends Seeder
{
    private array $namaPenyelia = [
        'Budi Santoso', 'Dewi Rahayu', 'Ahmad Fauzi', 'Siti Nurhaliza',
        'Rendra Pratama', 'Oktavia Lestari', 'Hendra Wijaya', 'Joko Susilo',
    ];

    private array $jabatan = [
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
        $surveys = Survey::with(['lulusan', 'penggunaLulusan', 'soals.jawaban'])
            ->where('is_completed', false)
            ->where('is_active', true)
            ->get();

        if ($surveys->isEmpty()) {
            $this->command->warn('Tidak ada survey aktif yang belum diisi. Seeder dilewati.');
            return;
        }

        $this->command->info("Ditemukan {$surveys->count()} survey yang akan diisi...");

        $berhasil = 0;
        $gagal    = 0;

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
    }

    private function isiSurvey(Survey $survey): void
    {
        $pengguna    = $survey->penggunaLulusan;
        $namaPengisi = $pengguna?->nama_penyelia ?? $this->namaPengisi();
        $now         = Carbon::now();
        $isFirst     = true;

        // Soal yang harus diisi: filter sesuai fakultas lulusan
        $fakultas = $survey->lulusan->fakultas ?? null;

        $soals = $survey->soals->filter(function ($soal) use ($fakultas) {
            return $soal->peruntukan_fakultas === 'Umum'
                || ($fakultas && $soal->peruntukan_fakultas === $fakultas);
        });

        foreach ($soals as $soalItem) {
            $jawabanList = $soalItem->jawaban;

            match ($soalItem->jenis_soal) {
                'rating' => $this->simpanRating(
                    $survey, $soalItem, $jawabanList, $namaPengisi, $isFirst, $now
                ),
                'essay'  => $this->simpanEssay(
                    $survey, $soalItem, $namaPengisi, $isFirst, $now
                ),
                'multiple_choice' => $this->simpanMultipleChoice(
                    $survey, $soalItem, $jawabanList, $namaPengisi, $isFirst, $now
                ),
                default => null,
            };

            $isFirst = false;
        }

        // Update data penyelia perusahaan
        if ($pengguna) {
            $pengguna->update([
                'nama_penyelia'    => $namaPengisi,
                'jabatan_penyelia' => $pengguna->jabatan_penyelia ?? $this->randomJabatan(),
                'jumlah_lulusan'   => $pengguna->jumlah_lulusan ?? rand(1, 5),
            ]);
        }

        $survey->update(['is_completed' => true]);
    }

    private function simpanRating(Survey $survey, $soal, $jawabanList, string $namaPengisi, bool $isFirst, Carbon $now): void
    {
        if ($jawabanList->isEmpty()) return;

        // Cenderung pilih jawaban yang lebih baik (nilai 3-4)
        $bobot    = $jawabanList->mapWithKeys(fn($j) => [$j->id => max(1, $j->nilai)]);
        $total    = $bobot->sum();
        $rand     = rand(1, $total);
        $kumulatif = 0;
        $pilihan  = $jawabanList->first();

        foreach ($bobot as $id => $b) {
            $kumulatif += $b;
            if ($rand <= $kumulatif) {
                $pilihan = $jawabanList->firstWhere('id', $id);
                break;
            }
        }

        DB::table('respon_jawaban')->insert([
            'survey_id'              => $survey->id,
            'soal_id'                => $soal->id,
            'jawaban_id'             => $pilihan->id,
            'jawaban_text'           => null,
            'responden'              => $namaPengisi,
            'jumlah_lulusan_bekerja' => $isFirst ? rand(1, 5) : null,
            'created_at'             => $now,
            'updated_at'             => $now,
        ]);
    }

    private function simpanEssay(Survey $survey, $soal, string $namaPengisi, bool $isFirst, Carbon $now): void
    {
        DB::table('respon_jawaban')->insert([
            'survey_id'              => $survey->id,
            'soal_id'                => $soal->id,
            'jawaban_id'             => null,
            'jawaban_text'           => $this->randomEsai(),
            'responden'              => $namaPengisi,
            'jumlah_lulusan_bekerja' => $isFirst ? rand(1, 5) : null,
            'created_at'             => $now,
            'updated_at'             => $now,
        ]);
    }

    private function simpanMultipleChoice(Survey $survey, $soal, $jawabanList, string $namaPengisi, bool $isFirst, Carbon $now): void
    {
        if ($jawabanList->isEmpty()) return;

        // Pilih 1-3 jawaban secara acak (tapi bukan "Lainnya" agar tidak perlu mc_custom)
        $pilihan  = $jawabanList->whereNotIn('jawaban', ['Lainnya'])->shuffle()->take(rand(1, min(3, $jawabanList->count())));
        $firstRow = true;

        foreach ($pilihan as $jawaban) {
            DB::table('respon_jawaban')->insert([
                'survey_id'              => $survey->id,
                'soal_id'                => $soal->id,
                'jawaban_id'             => $jawaban->id,
                'jawaban_text'           => null,
                'responden'              => $namaPengisi,
                'jumlah_lulusan_bekerja' => ($isFirst && $firstRow) ? rand(1, 5) : null,
                'created_at'             => $now,
                'updated_at'             => $now,
            ]);
            $firstRow = false;
        }
    }

    private function namaPengisi(): string
    {
        return $this->namaPenyelia[array_rand($this->namaPenyelia)];
    }

    private function randomJabatan(): string
    {
        return $this->jabatan[array_rand($this->jabatan)];
    }

    private function randomEsai(): string
    {
        return $this->esaiMasukan[array_rand($this->esaiMasukan)];
    }
}
