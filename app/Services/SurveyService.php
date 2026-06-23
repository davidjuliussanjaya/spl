<?php

namespace App\Services;

use App\Models\lulusan;
use App\Models\penggunalulusan;
use App\Models\ResponJawaban;
use App\Models\soal;
use App\Models\Survey;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SurveyService
{
    public function createSurvey(array $data)
    {
        return DB::transaction(function () use ($data) {
            $pengguna = penggunalulusan::findOrFail($data['pengguna_lulusan_id']);
            
            $pengguna->update([
                'nama_penyelia'     => $data['nama'] ?? $pengguna->nama_penyelia,
                'kontak_penyelia'   => $data['hp'] ?? $pengguna->kontak_penyelia,
                'email_penyelia'    => $data['email'] ?? $pengguna->email_penyelia,
                'nomor_badan_hukum' => $data['badan_hukum'] ?? $pengguna->nomor_badan_hukum,
                'kontak_perusahaan' => $data['telp_perusahaan'] ?? $pengguna->kontak_perusahaan,
                'alamat_perusahaan' => $data['alamat_perusahaan'] ?? $pengguna->alamat_perusahaan,
            ]);

            $lulus = lulusan::findOrFail($data['lulusan_id']);

            $survey = Survey::create([
                'judul'               => $data['judul'],
                'deskripsi'           => $data['deskripsi'] ?? null,
                'lulusan_id'          => $lulus->id,
                'pengguna_lulusan_id' => $data['pengguna_lulusan_id'],
                'access_code'         => strtoupper(Str::random(8)),
                'is_completed'        => false,
                'is_active'           => true,
            ]);

            // Hanya simpan soal yang sesuai dengan fakultas lulusan
            $soalTerpilih = soal::whereIn('id', $data['soal_pilihan'])
                ->where(function ($q) use ($lulus) {
                    $q->where('peruntukan_fakultas', 'Umum');
                    if ($lulus->fakultas) {
                        $q->orWhere('peruntukan_fakultas', $lulus->fakultas);
                    }
                })
                ->pluck('id');

            foreach ($soalTerpilih as $soal_id) {
                DB::table('survey_soal')->insert([
                    'survey_id'  => $survey->id,
                    'soal_id'    => $soal_id,
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
            if ($survey->pengguna_lulusan_id) {
                $pengguna = penggunalulusan::find($survey->pengguna_lulusan_id);
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

            // Rating & Essay
            foreach ($data['jawaban'] ?? [] as $soal_id => $isi_jawaban) {
                $soalModel = soal::find($soal_id);
                if (!$soalModel) continue;

                $respon = new ResponJawaban();
                $respon->survey_id = $survey->id;
                $respon->soal_id   = $soal_id;
                $respon->responden = $data['nama_pengisi'];

                if ($isFirstRecord) {
                    $respon->jumlah_lulusan_bekerja = $data['jumlah_lulusan_bekerja'] ?? null;
                    $isFirstRecord = false;
                }

                if ($soalModel->jenis_soal === 'essay') {
                    $respon->jawaban_text = $isi_jawaban;
                    $respon->jawaban_id   = null;
                } else {
                    $respon->jawaban_id   = $isi_jawaban;
                    $respon->jawaban_text = null;
                }

                $respon->save();
            }

            // Multiple Choice: simpan satu baris per jawaban yang dicentang
            foreach ($data['mc'] ?? [] as $soal_id => $jawaban_ids) {
                foreach ($jawaban_ids as $jawaban_id) {
                    $respon = new ResponJawaban();
                    $respon->survey_id  = $survey->id;
                    $respon->soal_id    = $soal_id;
                    $respon->responden  = $data['nama_pengisi'];
                    $respon->jawaban_id = $jawaban_id;

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

                $respon = new ResponJawaban();
                $respon->survey_id    = $survey->id;
                $respon->soal_id      = $soal_id;
                $respon->responden    = $data['nama_pengisi'];
                $respon->jawaban_id   = null;
                $respon->jawaban_text = trim($custom_text);

                if ($isFirstRecord) {
                    $respon->jumlah_lulusan_bekerja = $data['jumlah_lulusan_bekerja'] ?? null;
                    $isFirstRecord = false;
                }

                $respon->save();
            }

            $survey->update(['is_completed' => true]);

            return $survey;
        });
    }

    public function createBulkSurveys(array $data): array
    {
        return DB::transaction(function () use ($data) {
            $tahunLulus = $data['tahun_lulus'];

            $lulusanList = lulusan::whereRaw('EXTRACT(YEAR FROM tahun_lulus) = ?', [$tahunLulus])
                ->whereNotNull('pengguna_lulusan_id')
                ->get();

            if ($lulusanList->isEmpty()) {
                throw new \Exception("Tidak ada lulusan dengan tahun lulus {$tahunLulus} yang memiliki data perusahaan.");
            }

            $surveys = [];

            // Ambil detail soal yang dipilih admin (termasuk peruntukan_fakultas)
            $soalTerpilih = soal::whereIn('id', $data['soal_pilihan'])->get(['id', 'peruntukan_fakultas']);

            foreach ($lulusanList as $lulus) {
                // Filter soal: hanya yang Umum atau sesuai fakultas lulusan ini
                $soalUntukLulusan = $soalTerpilih->filter(function ($s) use ($lulus) {
                    return $s->peruntukan_fakultas === 'Umum'
                        || $s->peruntukan_fakultas === $lulus->fakultas;
                });

                $survey = Survey::create([
                    'judul'               => $data['judul'],
                    'deskripsi'           => $data['deskripsi'] ?? null,
                    'lulusan_id'          => $lulus->id,
                    'pengguna_lulusan_id' => $lulus->pengguna_lulusan_id,
                    'access_code'         => strtoupper(Str::random(8)),
                    'is_completed'        => false,
                    'is_active'           => true,
                ]);

                foreach ($soalUntukLulusan as $s) {
                    DB::table('survey_soal')->insert([
                        'survey_id'  => $survey->id,
                        'soal_id'    => $s->id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }

                $surveys[] = $survey;
            }

            return $surveys;
        });
    }

    public function updateSurvey(Survey $survey, array $data)
    {
        return DB::transaction(function () use ($survey, $data) {
            $pengguna = penggunalulusan::find($data['pengguna_lulusan_id']);
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

            $lulus = lulusan::findOrFail($data['lulusan_id']);

            $survey->update([
                'judul'               => $data['judul'],
                'deskripsi'           => $data['deskripsi'] ?? null,
                'lulusan_id'          => $lulus->id,
                'pengguna_lulusan_id' => $data['pengguna_lulusan_id'],
            ]);

            // Sync hanya soal yang sesuai dengan fakultas lulusan
            $soalValid = soal::whereIn('id', $data['soal_pilihan'])
                ->where(function ($q) use ($lulus) {
                    $q->where('peruntukan_fakultas', 'Umum');
                    if ($lulus->fakultas) {
                        $q->orWhere('peruntukan_fakultas', $lulus->fakultas);
                    }
                })
                ->pluck('id')
                ->toArray();

            $survey->soals()->sync($soalValid);

            return $survey;
        });
    }
}
