<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $namaFakultas = [
            'FTI' => 'Fakultas Teknologi dan Informatika',
            'FDIK' => 'Fakultas Desain dan Industri Kreatif',
            'FEB' => 'Fakultas Ekonomi dan Bisnis',
        ];

        DB::table('lulusan')
            ->select('fakultas', 'program_studi')
            ->whereNotNull('fakultas')
            ->whereNotNull('program_studi')
            ->distinct()
            ->orderBy('fakultas')
            ->orderBy('program_studi')
            ->each(function ($lulusan) use ($namaFakultas) {
                $kodeFakultas = trim($lulusan->fakultas);
                $namaProdi = trim($lulusan->program_studi);

                if ($kodeFakultas === '' || $namaProdi === '') {
                    return;
                }

                DB::table('fakultas')->updateOrInsert(
                    ['kode' => $kodeFakultas],
                    [
                        'nama' => $namaFakultas[$kodeFakultas] ?? $kodeFakultas,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                );

                $fakultasId = DB::table('fakultas')->where('kode', $kodeFakultas)->value('id');
                $kodeProdi = 'PS-' . strtoupper(substr(md5($fakultasId . '|' . $namaProdi), 0, 12));

                DB::table('program_studi')->updateOrInsert(
                    ['fakultas_id' => $fakultasId, 'nama' => $namaProdi],
                    ['kode' => $kodeProdi, 'created_at' => now(), 'updated_at' => now()],
                );

                $programStudiId = DB::table('program_studi')
                    ->where('fakultas_id', $fakultasId)
                    ->where('nama', $namaProdi)
                    ->value('id');

                DB::table('lulusan')
                    ->where('fakultas', $lulusan->fakultas)
                    ->where('program_studi', $lulusan->program_studi)
                    ->update([
                        'fakultas_id' => $fakultasId,
                        'program_studi_id' => $programStudiId,
                    ]);
            });
    }

    public function down(): void
    {
        // Backfill data tidak dihapus saat rollback agar riwayat lulusan tetap utuh.
    }
};
