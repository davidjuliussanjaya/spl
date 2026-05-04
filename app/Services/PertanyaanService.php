<?php

namespace App\Services;

use App\Models\jawaban;
use App\Models\soal;
use Illuminate\Support\Facades\DB;

class PertanyaanService
{
    public function storePertanyaan(array $data)
{
    return DB::transaction(function () use ($data) {
        // 1. Konversi Jenis Soal (Sesuai logic Anda sebelumnya)
        $jenis = $this->mapJenisSoal($data['type']);

        // 2. Simpan Soal
        $soal = Soal::create([
            'soal'        => $data['question'],
            'kategori'    => $data['kategori'], // Simpan data kategori ke database
            'kode'        => $data['kode'] ?? null,
            'jenis_soal'  => $jenis,
            'is_required' => isset($data['required']),
            'is_active'   => true
        ]);

        // 3. Simpan Jawaban jika Multiple Choice / Radio
        if ($jenis === 'multiple_choice' && isset($data['jawaban'])) {
            $this->saveJawaban($soal->id, $data['jawaban'], $data['nilai'] ?? []);
        }

        return $soal;
    });
}

    /**
     * Memperbarui Soal dan Jawaban
     */
    public function updatePertanyaan($id, array $data)
    {
        return DB::transaction(function () use ($id, $data) {
            $soal = Soal::findOrFail($id);
            $jenis = $this->mapJenisSoal($data['type']);

            $soal->update([
                'soal'        => $data['question'],
                'kode'        => $data['kode'],
                'jenis_soal'  => $jenis,
                'is_required' => isset($data['required']),
            ]);

            // Hapus jawaban lama dan simpan yang baru
            $soal->jawaban()->delete();

            if ($jenis === 'multiple_choice' && isset($data['jawaban'])) {
                $this->saveJawaban($soal->id, $data['jawaban'], $data['nilai'] ?? []);
            }

            return $soal;
        });
    }

    /**
     * Toggle Status Aktif
     */
    public function toggleStatus($id)
    {
        $soal = soal::findOrFail($id);
        $soal->is_active = !$soal->is_active;
        $soal->save();

        return $soal;
    }

    // Helper: Pemetaan Jenis
    private function mapJenisSoal($type)
    {
        return match ($type) {
            'radio' => 'multiple_choice',
            'text'  => 'essay',
            default => 'rating',
        };
    }

    // Helper: Simpan Jawaban Iterasi
    private function saveJawaban($soalId, $jawabanList, $nilaiList)
    {
        foreach ($jawabanList as $index => $teks) {
            jawaban::create([
                'soal_id' => $soalId,
                'jawaban' => $teks,
                'nilai'   => $nilaiList[$index] ?? 0,
                'urutan'  => $index + 1
            ]);
        }
    }
}
