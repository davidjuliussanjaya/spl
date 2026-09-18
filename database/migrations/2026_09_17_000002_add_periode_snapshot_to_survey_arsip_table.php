<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('survey_arsip', function (Blueprint $table) {
            $table->string('periode_kode', 50)->nullable();
            $table->string('periode_nama')->nullable();
            $table->date('periode_tanggal_mulai')->nullable();
            $table->date('periode_tanggal_berakhir')->nullable();
        });

        // Arsip yang masih mempunyai survei asal ikut memperoleh snapshot periode.
        DB::table('survey_arsip')
            ->join('survey', 'survey.id', '=', 'survey_arsip.survey_id')
            ->join('periode', 'periode.id', '=', 'survey.periode_id')
            ->select('survey_arsip.id', 'periode.kode_periode', 'periode.nama_periode', 'periode.tanggal_mulai', 'periode.tanggal_berakhir')
            ->orderBy('survey_arsip.id')
            ->chunkById(100, function ($arsip) {
                foreach ($arsip as $item) {
                    DB::table('survey_arsip')->where('id', $item->id)->update([
                        'periode_kode' => $item->kode_periode,
                        'periode_nama' => $item->nama_periode,
                        'periode_tanggal_mulai' => $item->tanggal_mulai,
                        'periode_tanggal_berakhir' => $item->tanggal_berakhir,
                    ]);
                }
            }, 'survey_arsip.id', 'id');
    }

    public function down(): void
    {
        Schema::table('survey_arsip', function (Blueprint $table) {
            $table->dropColumn([
                'periode_kode',
                'periode_nama',
                'periode_tanggal_mulai',
                'periode_tanggal_berakhir',
            ]);
        });
    }
};
