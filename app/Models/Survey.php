<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Survey extends Model
{
    protected $table = 'survey';

    protected $fillable = [
        'access_code',
        'lulusan_id',
        'pengguna_lulusan_id',
        'judul',
        'deskripsi',
        'is_completed',
        'is_active'
    ];

    /**
     * Relasi ke tabel lulusan
     */
    public function soals() 
    {
        return $this->belongsToMany(Soal::class, 'survey_soal', 'survey_id', 'soal_id');
    }
    public function lulusan()
    {
        return $this->belongsTo(Lulusan::class);
    }

    /**
     * Relasi ke pengguna lulusan (perusahaan)
     */
    public function penggunaLulusan()
    {
        return $this->belongsTo(PenggunaLulusan::class);
    }

    /**
     * Relasi ke jawaban (jika masih dipakai)
     */
    public function jawaban()
    {
        return $this->hasMany(Jawaban::class);
    }
}
