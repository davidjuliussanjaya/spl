<?php

namespace Database\Seeders;

use App\Models\lulusan;
use App\Models\penggunalulusan;
use App\Models\soal;
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

    public function run(): void
    {
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

        DB::transaction(function () use ($soalAktif, &$totalSurvey): void {
            foreach (self::PERIODS as $periodIndex => $periode) {
                for ($urutan = 1; $urutan <= 4; $urutan++) {
                    $nomorData = ($periodIndex * 4) + ($urutan - 1);
                    $profil = $this->profilLulusan[$urutan - 1];
                    $submittedAt = Carbon::create($periode, 11, min(5 + $urutan, 28), 10, 0, 0);

                    $perusahaan = $this->buatPerusahaan($periode, $urutan, $nomorData, $submittedAt);
                    $lulus = $this->buatLulusan($periode, $urutan, $nomorData, $profil, $perusahaan, $submittedAt);
                    $survey = $this->buatSurvey($periode, $urutan, $lulus, $perusahaan, $submittedAt);

                    $soalUntukLulusan = $soalAktif
                        ->filter(fn ($item) => $item->peruntukan_fakultas === 'Umum'
                            || $item->peruntukan_fakultas === $lulus->fakultas)
                        ->values();

                    // Jika instalasi hanya memiliki soal dengan peruntukan yang berbeda,
                    // tetap gunakan pertanyaan aktif agar survei demo dapat lengkap.
                    if ($soalUntukLulusan->isEmpty()) {
                        $soalUntukLulusan = $soalAktif;
                    }

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
                            'access_code' => $survey->access_code,
                            'judul' => $survey->judul,
                            'submitted_at' => $submittedAt,
                            'tahun_instrumen' => (string) $periode,

                            'lulusan_nama' => $lulus->nama,
                            'lulusan_nim' => $lulus->nim,
                            'lulusan_program_studi' => $lulus->program_studi,
                            'lulusan_fakultas' => $lulus->fakultas,
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
        return soal::with(['jawaban', 'kategori'])
            ->where('is_active', true)
            ->orderBy('kode')
            ->get();
    }

    private function buatPerusahaan(int $periode, int $urutan, int $nomorData, Carbon $timestamp): penggunalulusan
    {
        $email = "seed.spl.{$periode}.{$urutan}@mitra.test";
        $jenisPerusahaan = ['Swasta', 'Startup', 'BUMN/Instansi Pemerintah', 'Nirlaba/Yayasan'][$urutan - 1];

        return penggunalulusan::updateOrCreate(
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
        penggunalulusan $perusahaan,
        Carbon $timestamp,
    ): lulusan {
        $nim = 'SPL' . $periode . str_pad((string) $urutan, 2, '0', STR_PAD_LEFT);

        return lulusan::updateOrCreate(
            ['nim' => $nim],
            [
                'pengguna_lulusan_id' => $perusahaan->id,
                'nama' => $this->namaLulusan[$nomorData],
                'program_studi' => $profil['program_studi'],
                'fakultas' => $profil['fakultas'],
                'tahun_lulus' => Carbon::create($periode - 1, 8, 15),
                'status' => true,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
        );
    }

    private function buatSurvey(
        int $periode,
        int $urutan,
        lulusan $lulus,
        penggunalulusan $perusahaan,
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
        penggunalulusan $perusahaan,
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
                $pilihan = $soalItem->jawaban
                    ->sortBy('nilai')
                    ->values()
                    ->get(($nomorData + $urutanSoal) % max($soalItem->jawaban->count(), 1));

                if (! $pilihan) {
                    continue;
                }

                $jawaban = $pilihan->jawaban;
                $nilai = (int) $pilihan->nilai;
                $jawabanId = $pilihan->id;
                $jawabanSnapshot = $pilihan->jawaban;
            } elseif ($soalItem->jenis_soal === 'essay') {
                $jawaban = 'Lulusan menunjukkan kinerja yang baik dan mampu beradaptasi dengan kebutuhan kerja di perusahaan.';
                $jawabanText = $jawaban;
            } else {
                $pilihan = $soalItem->jawaban->first();
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
}
