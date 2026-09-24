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
        'is_active',
        'fakultas_id',
    ];

    public function soal()
    {
        return $this->hasMany(Soal::class, 'kategori_id');
    }

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function fakultas()
    {
        return $this->belongsTo(Fakultas::class);
    }
}
