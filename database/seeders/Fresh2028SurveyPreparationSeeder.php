<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Paket data mandiri periode 2028.
 *
 * Menggunakan kategori dan pertanyaan aktif yang telah tersedia; seeder ini
 * hanya membuat periode, perusahaan, lulusan, serta survei yang belum diisi.
 * Tidak dipanggil oleh DatabaseSeeder.
 */
class Fresh2028SurveyPreparationSeeder extends Seeder
{
    private const PERIODE_KODE = '2028-MITRA-BARU';

    private const TARGET_PRODI = [
        'Sistem Informasi' => 10,
        'Teknik Komputer' => 15,
        'Manajemen' => 9,
        'Akuntansi' => 7,
        'Desain Komunikasi Visual' => 10,
        'Produksi Film dan Televisi' => 11,
    ];

    public function run(): void
    {
        if (DB::table('survey')->where('judul', 'Survei Mitra Baru 2028')->exists()) {
            $this->command->warn('Survei paket data 2028 sudah tersedia; seeder dilewati untuk mencegah duplikasi.');
            return;
        }

        $prodi = DB::table('program_studi')
            ->join('fakultas', 'fakultas.id', '=', 'program_studi.fakultas_id')
            ->whereIn('program_studi.nama', array_keys(self::TARGET_PRODI))
            ->get([
                'program_studi.id as prodi_id',
                'program_studi.nama as prodi_nama',
                'fakultas.id as fakultas_id',
                'fakultas.kode as fakultas_kode',
            ])
            ->keyBy('prodi_nama');

        $prodiKurang = array_diff(array_keys(self::TARGET_PRODI), $prodi->keys()->all());
        if ($prodiKurang) {
            throw new \RuntimeException('Program studi belum tersedia: ' . implode(', ', $prodiKurang) . '.');
        }

        // Tidak membuat kategori/soal baru. Seluruh pertanyaan aktif yang
        // sudah ada dipasang pada setiap sesi survei 2028.
        $soalAktif = DB::table('soal')
            ->where('is_active', true)
            ->orderBy('kode')
            ->pluck('id');

        if ($soalAktif->isEmpty()) {
            throw new \RuntimeException('Belum ada pertanyaan aktif. Jalankan seeder instrumen yang sudah tersedia terlebih dahulu.');
        }

        $now = now();
        $totalSurvey = 0;

        DB::transaction(function () use ($prodi, $soalAktif, $now, &$totalSurvey) {
            $periodeId = DB::table('periode')->where('kode_periode', self::PERIODE_KODE)->value('id');
            if (! $periodeId) {
                $periodeId = DB::table('periode')->insertGetId([
                    'kode_periode' => self::PERIODE_KODE,
                    'nama_periode' => 'Periode Survei Mitra Baru 2028',
                    'tanggal_mulai' => '2028-01-01',
                    'tanggal_berakhir' => '2028-12-31',
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }

            $nomor = 0;
            foreach (self::TARGET_PRODI as $namaProdi => $jumlah) {
                $profilProdi = $prodi->get($namaProdi);

                for ($urutan = 1; $urutan <= $jumlah; $urutan++) {
                    $nomor++;
                    $perusahaanId = DB::table('pengguna_lulusan')->insertGetId([
                        'nama_perusahaan' => sprintf('Mitra Survei 2028 %02d', $nomor),
                        'nama_penyelia' => sprintf('Penyelia Survei 2028 %02d', $nomor),
                        'jabatan_penyelia' => 'Koordinator Kemitraan',
                        'kontak_penyelia' => sprintf('08132028%04d', $nomor),
                        'email_penyelia' => sprintf('survei2028.%02d@example.test', $nomor),
                        'jumlah_lulusan' => 1,
                        'durasi_lulusan_bekerja' => 0,
                        'nomor_badan_hukum' => sprintf('AHU-2028-%05d', $nomor),
                        'alamat_perusahaan' => sprintf('Jl. Mitra Survei No. %d, Surabaya', 200 + $nomor),
                        'kontak_perusahaan' => sprintf('031820%04d', $nomor),
                        'jenis_perusahaan' => 'Swasta',
                        'cabang_kota' => $nomor % 5,
                        'cabang_negara' => 0,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);

                    $lulusanId = DB::table('lulusan')->insertGetId([
                        'pengguna_lulusan_id' => $perusahaanId,
                        'nama' => sprintf('Lulusan %s 2028 %02d', $this->singkatanProdi($namaProdi), $urutan),
                        'nim' => sprintf('2028%02d%04d', $this->nomorProdi($namaProdi), $urutan),
                        'program_studi' => $profilProdi->prodi_nama,
                        'fakultas' => $profilProdi->fakultas_kode,
                        'program_studi_id' => $profilProdi->prodi_id,
                        'fakultas_id' => $profilProdi->fakultas_id,
                        'tahun_lulus' => '2028-08-15',
                        'is_aggregate' => false,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);

                    $surveyId = DB::table('survey')->insertGetId([
                        'judul' => 'Survei Mitra Baru 2028',
                        'tahun' => 2028,
                        'periode_id' => $periodeId,
                        'deskripsi' => 'Survei evaluasi pengguna lulusan untuk paket data 2028.',
                        'lulusan_id' => $lulusanId,
                        'pengguna_lulusan_id' => $perusahaanId,
                        'access_code' => strtoupper(Str::random(8)),
                        'is_completed' => false,
                        'is_active' => true,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);

                    DB::table('survey_soal')->insert($soalAktif->values()->map(fn ($soalId, $index) => [
                        'survey_id' => $surveyId,
                        'soal_id' => $soalId,
                        'urutan' => $index + 1,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ])->all());

                    $totalSurvey++;
                }
            }
        });

        $this->command->info("Paket data 2028 selesai: {$totalSurvey} perusahaan, lulusan, dan survei aktif belum diisi.");
        $this->command->line('Distribusi: SI 10, Teknik Komputer 15, Manajemen 9, Akuntansi 7, DKV 10, Produksi Film dan Televisi 11.');
        $this->command->line("Kategori dan pertanyaan yang digunakan: {$soalAktif->count()} pertanyaan aktif yang sudah ada.");
    }

    private function singkatanProdi(string $namaProdi): string
    {
        return match ($namaProdi) {
            'Sistem Informasi' => 'SI',
            'Teknik Komputer' => 'TK',
            'Manajemen' => 'MNJ',
            'Akuntansi' => 'AKN',
            'Desain Komunikasi Visual' => 'DKV',
            'Produksi Film dan Televisi' => 'PFT',
        };
    }

    private function nomorProdi(string $namaProdi): int
    {
        return array_search($namaProdi, array_keys(self::TARGET_PRODI), true) + 1;
    }
}
