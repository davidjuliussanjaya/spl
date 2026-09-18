<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Periode extends Model
{
    protected $table = 'periode';

    protected $fillable = [
        'kode_periode',
        'nama_periode',
        'tanggal_mulai',
        'tanggal_berakhir',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_berakhir' => 'date',
    ];

    public function surveys()
    {
        return $this->hasMany(Survey::class);
    }

    public function isBerlangsung(): bool
    {
        return today()->betweenIncluded($this->tanggal_mulai, $this->tanggal_berakhir);
    }
}
