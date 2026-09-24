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
            // Existing multiple-choice questions used checkboxes, so retain that
            // behaviour for historic questions until an administrator changes it.
            $table->boolean('allows_multiple_answers')->default(true)->after('jenis_soal');
        });

        DB::table('soal')
            ->where('jenis_soal', 'rating')
            ->update(['allows_multiple_answers' => false]);
    }

    public function down(): void
    {
        Schema::table('soal', function (Blueprint $table) {
            $table->dropColumn('allows_multiple_answers');
        });
    }
};
