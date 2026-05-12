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
        Schema::create('lulusan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengguna_lulusan_id')->constrained('pengguna_lulusan')->cascadeOnDelete();
            $table->string('nama');
            $table->string('nim');
            $table->string('program_studi');
            $table->string('fakultas')->nullable();
            $table->date('tahun_lulus');
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lulusan');
    }
};
