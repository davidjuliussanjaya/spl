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
        Schema::table('lulusan', function (Blueprint $table) {
            $table->string('fakultas')->after('program_studi')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lulusan', function (Blueprint $table) {
            $table->dropColumn('fakultas');
        });
    }
};
