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
        Schema::create('respon_jawaban', function (Blueprint $table) {
            $table->id();
            // Menghubungkan ke sesi survey yang sedang diisi
            $table->foreignId('survey_id')->constrained('survey')->cascadeOnDelete();
            
            $table->foreignId('soal_id')->constrained('soal')->cascadeOnDelete();
            $table->foreignId('jawaban_id')->nullable()->constrained('jawaban')->cascadeOnDelete();
            $table->text('jawaban_text')->nullable();
            $table->string('responden')->nullable(); 
            $table->integer('jumlah_lulusan_bekerja')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('respon_jawaban');
    }
};
