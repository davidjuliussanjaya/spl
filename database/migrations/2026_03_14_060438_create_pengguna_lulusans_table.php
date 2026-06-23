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
        Schema::create('pengguna_lulusan', function (Blueprint $table) {
            $table->id();
            $table->string('nama_perusahaan');
            $table->string('nama_penyelia');
            $table->string('jabatan_penyelia')->nullable();
            $table->string('kontak_penyelia')->nullable();
            $table->string('email_penyelia')->unique();
            $table->integer('jumlah_lulusan')->nullable();
            $table->integer('durasi_lulusan_bekerja')->nullable();
            $table->string('nomor_badan_hukum')->nullable();
            $table->text('alamat_perusahaan')->nullable();
            $table->string('kontak_perusahaan')->nullable();
            $table->string('jenis_perusahaan')->nullable();
            $table->integer('cabang_kota')->default(0);
            $table->integer('cabang_negara')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengguna_lulusan');
    }
};
