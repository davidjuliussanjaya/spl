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

            $table->foreignId('soal_id')->constrained('soal')->cascadeOnDelete();

            // untuk multiple choice
            $table->foreignId('jawaban_id')->nullable()->constrained('jawaban')->cascadeOnDelete();

            // untuk essay
            $table->text('jawaban_text')->nullable();

            // OPTIONAL (kalau ada user login)
            // $table->foreignId('user_id')->nullable()->constrained()->cascadeOnDelete();

            // OPTIONAL (kalau tanpa login)
            $table->string('responden')->nullable();

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
