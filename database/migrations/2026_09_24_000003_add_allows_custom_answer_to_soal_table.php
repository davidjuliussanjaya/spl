<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('soal', function (Blueprint $table) {
            $table->boolean('allows_custom_answer')->default(false)->after('allows_multiple_answers');
        });

        // Terapkan konfigurasi instrumen 2026 juga pada database yang sudah ada.
        DB::table('soal')
            ->where('kode', 'K1')
            ->whereIn('instrumen_id', DB::table('instrumen')->where('tahun', 2026)->select('id'))
            ->update(['allows_multiple_answers' => false]);

        DB::table('soal')
            ->where('kode', 'M1')
            ->whereIn('instrumen_id', DB::table('instrumen')->where('tahun', 2026)->select('id'))
            ->update(['allows_multiple_answers' => true, 'allows_custom_answer' => true]);
    }

    public function down(): void
    {
        Schema::table('soal', function (Blueprint $table) {
            $table->dropColumn('allows_custom_answer');
        });
    }
};
