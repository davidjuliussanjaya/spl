<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class jawaban extends Model
{
    protected $table = 'jawaban'; 

    protected $fillable = [
        'soal_id',
        'jawaban',
        'nilai',
        'urutan'
    ];

    // Relasi ke Soal
    public function soal()
    {
        return $this->belongsTo(Soal::class);
    }
}
