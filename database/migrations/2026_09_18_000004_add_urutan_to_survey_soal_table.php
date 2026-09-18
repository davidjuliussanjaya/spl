<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('survey_soal', function (Blueprint $table) {
            $table->unsignedInteger('urutan')->nullable()->after('soal_id');
            $table->index(['survey_id', 'urutan']);
        });
    }

    public function down(): void
    {
        Schema::table('survey_soal', function (Blueprint $table) {
            $table->dropIndex(['survey_id', 'urutan']);
            $table->dropColumn('urutan');
        });
    }
};
