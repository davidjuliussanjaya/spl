<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Lulusan extends Model
{
    use HasFactory;

    protected $table = 'lulusan';

    protected $fillable = [
        'pengguna_lulusan_id',
        'nama',
        'nim',
        'program_studi',
        'fakultas',
        'tahun_lulus',
        'status',
    ];

    protected $casts = [
        'tahun_lulus' => 'date',
        'status' => 'boolean',
    ];

    public function pengguna(): BelongsTo
    {
        return $this->belongsTo(PenggunaLulusan::class, 'pengguna_lulusan_id');
    }
}
