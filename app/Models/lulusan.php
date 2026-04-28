<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class lulusan extends Model
{
    use HasFactory;

    protected $table = 'lulusan';

    protected $fillable = [
        'pengguna_lulusan_id',
        'nama',
        'nim',
        'program_studi',
        'tahun_lulus',
        'status',
    ];

    // Casting tipe data (agar tahun_lulus otomatis menjadi objek Carbon/Date)
    protected $casts = [
        'tahun_lulus' => 'date',
        'status' => 'boolean',
    ];

    /**
     * Relasi ke model PenggunaLulusan.
     * Satu lulusan terikat pada satu perusahaan/pengguna.
     */
    public function pengguna(): BelongsTo
    {
        return $this->belongsTo(PenggunaLulusan::class, 'pengguna_lulusan_id');
    }
}
