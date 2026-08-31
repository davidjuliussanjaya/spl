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
        Schema::table('respon_jawaban', function (Blueprint $table) {
            // Simpan teks soal dan jawaban saat survey diisi agar data tidak berubah
            // meski soal/jawaban diedit atau dihapus di masa mendatang.
            $table->text('soal_text_snapshot')->nullable();
            $table->text('jawaban_text_snapshot')->nullable();

            // Ganti cascadeOnDelete pada soal_id dan jawaban_id:
            // - soal_id: restrict, record respon tidak bisa dihapus jika soal masih dipakai.
            // - jawaban_id: nullOnDelete, opsi jawaban boleh dihapus tetapi snapshot tetap ada.
            $table->dropForeign(['soal_id']);
            $table->dropForeign(['jawaban_id']);

            $table->foreign('soal_id')
                  ->references('id')->on('soal');

            $table->foreign('jawaban_id')
                  ->references('id')->on('jawaban')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('respon_jawaban', function (Blueprint $table) {
            $table->dropColumn(['soal_text_snapshot', 'jawaban_text_snapshot']);

            $table->dropForeign(['soal_id']);
            $table->dropForeign(['jawaban_id']);

            $table->foreign('soal_id')
                  ->references('id')->on('soal')
                  ->cascadeOnDelete();

            $table->foreign('jawaban_id')
                  ->references('id')->on('jawaban')
                  ->cascadeOnDelete();
        });
    }
};
