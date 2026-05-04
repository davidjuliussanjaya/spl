<?php

namespace App\Services;

use App\Models\lulusan;
use Illuminate\Support\Facades\DB;

class LulusanService
{
    /**
     * Menyimpan data lulusan baru.
     */
    public function storeLulusan(array $data): lulusan
    {
        return DB::transaction(function () use ($data) {
            // Logika tambahan: pastikan status menjadi boolean false jika tidak dicentang
            $data['status'] = isset($data['status']) ? true : false;

            return Lulusan::create($data);
        });
    }

    /**
     * Mendapatkan data lulusan berdasarkan filter.
     */
    public function getFilteredLulusan(\Illuminate\Http\Request $request)
    {
        $query = lulusan::query();

        // Filter Nama
        if ($request->has('nama') && $request->nama != '') {
            $query->where('nama', 'like', '%' . $request->nama . '%');
        }

        // Filter NIM
        if ($request->has('nim') && $request->nim != '') {
            $query->where('nim', 'like', '%' . $request->nim . '%');
        }

        // Filter Prodi
        if ($request->has('prodi') && $request->prodi != 'Select') {
            $query->where('program_studi', $request->prodi);
        }

        // Filter Fakultas
        if ($request->has('fakultas') && $request->fakultas != 'Select') {
            $query->where('fakultas', $request->fakultas);
        }

        // Filter Tahun Lulus (Range)
        if ($request->filled('dari') && $request->filled('sampai')) {
            $query->whereBetween('tahun_lulus', [$request->dari . '-01-01', $request->sampai . '-12-31']);
        }

        // Filter Status (Boolean di DB, tapi di UI Bekerja/Belum)
        if ($request->has('status_kerja') && $request->status_kerja != 'Select') {
            $statusValue = ($request->status_kerja == 'Bekerja') ? 1 : 0;
            $query->where('status', $statusValue);
        }

        return $query->latest()->get();
    }
}
