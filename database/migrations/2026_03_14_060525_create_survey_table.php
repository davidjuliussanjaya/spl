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
    
            // Kode unik untuk login perusahaan
            $table->string('access_code', 10)->unique();
            
            // Siapa yang disurvei (Lulusan) & Siapa yang mengisi (Perusahaan)
            $table->foreignId('lulusan_id')->constrained('lulusan')->cascadeOnDelete();
            $table->foreignId('pengguna_lulusan_id')->constrained('pengguna_lulusan')->cascadeOnDelete();

            // Informasi Sesi
            $table->string('judul'); 
            $table->text('deskripsi')->nullable();
            $table->boolean('is_completed')->default(false); 
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
