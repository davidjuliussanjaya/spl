<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kategoris', function (Blueprint $table) {
            $table->string('status', 20)->default('utama')->after('deskripsi');
        });

        Schema::table('soal', function (Blueprint $table) {
            $table->dropForeign(['fakultas_id']);
            $table->dropColumn(['fakultas_id', 'peruntukan_fakultas']);
        });
    }

    public function down(): void
    {
        Schema::table('soal', function (Blueprint $table) {
            $table->enum('peruntukan_fakultas', ['FTI', 'FDIK', 'FEB', 'Umum'])->default('Umum');
            $table->foreignId('fakultas_id')->nullable()->constrained('fakultas')->restrictOnDelete();
        });

        Schema::table('kategoris', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
