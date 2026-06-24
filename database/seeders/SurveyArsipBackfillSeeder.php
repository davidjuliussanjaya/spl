<?php

namespace Database\Seeders;

use App\Models\Survey;
use App\Models\SurveyArsip;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Backfill arsip untuk survey yang sudah completed sebelum fitur survey_arsip ditambahkan.
 * Aman dijalankan berulang kali — hanya memproses survey yang belum punya arsip.
 */
class SurveyArsipBackfillSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil survey completed yang belum ada di survey_arsip
        $sudahDiarsipkan = SurveyArsip::pluck('survey_id')->toArray();

        $surveys = Survey::with([
            'lulusan',
            'penggunaLulusan',
            'soals.jawaban',
            'soals.kategori',
            'soals.instrumen',
        ])
        ->where('is_completed', true)
        ->whereNotIn('id', $sudahDiarsipkan)
        ->get();

        if ($surveys->isEmpty()) {
            $this->command->info('Semua survey sudah diarsipkan. Tidak ada yang perlu diproses.');
            return;
        }

        $this->command->info("Memproses {$surveys->count()} survey untuk diarsipkan...");

        $berhasil = 0;
        $gagal    = 0;

        foreach ($surveys as $survey) {
            try {
                $this->arsipkanSurvey($survey);
                $berhasil++;
                $this->command->line("  ✓ Survey #{$survey->id} ({$survey->access_code}) — {$survey->lulusan?->nama}");
            } catch (\Exception $e) {
                $gagal++;
                $this->command->error("  ✗ Survey #{$survey->id} gagal: {$e->getMessage()}");
            }
        }

        $this->command->info("Selesai. Berhasil: {$berhasil}, Gagal: {$gagal}.");
    }

    private function arsipkanSurvey(Survey $survey): void
    {
        $lulus    = $survey->lulusan;
        $pengguna = $survey->penggunaLulusan;

        // Ambil semua respon dari respon_jawaban
        $respon = DB::table('respon_jawaban')
            ->where('survey_id', $survey->id)
            ->get();

        $namaPengisi    = $respon->first()?->responden ?? $pengguna?->nama_penyelia ?? 'Tidak Diketahui';
        $jumlahBekerja  = $respon->whereNotNull('jumlah_lulusan_bekerja')->first()?->jumlah_lulusan_bekerja;
        $submittedAt    = $respon->first() ? Carbon::parse($respon->first()->created_at) : now();

        // Bangun jawaban_json dari soals yang terhubung ke survey ini
        $soalMap = $survey->soals->keyBy('id');
        $jawabanArsip = [];

        foreach ($soalMap as $soalItem) {
            $responSoal = $respon->where('soal_id', $soalItem->id);
            if ($responSoal->isEmpty()) continue;

            $entry = [
                'kode'     => $soalItem->kode,
                'kategori' => $soalItem->kategori?->nama_kategori,
                'soal'     => $soalItem->soal_text_snapshot ?? $soalItem->soal,
                'jenis'    => $soalItem->jenis_soal,
                'nilai'    => null,
            ];

            if ($soalItem->jenis_soal === 'essay') {
                $entry['jawaban'] = $responSoal->first()?->jawaban_text;
            } elseif ($soalItem->jenis_soal === 'multiple_choice') {
                $teksArr = [];
                foreach ($responSoal as $r) {
                    if ($r->jawaban_text_snapshot) {
                        $teksArr[] = $r->jawaban_text_snapshot;
                    } elseif ($r->jawaban_text) {
                        $teksArr[] = $r->jawaban_text;
                    } elseif ($r->jawaban_id) {
                        $jw = $soalItem->jawaban->firstWhere('id', $r->jawaban_id);
                        if ($jw) $teksArr[] = $jw->jawaban;
                    }
                }
                $entry['jawaban'] = $teksArr;
            } else {
                // rating
                $r  = $responSoal->first();
                $jw = $r?->jawaban_text_snapshot
                    ? null
                    : ($r?->jawaban_id ? $soalItem->jawaban->firstWhere('id', $r->jawaban_id) : null);

                $entry['jawaban'] = $r?->jawaban_text_snapshot ?? $jw?->jawaban;
                $entry['nilai']   = $jw?->nilai;
            }

            $jawabanArsip[$soalItem->kode] = $entry;
        }

        ksort($jawabanArsip);

        SurveyArsip::create([
            'survey_id'       => $survey->id,
            'access_code'     => $survey->access_code,
            'judul'           => $survey->judul,
            'submitted_at'    => $submittedAt,
            'tahun_instrumen' => $survey->soals->first()?->instrumen?->tahun,

            'lulusan_nama'          => $lulus?->nama,
            'lulusan_nim'           => $lulus?->nim,
            'lulusan_program_studi' => $lulus?->program_studi,
            'lulusan_fakultas'      => $lulus?->fakultas,
            'lulusan_tahun_lulus'   => $lulus?->tahun_lulus
                ? Carbon::parse($lulus->tahun_lulus)->format('Y')
                : null,

            'perusahaan_nama'              => $pengguna?->nama_perusahaan,
            'perusahaan_jenis'             => $pengguna?->jenis_perusahaan,
            'perusahaan_alamat'            => $pengguna?->alamat_perusahaan,
            'perusahaan_kontak'            => $pengguna?->kontak_perusahaan,
            'perusahaan_nomor_badan_hukum' => $pengguna?->nomor_badan_hukum,
            'perusahaan_cabang_kota'       => $pengguna?->cabang_kota,
            'perusahaan_cabang_negara'     => $pengguna?->cabang_negara,

            'penyelia_nama'          => $namaPengisi,
            'penyelia_jabatan'       => $pengguna?->jabatan_penyelia,
            'penyelia_email'         => $pengguna?->email_penyelia,
            'penyelia_kontak'        => $pengguna?->kontak_penyelia,
            'jumlah_lulusan_bekerja' => (string) ($jumlahBekerja ?? ''),

            'jawaban_json' => array_values($jawabanArsip),
        ]);
    }
}
