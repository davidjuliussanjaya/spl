<?php

namespace App\Services;

use App\Models\Lulusan;
use App\Models\ProgramStudi;
use Illuminate\Support\Facades\DB;

class LulusanService
{
    /**
     * Menyimpan data lulusan baru.
     */
    public function storeLulusan(array $data): Lulusan
    {
        return DB::transaction(function () use ($data) {
            $programStudi = ProgramStudi::findOrFail($data['program_studi_id']);
            if ((int) $programStudi->fakultas_id !== (int) $data['fakultas_id']) {
                throw new \InvalidArgumentException('Program studi tidak terdaftar pada fakultas yang dipilih.');
            }

            // Kolom teks lama dipertahankan sebagai snapshot kompatibilitas.
            $data['program_studi'] = $programStudi->nama;
            $data['fakultas'] = $programStudi->fakultas->kode;

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
        $query = Lulusan::with(['fakultasMaster', 'programStudi']);

        // Filter Nama
        if ($request->has('nama') && $request->nama != '') {
            $query->where('nama', 'like', '%' . $request->nama . '%');
        }

        // Filter NIM
        if ($request->has('nim') && $request->nim != '') {
            $query->where('nim', 'like', '%' . $request->nim . '%');
        }

        // Filter Prodi
        if ($request->filled('program_studi_id')) {
            $query->where('program_studi_id', $request->integer('program_studi_id'));
        }

        // Filter Fakultas
        if ($request->filled('fakultas_id')) {
            $query->where('fakultas_id', $request->integer('fakultas_id'));
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

        return $query->latest('created_at')->paginate(10)->withQueryString();
    }
}
