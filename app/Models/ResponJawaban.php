<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ResponJawaban extends Model
{
    protected $table = 'respon_jawaban';

    protected $fillable = [
        'survey_id',
        'soal_id',
        'soal_text_snapshot',
        'jawaban_id',
        'jawaban_text_snapshot',
        'jawaban_text',
        'responden',
        'jumlah_lulusan_bekerja',
    ];

    // Relasi ke Survey (sesi pengisian)
    public function survey()
    {
        return $this->belongsTo(Survey::class);
    }

    // Relasi ke Soal (pertanyaan)
    public function soal()
    {
        return $this->belongsTo(Soal::class);
    }

    // Relasi ke Jawaban (opsi pilihan)
    public function jawaban()
    {
        return $this->belongsTo(Jawaban::class);
    }
}
