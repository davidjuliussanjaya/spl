<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class soal extends Model
{
    protected $table = 'soal';

    protected $fillable = [
        'soal',
        'kode',
        'jenis_soal',
        'is_required',
        'is_active'
    ];

    public function jawaban()
    {
        return $this->hasMany(Jawaban::class);
    }
    public function surveys() 
    {
        return $this->belongsToMany(Survey::class, 'survey_soal', 'soal_id', 'survey_id');
    }
}
