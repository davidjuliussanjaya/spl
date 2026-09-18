<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('periode', function (Blueprint $table) {
            $table->id();
            $table->string('kode_periode', 50)->unique();
            $table->string('nama_periode');
            $table->date('tanggal_mulai');
            $table->date('tanggal_berakhir');
            $table->timestamps();
        });

        Schema::table('survey', function (Blueprint $table) {
            // Nullable selama migrasi agar seluruh survei lama dapat dipetakan aman.
            $table->foreignId('periode_id')->nullable()->constrained('periode')->restrictOnDelete();
        });

        // Data lama sebelumnya hanya memiliki kolom tahun. Buat periode tahunan
        // dan hubungkan setiap survei agar riwayat tetap dapat dibuka.
        DB::table('survey')->orderBy('id')->chunkById(100, function ($surveys) {
            foreach ($surveys as $survey) {
                $year = is_numeric($survey->tahun ?? null)
                    ? (int) $survey->tahun
                    : Carbon::parse($survey->created_at ?? now())->year;
                $code = (string) $year;

                $periodeId = DB::table('periode')->where('kode_periode', $code)->value('id');

                if (! $periodeId) {
                    $now = now();
                    $periodeId = DB::table('periode')->insertGetId([
                        'kode_periode' => $code,
                        'nama_periode' => "Periode Survei {$year}",
                        'tanggal_mulai' => "{$year}-01-01",
                        'tanggal_berakhir' => "{$year}-12-31",
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
                }

                DB::table('survey')->where('id', $survey->id)->update(['periode_id' => $periodeId]);
            }
        });
    }

    public function down(): void
    {
        Schema::table('survey', function (Blueprint $table) {
            $table->dropForeign(['periode_id']);
            $table->dropColumn('periode_id');
        });

        Schema::dropIfExists('periode');
    }
};
