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
}
