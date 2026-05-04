<?php

namespace App\Services;

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

            $survey = Survey::create([
                'judul'               => $data['judul'],
                'deskripsi'           => $data['deskripsi'] ?? null,
                'lulusan_id'          => $data['lulusan_id'],
                'pengguna_lulusan_id' => $data['pengguna_lulusan_id'],
                'access_code'         => strtoupper(Str::random(8)),
                'is_completed'        => false,
                'is_active'           => true,
            ]);

            foreach ($data['soal_pilihan'] as $soal_id) {
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
                        'nama_penyelia'   => $data['nama_pengisi'],
                        'kontak_penyelia' => $data['hp_pengisi'] ?? $pengguna->kontak_penyelia,
                        'email_penyelia'  => $data['email_pengisi'] ?? $pengguna->email_penyelia,
                    ]);
                }
            }

            foreach ($data['jawaban'] as $soal_id => $isi_jawaban) {
                $soal = soal::find($soal_id);
                if (!$soal) continue;

                $respon = new ResponJawaban();
                $respon->survey_id = $survey->id;
                $respon->soal_id   = $soal_id;
                $respon->responden = $data['nama_pengisi'];

                if ($soal->jenis_soal == 'essay') {
                    $respon->jawaban_text = $isi_jawaban;
                    $respon->jawaban_id   = null;
                } else {
                    $respon->jawaban_id   = $isi_jawaban; 
                    $respon->jawaban_text = null;
                }

                $respon->save();
            }

            $survey->update(['is_completed' => true]);

            return $survey;
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

            $survey->update([
                'judul' => $data['judul'],
                'deskripsi' => $data['deskripsi'] ?? null,
                'lulusan_id' => $data['lulusan_id'],
                'pengguna_lulusan_id' => $data['pengguna_lulusan_id'],
            ]);

            $survey->soals()->sync($data['soal_pilihan']);

            return $survey;
        });
    }
}
