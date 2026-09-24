<?php

namespace App\Services;

use App\Models\Lulusan;
use App\Models\ProgramStudi;
use Illuminate\Support\Arr;
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

            return Lulusan::create($data);
        });
    }

    /**
     * Memperbarui data akademik lulusan tanpa mengubah perusahaan penilainya.
     */
    public function updateMahasiswa(Lulusan $lulusan, array $data): Lulusan
    {
        return DB::transaction(function () use ($lulusan, $data) {
            $programStudi = ProgramStudi::findOrFail($data['program_studi_id']);
            if ((int) $programStudi->fakultas_id !== (int) $data['fakultas_id']) {
                throw new \InvalidArgumentException('Program studi tidak terdaftar pada fakultas yang dipilih.');
            }

            $data['program_studi'] = $programStudi->nama;
            $data['fakultas'] = $programStudi->fakultas->kode;
            $lulusan->update($data);

            return $lulusan->fresh();
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

        $periode = collect(Arr::wrap($request->input('periode', [])))
            ->filter(fn ($value) => filled($value))
            ->values()
            ->all();
        if (! empty($periode)) {
            $query->whereHas('surveys.periode', fn ($survey) => $survey->whereIn('kode_periode', $periode));
        }

        $programStudi = collect(Arr::wrap($request->input('program_studi', [])))
            ->filter(fn ($value) => filled($value))
            ->values()
            ->all();
        if (! empty($programStudi)) {
            $query->whereIn('program_studi', $programStudi);
        }

        return $query->latest('created_at')->paginate(10)->withQueryString();
    }
}
