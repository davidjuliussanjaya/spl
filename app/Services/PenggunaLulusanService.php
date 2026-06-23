<?php

namespace App\Services;

use App\Models\penggunalulusan;

class PenggunaLulusanService
{
    /**
     * Menyimpan data pengguna lulusan baru.
     */
    public function storePengguna(array $data, \Illuminate\Http\Request $request)
    {
        $data['cabang_kota'] = (int) ($data['cabang_kota'] ?? 0);
        $data['cabang_negara'] = (int) ($data['cabang_negara'] ?? 0);

        return penggunalulusan::create($data);
    }

    /**
     * Memperbarui data pengguna lulusan.
     */
    public function updatePengguna(int $id, array $data, \Illuminate\Http\Request $request)
    {
        $pengguna = penggunalulusan::findOrFail($id);
        
        $data['cabang_kota'] = (int) ($data['cabang_kota'] ?? 0);
        $data['cabang_negara'] = (int) ($data['cabang_negara'] ?? 0);

        $pengguna->update($data);
        
        return $pengguna;
    }

    /**
     * Menghapus data pengguna lulusan.
     */
    public function deletePengguna(int $id)
    {
        $pengguna = penggunalulusan::findOrFail($id);
        $pengguna->delete();
        
        return true;
    }
}
