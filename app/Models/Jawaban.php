<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Jawaban extends Model
{
    protected $table = 'jawaban';

    protected $fillable = [
        'soal_id',
        'jawaban',
        'nilai',
        'urutan',
    ];

    public function soal()
    {
        return $this->belongsTo(Soal::class);
    }
}
