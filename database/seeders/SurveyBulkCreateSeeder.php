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
            $this->command->warn('Survey sudah ada (' . Survey::count() . ' data). Seeder dilewati untuk mencegah duplikasi.');
            return;
        }

        $tahun = 2026;

        // Ambil semua soal yang aktif dari instrumen 2026
        $instrumenId = DB::table('instrumen')->where('tahun', $tahun)->value('id');

        if (!$instrumenId) {
            $this->command->error("Instrumen tahun {$tahun} tidak ditemukan. Jalankan DraftInstrumenUniversitas2026Seeder terlebih dahulu.");
            return;
        }

        $semuaSoal = soal::where('instrumen_id', $instrumenId)
            ->where('is_active', true)
            ->get(['id', 'peruntukan_fakultas']);

        if ($semuaSoal->isEmpty()) {
            $this->command->error('Tidak ada soal aktif di instrumen 2026. Seeder dihentikan.');
            return;
        }

        // Ambil semua lulusan yang sudah terhubung ke perusahaan
        $lulusanList = lulusan::whereNotNull('pengguna_lulusan_id')->get();

        if ($lulusanList->isEmpty()) {
            $this->command->error('Tidak ada data lulusan yang terhubung ke perusahaan. Jalankan LulusanSeeder terlebih dahulu.');
            return;
        }

        $this->command->info("Membuat survey untuk {$lulusanList->count()} lulusan...");

        $now     = Carbon::now();
        $berhasil = 0;

        DB::transaction(function () use ($lulusanList, $semuaSoal, $tahun, $now, &$berhasil) {
            foreach ($lulusanList as $lulus) {
                $survey = Survey::create([
                    'judul'               => "Survey Kepuasan Pengguna Lulusan {$tahun}",
                    'tahun'               => $tahun,
                    'deskripsi'           => 'Survey tracer study untuk penilaian kinerja dan kompetensi lulusan oleh pengguna lulusan (mitra industri).',
                    'lulusan_id'          => $lulus->id,
                    'pengguna_lulusan_id' => $lulus->pengguna_lulusan_id,
                    'access_code'         => strtoupper(Str::random(8)),
                    'is_completed'        => false,
                    'is_active'           => true,
                ]);

                // Hanya masukkan soal 'Umum' atau sesuai fakultas lulusan
                $soalUntukLulusan = $semuaSoal->filter(fn($s) =>
                    $s->peruntukan_fakultas === 'Umum'
                    || $s->peruntukan_fakultas === $lulus->fakultas
                );

                $rows = $soalUntukLulusan->map(fn($s) => [
                    'survey_id'  => $survey->id,
                    'soal_id'    => $s->id,
                    'created_at' => $now,
                    'updated_at' => $now,
                ])->values()->toArray();

                if (!empty($rows)) {
                    DB::table('survey_soal')->insert($rows);
                }

                $berhasil++;
            }
        });

        $this->command->info("Survey berhasil dibuat: {$berhasil} survey.");
        $this->command->line("  Tahun survey: {$tahun}");
        $this->command->line("  Soal per survey: Umum + spesifik fakultas dari instrumen {$tahun}");
    }
}
