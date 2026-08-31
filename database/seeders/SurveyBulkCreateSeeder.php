<?php

namespace Database\Seeders;

use App\Models\lulusan;
use App\Models\soal;
use App\Models\Survey;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SurveyBulkCreateSeeder extends Seeder
{
    public function run(): void
    {
        if (Survey::exists()) {
            $this->command->warn('Survey sudah ada ('.Survey::count().' data). Seeder dilewati untuk mencegah duplikasi.');

            return;
        }

        $tahunInstrumen = 2026;
        $tahunSurveyAkhir = Carbon::now()->year;
        $tahunSurveyAwal = $tahunSurveyAkhir - 3;
        $tahunSurveyList = range($tahunSurveyAwal, $tahunSurveyAkhir);
        $tahunLulusList = array_map(fn ($tahunSurvey) => $tahunSurvey - 1, $tahunSurveyList);

        // Ambil semua soal yang aktif dari instrumen 2026
        $instrumenId = DB::table('instrumen')->where('tahun', $tahunInstrumen)->value('id');

        if (! $instrumenId) {
            $this->command->error("Instrumen tahun {$tahunInstrumen} tidak ditemukan. Jalankan DraftInstrumenUniversitas2026Seeder terlebih dahulu.");

            return;
        }

        $semuaSoal = soal::where('instrumen_id', $instrumenId)
            ->where('is_active', true)
            ->get(['id', 'peruntukan_fakultas']);

        if ($semuaSoal->isEmpty()) {
            $this->command->error("Tidak ada soal aktif di instrumen {$tahunInstrumen}. Seeder dihentikan.");

            return;
        }

        // Survey dilakukan satu tahun setelah mahasiswa lulus.
        $lulusanList = lulusan::whereNotNull('pengguna_lulusan_id')
            ->where(function ($query) use ($tahunLulusList) {
                foreach ($tahunLulusList as $tahunLulus) {
                    $query->orWhereYear('tahun_lulus', $tahunLulus);
                }
            })
            ->get();

        if ($lulusanList->isEmpty()) {
            $this->command->error('Tidak ada data lulusan yang sesuai periode survey empat tahun terakhir dan terhubung ke perusahaan. Jalankan LulusanSeeder terlebih dahulu.');

            return;
        }

        $periode = "{$tahunSurveyAwal}-{$tahunSurveyAkhir}";
        $this->command->info("Membuat survey periode {$periode} untuk {$lulusanList->count()} lulusan...");

        $now = Carbon::now();
        $berhasil = 0;
        $rekap = array_fill_keys($tahunSurveyList, 0);

        DB::transaction(function () use ($lulusanList, $semuaSoal, $now, &$berhasil, &$rekap) {
            foreach ($lulusanList as $lulus) {
                $tahunLulus = Carbon::parse($lulus->tahun_lulus)->year;
                $tahunSurvey = $tahunLulus + 1;

                $survey = Survey::create([
                    'judul' => "Survey Kepuasan Pengguna Lulusan {$tahunSurvey}",
                    'tahun' => $tahunSurvey,
                    'deskripsi' => 'Survey tracer study untuk penilaian kinerja dan kompetensi lulusan oleh pengguna lulusan (mitra industri).',
                    'lulusan_id' => $lulus->id,
                    'pengguna_lulusan_id' => $lulus->pengguna_lulusan_id,
                    'access_code' => strtoupper(Str::random(8)),
                    'is_completed' => false,
                    'is_active' => true,
                ]);

                // Hanya masukkan soal 'Umum' atau sesuai fakultas lulusan
                $soalUntukLulusan = $semuaSoal->filter(fn ($s) => $s->peruntukan_fakultas === 'Umum'
                    || $s->peruntukan_fakultas === $lulus->fakultas
                );

                $rows = $soalUntukLulusan->map(fn ($s) => [
                    'survey_id' => $survey->id,
                    'soal_id' => $s->id,
                    'created_at' => $now,
                    'updated_at' => $now,
                ])->values()->toArray();

                if (! empty($rows)) {
                    DB::table('survey_soal')->insert($rows);
                }

                $berhasil++;
                $rekap[$tahunSurvey] = ($rekap[$tahunSurvey] ?? 0) + 1;
            }
        });

        $this->command->info("Survey berhasil dibuat: {$berhasil} survey.");
        $this->command->line("  Periode survey: {$periode}");
        foreach ($rekap as $tahunSurvey => $jumlah) {
            $this->command->line("  {$tahunSurvey}: {$jumlah} survey");
        }
        $this->command->line("  Soal per survey: Umum + spesifik fakultas dari instrumen {$tahunInstrumen}");
    }
}
