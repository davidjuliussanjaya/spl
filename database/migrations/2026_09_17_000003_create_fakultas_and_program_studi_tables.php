<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fakultas', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 20)->unique();
            $table->string('nama')->unique();
            $table->timestamps();
        });

        Schema::create('program_studi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fakultas_id')->constrained('fakultas')->restrictOnDelete();
            $table->string('kode', 30)->unique();
            $table->string('nama');
            $table->timestamps();
            $table->unique(['fakultas_id', 'nama']);
        });

        Schema::table('lulusan', function (Blueprint $table) {
            $table->foreignId('fakultas_id')->nullable()->constrained('fakultas')->restrictOnDelete();
            $table->foreignId('program_studi_id')->nullable()->constrained('program_studi')->restrictOnDelete();
        });

        Schema::table('soal', function (Blueprint $table) {
            // Null berarti pertanyaan umum untuk semua fakultas.
            $table->foreignId('fakultas_id')->nullable()->constrained('fakultas')->restrictOnDelete();
        });

        $namaFakultas = [
            'FTI' => 'Fakultas Teknologi dan Informatika',
            'FDIK' => 'Fakultas Desain dan Industri Kreatif',
            'FEB' => 'Fakultas Ekonomi dan Bisnis',
        ];

        // Bentuk master dan relasi dari data lama tanpa menghilangkan riwayat teksnya.
        DB::table('lulusan')->select('fakultas')->whereNotNull('fakultas')->distinct()->orderBy('fakultas')->each(function ($row) use ($namaFakultas) {
            $kode = trim($row->fakultas);
            if ($kode === '') {
                return;
            }

            DB::table('fakultas')->updateOrInsert(
                ['kode' => $kode],
                ['nama' => $namaFakultas[$kode] ?? $kode, 'updated_at' => now(), 'created_at' => now()],
            );
        });

        DB::table('lulusan')->orderBy('id')->chunkById(100, function ($lulusans) {
            foreach ($lulusans as $lulusan) {
                $fakultasId = DB::table('fakultas')->where('kode', $lulusan->fakultas)->value('id');
                if (! $fakultasId) {
                    continue;
                }

                $namaProdi = trim((string) $lulusan->program_studi);
                if ($namaProdi === '') {
                    DB::table('lulusan')->where('id', $lulusan->id)->update(['fakultas_id' => $fakultasId]);
                    continue;
                }

                $kodeProdi = 'PS-' . strtoupper(substr(md5($fakultasId . '|' . $namaProdi), 0, 12));
                DB::table('program_studi')->updateOrInsert(
                    ['fakultas_id' => $fakultasId, 'nama' => $namaProdi],
                    ['kode' => $kodeProdi, 'updated_at' => now(), 'created_at' => now()],
                );
                $programStudiId = DB::table('program_studi')
                    ->where('fakultas_id', $fakultasId)
                    ->where('nama', $namaProdi)
                    ->value('id');

                DB::table('lulusan')->where('id', $lulusan->id)->update([
                    'fakultas_id' => $fakultasId,
                    'program_studi_id' => $programStudiId,
                ]);
            }
        });

        DB::table('soal')->whereNotIn('peruntukan_fakultas', ['Umum'])->orderBy('id')->each(function ($soal) {
            $fakultasId = DB::table('fakultas')->where('kode', $soal->peruntukan_fakultas)->value('id');
            if ($fakultasId) {
                DB::table('soal')->where('id', $soal->id)->update(['fakultas_id' => $fakultasId]);
            }
        });
    }

    public function down(): void
    {
        Schema::table('soal', function (Blueprint $table) {
            $table->dropForeign(['fakultas_id']);
            $table->dropColumn('fakultas_id');
        });
        Schema::table('lulusan', function (Blueprint $table) {
            $table->dropForeign(['program_studi_id']);
            $table->dropForeign(['fakultas_id']);
            $table->dropColumn(['program_studi_id', 'fakultas_id']);
        });
        Schema::dropIfExists('program_studi');
        Schema::dropIfExists('fakultas');
    }
};
