<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Instrumen extends Model
{
    protected $table = 'instrumen';

    protected $fillable = [
        'tahun',
        'judul',
        'deskripsi',
        'is_active',
    ];

    public function soals()
    {
        return $this->hasMany(Soal::class);
    }
}
