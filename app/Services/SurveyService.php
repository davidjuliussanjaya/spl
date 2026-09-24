<?php

namespace App\Services;

use App\Models\Lulusan;
use App\Models\Periode;
use App\Models\PenggunaLulusan;
use App\Models\ResponJawaban;
use App\Models\Soal;
use App\Models\Jawaban;
use App\Models\Survey;
use App\Models\SurveyArsip;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SurveyService
{
    public function createSurvey(array $data)
    {
        return DB::transaction(function () use ($data) {
            $periode = Periode::findOrFail($data['periode_id']);
            $this->pastikanPeriodeBerlangsung($periode);
            $pengguna = PenggunaLulusan::findOrFail($data['pengguna_lulusan_id']);
            $lulus = Lulusan::findOrFail($data['lulusan_id']);

            $pengguna->update([
                'nama_penyelia'     => $data['nama'] ?? $pengguna->nama_penyelia,
                'kontak_penyelia'   => $data['hp'] ?? $pengguna->kontak_penyelia,
                'email_penyelia'    => $data['email'] ?? $pengguna->email_penyelia,
                'nomor_badan_hukum' => $data['badan_hukum'] ?? $pengguna->nomor_badan_hukum,
                'kontak_perusahaan' => $data['telp_perusahaan'] ?? $pengguna->kontak_perusahaan,
                'alamat_perusahaan' => $data['alamat_perusahaan'] ?? $pengguna->alamat_perusahaan,
            ]);

            $survey = Survey::create([
                'judul'               => $data['judul'],
                // Kolom tahun dipertahankan untuk kompatibilitas data lama.
                'tahun'               => $periode->tanggal_mulai->year,
                'periode_id'          => $periode->id,
                'deskripsi'           => $data['deskripsi'] ?? null,
                'lulusan_id'          => $lulus->id,
                'pengguna_lulusan_id' => $data['pengguna_lulusan_id'],
                'access_code'         => strtoupper(Str::random(8)),
                'is_completed'        => false,
                'is_active'           => true,
            ]);

            $soalTerpilih = $this->soalUntukLulusan($data['soal_pilihan'], $lulus);

            if ($soalTerpilih->isEmpty()) {
                throw new \DomainException('Tidak ada pertanyaan yang sesuai dengan fakultas lulusan yang dipilih.');
            }

            foreach ($this->urutkanSoal($soalTerpilih, $data['kategori_urutan'] ?? []) as $index => $soal) {
                DB::table('survey_soal')->insert([
                    'survey_id'  => $survey->id,
                    'soal_id'    => $soal->id,
                    'urutan'     => $index + 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            return $survey;
        });
    }

    public function submitJawaban(Survey $survey, array $data)
    {
        return DB::transaction(function () use ($survey, $data) {
            // Mengunci baris survei agar dua kiriman yang tiba bersamaan tidak
            // dapat menghasilkan respons dan arsip ganda.
            $survey = Survey::query()->lockForUpdate()->findOrFail($survey->id);

            if ($survey->is_completed) {
                throw new \DomainException('Survei ini sudah selesai diisi.');
            }

            if (! $survey->is_active) {
                throw new \DomainException('Survei ini tidak aktif.');
            }

            if ($survey->pengguna_lulusan_id) {
                $pengguna = PenggunaLulusan::find($survey->pengguna_lulusan_id);
                if ($pengguna) {
                    $pengguna->update([
                        'nama_penyelia'         => $data['nama_pengisi'],
                        'jabatan_penyelia'      => $data['jabatan_pengisi'] ?? $pengguna->jabatan_penyelia,
                        'kontak_penyelia'       => $data['hp_pengisi'] ?? $pengguna->kontak_penyelia,
                        'email_penyelia'        => $data['email_pengisi'] ?? $pengguna->email_penyelia,
                        'nama_perusahaan'       => $data['nama_perusahaan'] ?? $pengguna->nama_perusahaan,
                        'nomor_badan_hukum'     => $data['nomor_badan_hukum'] ?? $pengguna->nomor_badan_hukum,
                        'jenis_perusahaan'      => $data['jenis_perusahaan'] ?? $pengguna->jenis_perusahaan,
                        'alamat_perusahaan'     => $data['alamat_perusahaan'] ?? $pengguna->alamat_perusahaan,
                        'kontak_perusahaan'     => $data['kontak_perusahaan'] ?? $pengguna->kontak_perusahaan,
                        'cabang_kota'           => $data['cabang_kota'] ?? $pengguna->cabang_kota,
                        'cabang_negara'         => $data['cabang_negara'] ?? $pengguna->cabang_negara,
                        'jumlah_lulusan'        => $data['jumlah_lulusan_bekerja'] ?? $pengguna->jumlah_lulusan,
                    ]);
                }
            }

            $isFirstRecord = true;

            // Pre-load semua soal dan jawaban yang relevan untuk efisiensi query
            // Ambil key soal secara terpisah agar ID numerik tidak diindeks ulang oleh array_merge.
            $soalIds = array_values(array_unique(array_merge(
                array_keys($data['jawaban'] ?? []),
                array_keys($data['mc'] ?? []),
                array_keys($data['mc_custom'] ?? []),
            )));
            // Hanya pertanyaan yang memang terpasang pada survei ini boleh
            // menghasilkan respons, termasuk untuk isian bebas.
            $soalCache = $survey->soals()
                ->whereIn('soal.id', $soalIds)
                ->get()
                ->keyBy('id');
            $jawabanCache = Jawaban::whereIn('soal_id', $soalCache->keys())->get()->keyBy('id');

            // Rating & Essay
            foreach ($data['jawaban'] ?? [] as $soal_id => $isi_jawaban) {
                $soalModel = $soalCache->get($soal_id);
                if (!$soalModel) continue;

                $respon = new ResponJawaban();
                $respon->survey_id          = $survey->id;
                $respon->soal_id            = $soal_id;
                $respon->soal_text_snapshot = $soalModel->soal;
                $respon->responden          = $data['nama_pengisi'];

                if ($isFirstRecord) {
                    $respon->jumlah_lulusan_bekerja = $data['jumlah_lulusan_bekerja'] ?? null;
                    $isFirstRecord = false;
                }

                if ($soalModel->jenis_soal === 'essay') {
                    $respon->jawaban_text          = $isi_jawaban;
                    $respon->jawaban_id            = null;
                    $respon->jawaban_text_snapshot = null;
                } else {
                    $jawabanModel                  = $jawabanCache->get($isi_jawaban);
                    $respon->jawaban_id            = $isi_jawaban;
                    $respon->jawaban_text_snapshot = $jawabanModel?->jawaban;
                    $respon->jawaban_text          = null;
                }

                $respon->save();
            }

            // Multiple Choice: simpan satu baris per jawaban yang dicentang
            foreach ($data['mc'] ?? [] as $soal_id => $jawaban_ids) {
                $soalModel = $soalCache->get($soal_id);

                foreach (array_filter((array) $jawaban_ids, fn ($id) => $id !== null && $id !== '') as $jawaban_id) {
                    $jawabanModel = $jawabanCache->get($jawaban_id);

                    if (! $soalModel || ! $jawabanModel || (int) $jawabanModel->soal_id !== (int) $soal_id) {
                        continue;
                    }

                    $respon = new ResponJawaban();
                    $respon->survey_id             = $survey->id;
                    $respon->soal_id               = $soal_id;
                    $respon->soal_text_snapshot    = $soalModel?->soal;
                    $respon->responden             = $data['nama_pengisi'];
                    $respon->jawaban_id            = $jawaban_id;
                    $respon->jawaban_text_snapshot = $jawabanModel?->jawaban;

                    if ($isFirstRecord) {
                        $respon->jumlah_lulusan_bekerja = $data['jumlah_lulusan_bekerja'] ?? null;
                        $isFirstRecord = false;
                    }

                    $respon->save();
                }
            }

            // Multiple Choice: simpan teks "Lainnya" jika diisi
            foreach ($data['mc_custom'] ?? [] as $soal_id => $custom_text) {
                if (empty(trim($custom_text ?? ''))) continue;

                $soalModel = $soalCache->get($soal_id);
                if (! $soalModel || $soalModel->jenis_soal !== 'multiple_choice' || ! $soalModel->allows_custom_answer) {
                    continue;
                }

                $respon = new ResponJawaban();
                $respon->survey_id          = $survey->id;
                $respon->soal_id            = $soal_id;
                $respon->soal_text_snapshot = $soalModel?->soal;
                $respon->responden          = $data['nama_pengisi'];
                $respon->jawaban_id         = null;
                $respon->jawaban_text       = trim($custom_text);

                if ($isFirstRecord) {
                    $respon->jumlah_lulusan_bekerja = $data['jumlah_lulusan_bekerja'] ?? null;
                    $isFirstRecord = false;
                }

                $respon->save();
            }

            $survey->update(['is_completed' => true]);

            // Tulis arsip permanen — tidak bergantung FK apapun
            $this->buatArsip($survey->fresh(['lulusan.programStudi', 'lulusan.fakultasMaster', 'penggunaLulusan', 'periode']), $data);

            return $survey;
        });
    }

    private function buatArsip(Survey $survey, array $data): void
    {
        $lulus    = $survey->lulusan;
        $pengguna = $survey->penggunaLulusan;

        // Kumpulkan semua soal yang ada di survey ini beserta relasi jawaban & kategori
        $soals = $survey->soals()->with(['jawaban', 'kategori'])->get()->keyBy('id');

        // Bangun array jawaban terurut berdasarkan kode soal
        $jawabanArr = [];

        // Rating & Essay (dari $data['jawaban'])
        foreach ($data['jawaban'] ?? [] as $soal_id => $isi) {
            $s = $soals->get($soal_id);
            if (!$s) continue;

            $entry = [
                'kode'     => $s->kode,
                'kategori' => $s->kategori?->nama_kategori,
                'soal'     => $s->soal,
                'jenis'    => $s->jenis_soal,
                'nilai'    => null,
            ];

            if ($s->jenis_soal === 'essay') {
                $entry['jawaban'] = $isi;
            } else {
                $pil = $s->jawaban->firstWhere('id', $isi);
                $entry['jawaban'] = $pil?->jawaban;
                $entry['nilai']   = $pil?->nilai;
            }

            $jawabanArr[$s->kode] = $entry;
        }

        // Multiple Choice
        foreach ($data['mc'] ?? [] as $soal_id => $jawaban_ids) {
            $s = $soals->get($soal_id);
            if (!$s) continue;

            $pilihan = $s->jawaban->whereIn('id', (array) $jawaban_ids)->pluck('jawaban')->toArray();
            $jawabanArr[$s->kode] = [
                'kode'     => $s->kode,
                'kategori' => $s->kategori?->nama_kategori,
                'soal'     => $s->soal,
                'jenis'    => $s->jenis_soal,
                'jawaban'  => $pilihan,
                'nilai'    => null,
            ];
        }

        // Teks "Lainnya" pada multiple choice
        foreach ($data['mc_custom'] ?? [] as $soal_id => $custom_text) {
            if (empty(trim($custom_text ?? ''))) continue;

            $s = $soals->get($soal_id);
            if (! $s || ! $s->allows_custom_answer) continue;

            if (! isset($jawabanArr[$s->kode])) {
                $jawabanArr[$s->kode] = [
                    'kode'     => $s->kode,
                    'kategori' => $s->kategori?->nama_kategori,
                    'soal'     => $s->soal,
                    'jenis'    => $s->jenis_soal,
                    'jawaban'  => [],
                    'nilai'    => null,
                ];
            }

            $jawabanArr[$s->kode]['jawaban'][] = trim($custom_text);
        }

        // Urutkan berdasarkan kode soal (B1, B2, C1, ...)
        ksort($jawabanArr);

        SurveyArsip::create([
            'survey_id'     => $survey->id,
            'pengguna_lulusan_id' => $survey->pengguna_lulusan_id,
            'access_code'   => $survey->access_code,
            'judul'         => $survey->judul,
            'periode_kode'  => $survey->periode?->kode_periode,
            'periode_nama'  => $survey->periode?->nama_periode,
            'periode_tanggal_mulai' => $survey->periode?->tanggal_mulai,
            'periode_tanggal_berakhir' => $survey->periode?->tanggal_berakhir,
            'submitted_at'  => now(),
            'tahun_instrumen' => $survey->soals->first()?->instrumen_id
                ? \App\Models\Instrumen::find($survey->soals->first()->instrumen_id)?->tahun
                : null,

            'lulusan_nama'          => $lulus?->nama,
            'lulusan_nim'           => $lulus?->nim,
            'lulusan_program_studi' => $lulus?->programStudi?->nama,
            'lulusan_fakultas'      => $lulus?->fakultasMaster?->kode,
            'lulusan_tahun_lulus'   => $lulus?->tahun_lulus
                ? \Carbon\Carbon::parse($lulus->tahun_lulus)->format('Y')
                : null,

            'perusahaan_nama'              => $pengguna?->nama_perusahaan,
            'perusahaan_jenis'             => $pengguna?->jenis_perusahaan,
            'perusahaan_alamat'            => $pengguna?->alamat_perusahaan,
            'perusahaan_kontak'            => $pengguna?->kontak_perusahaan,
            'perusahaan_nomor_badan_hukum' => $pengguna?->nomor_badan_hukum,
            'perusahaan_cabang_kota'       => $pengguna?->cabang_kota,
            'perusahaan_cabang_negara'     => $pengguna?->cabang_negara,

            'penyelia_nama'           => $data['nama_pengisi'],
            'penyelia_jabatan'        => $data['jabatan_pengisi'] ?? $pengguna?->jabatan_penyelia,
            'penyelia_email'          => $data['email_pengisi'] ?? $pengguna?->email_penyelia,
            'penyelia_kontak'         => $data['hp_pengisi'] ?? $pengguna?->kontak_penyelia,
            'jumlah_lulusan_bekerja'  => (string) ($data['jumlah_lulusan_bekerja'] ?? null),

            'jawaban_json' => array_values($jawabanArr),
        ]);
    }

    public function createBulkSurveys(array $data): array
    {
        return DB::transaction(function () use ($data) {
            $periode = Periode::findOrFail($data['periode_id']);
            $this->pastikanPeriodeBerlangsung($periode);
            $tahunLulus = $data['tahun_lulus'];

            $lulusanList = Lulusan::whereYear('tahun_lulus', $tahunLulus)
                ->whereNotNull('pengguna_lulusan_id')
                ->get();

            if ($lulusanList->isEmpty()) {
                throw new \Exception("Tidak ada lulusan dengan tahun lulus {$tahunLulus} yang memiliki data perusahaan.");
            }

            $surveys = [];

            $soalTerpilih = Soal::with('kategori:id,fakultas_id')
                ->whereIn('id', $data['soal_pilihan'])
                ->get(['id', 'kategori_id', 'kode']);

            foreach ($lulusanList as $lulus) {
                $soalUntukLulusan = $soalTerpilih->filter(function (Soal $soal) use ($lulus) {
                    return ! $soal->kategori?->fakultas_id
                        || (int) $soal->kategori->fakultas_id === (int) $lulus->fakultas_id;
                });

                $survey = Survey::create([
                    'judul'               => $data['judul'],
                    'tahun'               => $periode->tanggal_mulai->year,
                    'periode_id'          => $periode->id,
                    'deskripsi'           => $data['deskripsi'] ?? null,
                    'lulusan_id'          => $lulus->id,
                    'pengguna_lulusan_id' => $lulus->pengguna_lulusan_id,
                    'access_code'         => strtoupper(Str::random(8)),
                    'is_completed'        => false,
                    'is_active'           => true,
                ]);

                foreach ($this->urutkanSoal($soalUntukLulusan, $data['kategori_urutan'] ?? []) as $index => $s) {
                    DB::table('survey_soal')->insert([
                        'survey_id'  => $survey->id,
                        'soal_id'    => $s->id,
                        'urutan'     => $index + 1,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }

                $surveys[] = $survey;
            }

            return [
                'surveys' => $surveys,
            ];
        });
    }

    public function updateSurvey(Survey $survey, array $data)
    {
        return DB::transaction(function () use ($survey, $data) {
            $periode = Periode::findOrFail($data['periode_id']);
            $this->pastikanPeriodeBerlangsung($periode);
            $lulus = Lulusan::findOrFail($data['lulusan_id']);

            $pengguna = PenggunaLulusan::find($data['pengguna_lulusan_id']);
            if ($pengguna) {
                $pengguna->update([
                    'nama_penyelia'     => $data['nama'] ?? $pengguna->nama_penyelia,
                    'kontak_penyelia'   => $data['hp'] ?? $pengguna->kontak_penyelia,
                    'email_penyelia'    => $data['email'] ?? $pengguna->email_penyelia,
                    'nomor_badan_hukum' => $data['badan_hukum'] ?? $pengguna->nomor_badan_hukum,
                    'kontak_perusahaan' => $data['telp_perusahaan'] ?? $pengguna->kontak_perusahaan,
                    'alamat_perusahaan' => $data['alamat_perusahaan'] ?? $pengguna->alamat_perusahaan,
                ]);
            }

            $survey->update([
                'judul'               => $data['judul'],
                'tahun'               => $periode->tanggal_mulai->year,
                'periode_id'          => $periode->id,
                'deskripsi'           => $data['deskripsi'] ?? null,
                'lulusan_id'          => $lulus->id,
                'pengguna_lulusan_id' => $data['pengguna_lulusan_id'],
            ]);

            $soalValid = $this->soalUntukLulusan($data['soal_pilihan'], $lulus);

            if ($soalValid->isEmpty()) {
                throw new \DomainException('Tidak ada pertanyaan yang sesuai dengan fakultas lulusan yang dipilih.');
            }

            $pivotData = $this->urutkanSoal($soalValid, $data['kategori_urutan'] ?? [])
                ->values()
                ->mapWithKeys(fn ($soal, $index) => [$soal->id => ['urutan' => $index + 1]])
                ->all();

            $survey->soals()->sync($pivotData);

            return $survey;
        });
    }

    /**
     * Menempatkan seluruh soal dari kategori yang sama secara berurutan.
     * Prioritas kategori mengikuti susunan kartu pada form admin; soal di dalam
     * kategori tetap mengikuti kode soal agar urutannya konsisten.
     */
    private function urutkanSoal($soals, array $kategoriUrutan)
    {
        $prioritasKategori = array_flip(array_map('intval', $kategoriUrutan));

        return $soals->sort(function ($a, $b) use ($prioritasKategori) {
            $urutanA = $prioritasKategori[$a->kategori_id] ?? PHP_INT_MAX;
            $urutanB = $prioritasKategori[$b->kategori_id] ?? PHP_INT_MAX;

            return [$urutanA, $a->kode ?? '', $a->id] <=> [$urutanB, $b->kode ?? '', $b->id];
        })->values();
    }

    private function pastikanPeriodeBerlangsung(Periode $periode): void
    {
        if (! $periode->isBerlangsung()) {
            throw new \DomainException('Survei hanya dapat dibuat atau diubah pada periode yang sedang berlangsung.');
        }
    }

    /** Ambil hanya pertanyaan dari kategori umum atau fakultas lulusan terkait. */
    private function soalUntukLulusan(array $soalIds, Lulusan $lulusan)
    {
        return Soal::query()
            ->whereIn('id', $soalIds)
            ->whereHas('kategori', function ($query) use ($lulusan) {
                $query->whereNull('fakultas_id')
                    ->when($lulusan->fakultas_id, fn ($query, $fakultasId) => $query->orWhere('fakultas_id', $fakultasId));
            })
            ->get(['id', 'kategori_id', 'kode']);
    }

}
