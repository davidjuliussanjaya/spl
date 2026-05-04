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
        // Konversi checkbox ke boolean
        $data['cabang_kota'] = $request->has('cabang_kota');
        $data['cabang_negara'] = $request->has('cabang_negara');

        return penggunalulusan::create($data);
    }

    /**
     * Memperbarui data pengguna lulusan.
     */
    public function updatePengguna(int $id, array $data, \Illuminate\Http\Request $request)
    {
        $pengguna = penggunalulusan::findOrFail($id);
        
        $data['cabang_kota'] = $request->has('cabang_kota');
        $data['cabang_negara'] = $request->has('cabang_negara');

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
