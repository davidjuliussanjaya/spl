<?php

namespace App\Services;

use App\Models\PenggunaLulusan;

class PenggunaLulusanService
{
    /**
     * Menyimpan data pengguna lulusan baru.
     */
    public function storePengguna(array $data, \Illuminate\Http\Request $request)
    {
        // Nilai cabang dilaporkan oleh responden saat survei, bukan oleh admin.
        unset($data['cabang_kota'], $data['cabang_negara'], $data['durasi_lulusan_bekerja']);
        $data['cabang_kota'] = 0;
        $data['cabang_negara'] = 0;

        return PenggunaLulusan::create($data);
    }

    /**
     * Memperbarui data pengguna lulusan.
     */
    public function updatePengguna(int $id, array $data, \Illuminate\Http\Request $request)
    {
        $pengguna = PenggunaLulusan::findOrFail($id);

        // Jangan menimpa data cabang yang sudah diisi responden melalui survei.
        unset($data['cabang_kota'], $data['cabang_negara'], $data['durasi_lulusan_bekerja']);

        $pengguna->update($data);
        
        return $pengguna;
    }

    /**
     * Menghapus data pengguna lulusan.
     */
    public function deletePengguna(int $id)
    {
        $pengguna = PenggunaLulusan::findOrFail($id);
        $pengguna->delete();
        
        return true;
    }
}
