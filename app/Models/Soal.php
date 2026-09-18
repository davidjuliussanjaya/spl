<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Soal extends Model
{
    protected $table = 'soal';

    protected $fillable = [
        'instrumen_id',
        'soal',
        'kode',
        'jenis_soal',
        'kategori_id',
        'is_required',
        'is_active',
    ];

    public function instrumen()
    {
        return $this->belongsTo(Instrumen::class);
    }

    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'kategori_id');
    }

    public function jawaban()
    {
        return $this->hasMany(Jawaban::class);
    }

    public function surveys()
    {
        return $this->belongsToMany(Survey::class, 'survey_soal', 'soal_id', 'survey_id')
            ->withPivot('urutan')
            ->withTimestamps();
    }
}
