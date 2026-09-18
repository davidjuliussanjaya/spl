<?php

namespace Database\Seeders;

use App\Models\Lulusan;
use App\Models\PenggunaLulusan;
use App\Models\Periode;
use App\Models\ProgramStudi;
use App\Models\Soal;
use App\Models\Survey;
use App\Models\SurveyArsip;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Data demo survei selesai untuk empat periode.
 *
 * Seeder ini tidak membuat atau mengubah aspek evaluasi dan pertanyaan.
 * Semua pertanyaan aktif beserta kategorinya diambil dari database yang ada
 * saat dijalankan, lalu disalin ke setiap survei dan arsip jawabannya.
 */
class CompletedSurveyPeriodsSeeder extends Seeder
{
    private const PERIODS = [2023, 2024, 2025, 2026];

    /**
     * Target mutu untuk 16 survei demo (4 survei setiap periode).
     *
     * 2023 didominasi penilaian rendah, 2024 cukup, 2025 baik, dan 2026
     * sengaja campuran. Pola ini membuat grafik serta kategori terbaik dan
     * terendah dapat diuji dengan data yang nyata, bukan data seragam.
     */
    private const TARGET_RATING_BY_SURVEY = [
        1, 2, 2, 3, // 2023: perlu perbaikan
        2, 3, 3, 2, // 2024: cukup hingga baik
        3, 4, 3, 4, // 2025: baik hingga sangat baik
        4, 3, 4, 2, // 2026: campuran untuk perbandingan
    ];

    /** Variasi kecil per pertanyaan agar satu respons tidak bernilai identik seluruhnya. */
    private const QUESTION_RATING_ADJUSTMENTS = [0, 0, -1, 0, 1, 0, -1, 0, 1, 0];

    private array $profilLulusan = [
        ['fakultas' => 'FTI',  'program_studi' => 'Teknik Informatika'],
        ['fakultas' => 'FDIK', 'program_studi' => 'Desain Komunikasi Visual'],
        ['fakultas' => 'FEB',  'program_studi' => 'Akuntansi'],
        ['fakultas' => 'FTI',  'program_studi' => 'Sistem Informasi'],
    ];

    private array $namaLulusan = [
        'Alya Prameswari', 'Bagas Wicaksono', 'Cahaya Permata', 'Daffa Ramadhan',
        'Elsa Maharani', 'Fikri Alamsyah', 'Gita Lestari', 'Hafiz Pratama',
        'Intan Kurniawati', 'Jovan Aditya', 'Kayla Nurfadila', 'Luthfi Maulana',
        'Maya Anindita', 'Naufal Rizki', 'Olivia Safitri', 'Pandega Putra',
    ];

    private array $namaPerusahaan = [
        'PT Arunika Teknologi', 'Studio Kreasi Visual', 'PT Cakrawala Finansial', 'PT Sistem Cerdas Indonesia',
        'PT Nusa Data Solusi', 'CV Lentera Kreatif', 'Koperasi Sejahtera Bersama', 'PT Digital Mitra Utama',
        'PT Pilar Inovasi', 'Agensi Rupa Nusantara', 'PT Konsultan Bisnis Mandiri', 'PT Jaringan Informatika Raya',
        'PT Teknologi Harmoni', 'Rumah Desain Komunika', 'PT Investama Prima', 'PT Solusi Aplikasi Nusantara',
    ];

    private array $namaPenyelia = [
        'Rama Wijaya', 'Salsa Azzahra', 'Dion Prasetyo', 'Nadia Kusuma',
        'Farhan Akbar', 'Rani Oktaviani', 'Galih Santoso', 'Vera Handayani',
        'Yusuf Kurniawan', 'Tania Puspita', 'Rizal Mahendra', 'Wulan Sari',
        'Agus Firmansyah', 'Cindy Larasati', 'Bima Saputra', 'Dinda Amelia',
    ];

    private array $komentarPositif = [
        'Kinerja lulusan sangat memuaskan. Ia cepat beradaptasi, mandiri, dan mampu memberi kontribusi nyata pada tim.',
        'Lulusan menunjukkan kompetensi yang kuat, komunikasi yang baik, serta kesiapan kerja di atas harapan perusahaan.',
        'Kami sangat puas dengan kualitas lulusan, khususnya pada ketelitian, inisiatif, dan kemampuan menyelesaikan masalah.',
    ];

    private array $komentarCukup = [
        'Lulusan mampu menjalankan tugas dengan baik, namun masih perlu pendampingan pada beberapa proses kerja baru.',
        'Secara umum kinerjanya cukup baik. Penguatan komunikasi dan manajemen waktu akan membuat kontribusinya lebih optimal.',
        'Kompetensi dasar sudah memadai, tetapi pengalaman menghadapi kasus kerja nyata perlu terus ditingkatkan.',
    ];

    private array $komentarPerluPerbaikan = [
        'Lulusan masih memerlukan banyak arahan dalam menyelesaikan tugas. Kemampuan komunikasi dan inisiatif perlu diperkuat.',
        'Kesiapan kerja belum konsisten. Kami menyarankan lebih banyak praktik industri, kerja tim, dan latihan pemecahan masalah.',
        'Beberapa kompetensi dasar belum memenuhi kebutuhan kerja saat ini, terutama ketelitian dan adaptasi terhadap target kerja.',
    ];

    public function run(): void
    {
        $this->call(FakultasProgramStudiSeeder::class);
        $soalAktif = $this->ambilSoalAktif();

        // Database baru belum memiliki aspek evaluasi dan pertanyaan. Siapkan
        // instrumen standar terlebih dahulu; bila data sudah ada, tidak ada
        // aspek/pertanyaan yang diubah atau ditimpa.
        if ($soalAktif->isEmpty()) {
            $this->command->info('Belum ada pertanyaan aktif. Menjalankan seeder aspek evaluasi dan instrumen standar.');
            $this->call(DraftInstrumenUniversitas2026Seeder::class);
            $soalAktif = $this->ambilSoalAktif();
        }

        if ($soalAktif->isEmpty()) {
            $this->command->error('Seeder dibatalkan: aspek evaluasi dan pertanyaan aktif tidak berhasil disiapkan.');

            return;
        }

        $totalSurvey = 0;

        // Gunakan satu acuan waktu agar data demo untuk periode berjalan tidak
        // memperoleh timestamp yang melampaui waktu seeder dijalankan.
        $seededAt = now();

        DB::transaction(function () use ($soalAktif, $seededAt, &$totalSurvey): void {
            foreach (self::PERIODS as $periodIndex => $periode) {
                $periodeMaster = Periode::firstOrCreate(
                    ['kode_periode' => (string) $periode],
                    [
                        'nama_periode' => "Periode Survei {$periode}",
                        'tanggal_mulai' => "{$periode}-01-01",
                        'tanggal_berakhir' => "{$periode}-12-31",
                    ],
                );
                for ($urutan = 1; $urutan <= 4; $urutan++) {
                    $nomorData = ($periodIndex * 4) + ($urutan - 1);
                    $profil = $this->profilLulusan[$urutan - 1];
                    $targetSubmittedAt = Carbon::create($periode, 11, min(5 + $urutan, 28), 10, 0, 0);
                    $submittedAt = $targetSubmittedAt->isFuture()
                        ? $seededAt->copy()->subMinutes(4 - $urutan)
                        : $targetSubmittedAt;

                    $perusahaan = $this->buatPerusahaan($periode, $urutan, $nomorData, $submittedAt);
                    $lulus = $this->buatLulusan($periode, $urutan, $nomorData, $profil, $perusahaan, $submittedAt);
                    $survey = $this->buatSurvey($periode, $periodeMaster, $urutan, $lulus, $perusahaan, $submittedAt);

                    $soalUntukLulusan = $soalAktif->values();

                    $survey->soals()->sync($soalUntukLulusan->pluck('id')->all());
                    DB::table('respon_jawaban')->where('survey_id', $survey->id)->delete();

                    $jawabanArsip = $this->isiJawaban(
                        $survey,
                        $soalUntukLulusan,
                        $nomorData,
                        $perusahaan,
                        $submittedAt,
                    );

                    $survey->update([
                        'is_completed' => true,
                        'is_active' => true,
                        'updated_at' => $submittedAt,
                    ]);

                    SurveyArsip::updateOrCreate(
                        ['survey_id' => $survey->id],
                        [
                            'pengguna_lulusan_id' => $survey->pengguna_lulusan_id,
                            'access_code' => $survey->access_code,
                            'judul' => $survey->judul,
                            'periode_kode' => $periodeMaster->kode_periode,
                            'periode_nama' => $periodeMaster->nama_periode,
                            'periode_tanggal_mulai' => $periodeMaster->tanggal_mulai,
                            'periode_tanggal_berakhir' => $periodeMaster->tanggal_berakhir,
                            'submitted_at' => $submittedAt,
                            'tahun_instrumen' => (string) $periode,

                            'lulusan_nama' => $lulus->nama,
                            'lulusan_nim' => $lulus->nim,
                            'lulusan_program_studi' => $lulus->programStudi?->nama,
                            'lulusan_fakultas' => $lulus->fakultasMaster?->kode,
                            'lulusan_tahun_lulus' => Carbon::parse($lulus->tahun_lulus)->format('Y'),

                            'perusahaan_nama' => $perusahaan->nama_perusahaan,
                            'perusahaan_jenis' => $perusahaan->jenis_perusahaan,
                            'perusahaan_alamat' => $perusahaan->alamat_perusahaan,
                            'perusahaan_kontak' => $perusahaan->kontak_perusahaan,
                            'perusahaan_nomor_badan_hukum' => $perusahaan->nomor_badan_hukum,
                            'perusahaan_cabang_kota' => (string) $perusahaan->cabang_kota,
                            'perusahaan_cabang_negara' => (string) $perusahaan->cabang_negara,

                            'penyelia_nama' => $perusahaan->nama_penyelia,
                            'penyelia_jabatan' => $perusahaan->jabatan_penyelia,
                            'penyelia_email' => $perusahaan->email_penyelia,
                            'penyelia_kontak' => $perusahaan->kontak_penyelia,
                            'jumlah_lulusan_bekerja' => (string) $perusahaan->jumlah_lulusan,
                            'jawaban_json' => array_values($jawabanArsip),
                        ],
                    );

                    $totalSurvey++;
                }
            }
        });

        $this->command->info("CompletedSurveyPeriodsSeeder selesai: {$totalSurvey} survei selesai dibuat/diperbarui.");
        $this->command->line('Periode: 2023, 2024, 2025, 2026 — masing-masing 4 survei.');
        $this->command->line("Pertanyaan aktif yang digunakan: {$soalAktif->count()}.");
    }

    private function ambilSoalAktif()
    {
        return Soal::with(['jawaban', 'kategori'])
            ->where('is_active', true)
            ->orderBy('kode')
            ->get();
    }

    private function buatPerusahaan(int $periode, int $urutan, int $nomorData, Carbon $timestamp): PenggunaLulusan
    {
        $email = "seed.spl.{$periode}.{$urutan}@mitra.test";
        $jenisPerusahaan = ['Swasta', 'Startup', 'BUMN/Instansi Pemerintah', 'Nirlaba/Yayasan'][$urutan - 1];

        return PenggunaLulusan::updateOrCreate(
            ['email_penyelia' => $email],
            [
                'nama_perusahaan' => $this->namaPerusahaan[$nomorData],
                'nama_penyelia' => $this->namaPenyelia[$nomorData],
                'jabatan_penyelia' => ['HR Manager', 'Creative Director', 'Finance Manager', 'IT Manager'][$urutan - 1],
                'kontak_penyelia' => '0812' . str_pad((string) (5100000 + $nomorData), 8, '0', STR_PAD_LEFT),
                'jumlah_lulusan' => 1,
                'durasi_lulusan_bekerja' => 12 + (($nomorData % 4) * 6),
                'nomor_badan_hukum' => 'AHU-SPL-' . $periode . '-' . str_pad((string) $urutan, 2, '0', STR_PAD_LEFT),
                'alamat_perusahaan' => 'Jl. Mitra Lulusan No. ' . (10 + $nomorData) . ', Surabaya',
                'kontak_perusahaan' => '031' . (7000000 + $nomorData),
                'jenis_perusahaan' => $jenisPerusahaan,
                'cabang_kota' => $urutan % 3,
                'cabang_negara' => $urutan === 1 ? 1 : 0,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
        );
    }

    private function buatLulusan(
        int $periode,
        int $urutan,
        int $nomorData,
        array $profil,
        PenggunaLulusan $perusahaan,
        Carbon $timestamp,
    ): Lulusan {
        $nim = 'SPL' . $periode . str_pad((string) $urutan, 2, '0', STR_PAD_LEFT);

        $programStudi = ProgramStudi::with('fakultas')
            ->where('nama', $profil['program_studi'])
            ->whereHas('fakultas', fn ($query) => $query->where('kode', $profil['fakultas']))
            ->firstOrFail();

        return Lulusan::updateOrCreate(
            ['nim' => $nim],
            [
                'pengguna_lulusan_id' => $perusahaan->id,
                'nama' => $this->namaLulusan[$nomorData],
                'program_studi' => $profil['program_studi'],
                'fakultas' => $profil['fakultas'],
                'program_studi_id' => $programStudi->id,
                'fakultas_id' => $programStudi->fakultas_id,
                'tahun_lulus' => Carbon::create($periode - 1, 8, 15),
                'status' => true,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
        );
    }

    private function buatSurvey(
        int $periode,
        Periode $periodeMaster,
        int $urutan,
        Lulusan $lulus,
        PenggunaLulusan $perusahaan,
        Carbon $timestamp,
    ): Survey {
        $kodeAkses = 'SP' . substr((string) $periode, -2) . str_pad((string) $urutan, 2, '0', STR_PAD_LEFT);

        return Survey::updateOrCreate(
            ['access_code' => $kodeAkses],
            [
                'lulusan_id' => $lulus->id,
                'pengguna_lulusan_id' => $perusahaan->id,
                'judul' => "Survey Kepuasan Pengguna Lulusan {$periode}",
                'tahun' => $periode,
                'periode_id' => $periodeMaster->id,
                'deskripsi' => 'Data demo survei selesai untuk evaluasi pengguna lulusan.',
                'is_completed' => true,
                'is_active' => true,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
        );
    }

    private function isiJawaban(
        Survey $survey,
        $soalList,
        int $nomorData,
        PenggunaLulusan $perusahaan,
        Carbon $timestamp,
    ): array {
        $jawabanArsip = [];
        $sudahSimpanJumlahLulusan = false;

        foreach ($soalList as $urutanSoal => $soalItem) {
            $kode = $soalItem->kode ?: "SOAL-{$soalItem->id}";
            $jawaban = null;
            $nilai = null;
            $jawabanId = null;
            $jawabanSnapshot = null;
            $jawabanText = null;

            if ($soalItem->jenis_soal === 'rating') {
                $targetNilai = $this->tentukanTargetRating($nomorData, $urutanSoal);
                $pilihan = $soalItem->jawaban
                    ->sortBy(fn ($item) => abs(((int) $item->nilai) - $targetNilai))
                    ->first();

                if (! $pilihan) {
                    continue;
                }

                $jawaban = $pilihan->jawaban;
                $nilai = (int) $pilihan->nilai;
                $jawabanId = $pilihan->id;
                $jawabanSnapshot = $pilihan->jawaban;
            } elseif ($soalItem->jenis_soal === 'essay') {
                $jawaban = $this->tentukanKomentar($nomorData, $urutanSoal);
                $jawabanText = $jawaban;
            } else {
                $pilihan = $soalItem->jawaban
                    ->values()
                    ->get(($nomorData + $urutanSoal) % max($soalItem->jawaban->count(), 1));
                if (! $pilihan) {
                    continue;
                }

                $jawaban = [$pilihan->jawaban];
                $jawabanId = $pilihan->id;
                $jawabanSnapshot = $pilihan->jawaban;
            }

            DB::table('respon_jawaban')->insert([
                'survey_id' => $survey->id,
                'soal_id' => $soalItem->id,
                'jawaban_id' => $jawabanId,
                'jawaban_text' => $jawabanText,
                'soal_text_snapshot' => $soalItem->soal,
                'jawaban_text_snapshot' => $jawabanSnapshot,
                'responden' => $perusahaan->nama_penyelia,
                'jumlah_lulusan_bekerja' => $sudahSimpanJumlahLulusan ? null : $perusahaan->jumlah_lulusan,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ]);

            $jawabanArsip[$kode] = [
                'kode' => $kode,
                'kategori' => $soalItem->kategori?->nama_kategori,
                'soal' => $soalItem->soal,
                'jenis' => $soalItem->jenis_soal,
                'jawaban' => $jawaban,
                'nilai' => $nilai,
            ];
            $sudahSimpanJumlahLulusan = true;
        }

        ksort($jawabanArsip);

        return $jawabanArsip;
    }

    private function tentukanTargetRating(int $nomorData, int $urutanSoal): int
    {
        $targetDasar = self::TARGET_RATING_BY_SURVEY[$nomorData] ?? 3;
        $variasi = self::QUESTION_RATING_ADJUSTMENTS[
            ($nomorData + $urutanSoal) % count(self::QUESTION_RATING_ADJUSTMENTS)
        ];

        return max(1, min(4, $targetDasar + $variasi));
    }

    private function tentukanKomentar(int $nomorData, int $urutanSoal): string
    {
        $targetDasar = self::TARGET_RATING_BY_SURVEY[$nomorData] ?? 3;
        $komentar = match (true) {
            $targetDasar >= 4 => $this->komentarPositif,
            $targetDasar === 3 => $this->komentarCukup,
            default => $this->komentarPerluPerbaikan,
        };

        return $komentar[($nomorData + $urutanSoal) % count($komentar)];
    }
}
