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
        Schema::create('survey', function (Blueprint $table) {
            $table->id();

            $table->foreignId('lulusan_id')
                ->constrained('lulusan')
                ->cascadeOnDelete();

            $table->foreignId('pengguna_lulusan_id')
                ->constrained('pengguna_lulusan')
                ->cascadeOnDelete();

            $table->foreignId('jawaban_id')
                ->constrained('jawaban')
                ->cascadeOnDelete();

            $table->string('judul');
            $table->text('deskripsi')->nullable();
            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('survey');
    }
};
