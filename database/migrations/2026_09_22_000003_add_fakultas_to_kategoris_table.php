<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kategoris', function (Blueprint $table) {
            // Null berarti kategori umum dan dapat digunakan oleh semua fakultas.
            $table->foreignId('fakultas_id')
                ->nullable()
                ->after('status')
                ->constrained('fakultas')
                ->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('kategoris', function (Blueprint $table) {
            $table->dropForeign(['fakultas_id']);
            $table->dropColumn('fakultas_id');
        });
    }
};
