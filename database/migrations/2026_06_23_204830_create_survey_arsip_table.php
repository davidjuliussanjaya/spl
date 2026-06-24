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
        Schema::create('survey_arsip', function (Blueprint $table) {
            $table->id();

            // Referensi ke survey asli — hanya untuk keterbacaan, BUKAN FK
            // Agar arsip tidak ikut terhapus bila survey dihapus
            $table->unsignedBigInteger('survey_id')->nullable();
            $table->string('access_code', 20)->nullable();
            $table->string('judul')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->string('tahun_instrumen', 10)->nullable();

            // ── Identitas Lulusan ────────────────────────────────────────────
            $table->string('lulusan_nama')->nullable();
            $table->string('lulusan_nim', 50)->nullable();
            $table->string('lulusan_program_studi')->nullable();
            $table->string('lulusan_fakultas', 20)->nullable();
            $table->string('lulusan_tahun_lulus', 20)->nullable();

            // ── Identitas Perusahaan / Instansi ─────────────────────────────
            $table->string('perusahaan_nama')->nullable();
            $table->string('perusahaan_jenis', 100)->nullable();
            $table->text('perusahaan_alamat')->nullable();
            $table->string('perusahaan_kontak', 50)->nullable();
            $table->string('perusahaan_nomor_badan_hukum', 100)->nullable();
            $table->string('perusahaan_cabang_kota', 100)->nullable();
            $table->string('perusahaan_cabang_negara', 100)->nullable();

            // ── Identitas Penyelia yang Mengisi ──────────────────────────────
            $table->string('penyelia_nama')->nullable();
            $table->string('penyelia_jabatan', 100)->nullable();
            $table->string('penyelia_email', 150)->nullable();
            $table->string('penyelia_kontak', 50)->nullable();
            $table->string('jumlah_lulusan_bekerja', 20)->nullable();

            // ── Jawaban Lengkap dalam format JSON ────────────────────────────
            // Format: array of { kode, kategori, soal, jenis, jawaban, nilai }
            // Disimpan sebagai teks — tidak dapat diubah dari luar
            $table->text('jawaban_json');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('survey_arsip');
    }
};
