<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lulusan', function (Blueprint $table) {
            $table->boolean('is_aggregate')->default(false);
        });

        DB::table('lulusan')
            ->whereRaw('LOWER(nama) LIKE ?', ['data agregat tanpa nama alumni (%'])
            ->update(['is_aggregate' => true]);

        foreach ($this->academicMappings() as $mapping) {
            $lulusanDenganProdi = DB::table('lulusan')
                ->whereRaw('LOWER(program_studi) LIKE ?', [$mapping['pattern']]);

            if (! $lulusanDenganProdi->exists()) {
                continue;
            }

            $fakultasId = $this->fakultasId($mapping['fakultas_kode'], $mapping['fakultas_nama']);
            $prodiId = $this->programStudiId($fakultasId, $mapping['prodi']);

            $lulusanDenganProdi
                ->update([
                    'fakultas' => $mapping['fakultas_kode'],
                    'fakultas_id' => $fakultasId,
                    'program_studi' => $mapping['prodi'],
                    'program_studi_id' => $prodiId,
                ]);

            DB::table('survey_arsip')
                ->whereRaw('LOWER(lulusan_program_studi) LIKE ?', [$mapping['pattern']])
                ->update([
                    'lulusan_fakultas' => $mapping['fakultas_kode'],
                    'lulusan_program_studi' => $mapping['prodi'],
                ]);
        }
    }

    public function down(): void
    {
        Schema::table('lulusan', function (Blueprint $table) {
            $table->dropColumn('is_aggregate');
        });
    }

    private function fakultasId(string $kode, string $nama): int
    {
        DB::table('fakultas')->updateOrInsert(['kode' => $kode], ['nama' => $nama]);

        return (int) DB::table('fakultas')->where('kode', $kode)->value('id');
    }

    private function programStudiId(int $fakultasId, string $nama): int
    {
        DB::table('program_studi')->updateOrInsert(
            ['fakultas_id' => $fakultasId, 'nama' => $nama],
            ['kode' => 'PS-' . strtoupper(substr(md5($fakultasId . '|' . $nama), 0, 12))],
        );

        return (int) DB::table('program_studi')
            ->where('fakultas_id', $fakultasId)
            ->where('nama', $nama)
            ->value('id');
    }

    private function academicMappings(): array
    {
        return [
            ['pattern' => '%sistem informasi%', 'fakultas_kode' => 'FTI', 'fakultas_nama' => 'Fakultas Teknologi dan Informatika', 'prodi' => 'Sistem Informasi'],
            ['pattern' => '%teknik komputer%', 'fakultas_kode' => 'FTI', 'fakultas_nama' => 'Fakultas Teknologi dan Informatika', 'prodi' => 'Teknik Komputer'],
            ['pattern' => '%akuntansi%', 'fakultas_kode' => 'FEB', 'fakultas_nama' => 'Fakultas Ekonomi dan Bisnis', 'prodi' => 'Akuntansi'],
            ['pattern' => '%manajemen informatika%', 'fakultas_kode' => 'FTI', 'fakultas_nama' => 'Fakultas Teknologi dan Informatika', 'prodi' => 'Manajemen Informatika'],
            ['pattern' => '%manajemen%', 'fakultas_kode' => 'FEB', 'fakultas_nama' => 'Fakultas Ekonomi dan Bisnis', 'prodi' => 'Manajemen'],
            ['pattern' => '%desain komunikasi visual%', 'fakultas_kode' => 'FDIK', 'fakultas_nama' => 'Fakultas Desain dan Industri Kreatif', 'prodi' => 'Desain Komunikasi Visual'],
            ['pattern' => '%desain produk%', 'fakultas_kode' => 'FDIK', 'fakultas_nama' => 'Fakultas Desain dan Industri Kreatif', 'prodi' => 'Desain Produk'],
            ['pattern' => '%produksi film%', 'fakultas_kode' => 'FDIK', 'fakultas_nama' => 'Fakultas Desain dan Industri Kreatif', 'prodi' => 'Produksi Film dan Televisi'],
            ['pattern' => '%administrasi perkantoran%', 'fakultas_kode' => 'FDIK', 'fakultas_nama' => 'Fakultas Desain dan Industri Kreatif', 'prodi' => 'Administrasi Perkantoran'],
        ];
    }
};
