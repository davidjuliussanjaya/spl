<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kategori extends Model
{
    protected $table = 'kategoris';

    protected $fillable = [
        'nama_kategori',
        'deskripsi',
        'status',
        'fakultas_id',
    ];

    public function soal()
    {
        return $this->hasMany(Soal::class, 'kategori_id');
    }

    public function fakultas()
    {
        return $this->belongsTo(Fakultas::class);
    }
}
