<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SurveyArsip extends Model
{
    protected $table = 'survey_arsip';

    protected $fillable = [
        'survey_id',
        'access_code',
        'judul',
        'submitted_at',
        'tahun_instrumen',

        'lulusan_nama',
        'lulusan_nim',
        'lulusan_program_studi',
        'lulusan_fakultas',
        'lulusan_tahun_lulus',

        'perusahaan_nama',
        'perusahaan_jenis',
        'perusahaan_alamat',
        'perusahaan_kontak',
        'perusahaan_nomor_badan_hukum',
        'perusahaan_cabang_kota',
        'perusahaan_cabang_negara',

        'penyelia_nama',
        'penyelia_jabatan',
        'penyelia_email',
        'penyelia_kontak',
        'jumlah_lulusan_bekerja',

        'jawaban_json',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'jawaban_json' => 'array',
    ];
}
