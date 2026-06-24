<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('soal', function (Blueprint $table) {
            // Kolom mungkin sudah ada jika migration sebelumnya gagal sebagian
            if (!Schema::hasColumn('soal', 'instrumen_id')) {
                $table->foreignId('instrumen_id')
                      ->nullable()
                      ->after('id')
                      ->constrained('instrumen');
            } else {
                // Kolom sudah ada, cukup tambahkan FK constraint
                $table->foreign('instrumen_id')
                      ->references('id')->on('instrumen');
            }
        });
    }

    public function down(): void
    {
        Schema::table('soal', function (Blueprint $table) {
            $table->dropForeign(['instrumen_id']);
            $table->dropColumn('instrumen_id');
        });
    }
};
