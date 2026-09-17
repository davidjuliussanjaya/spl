<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PenggunaLulusan extends Model
{
    use HasFactory;

    protected $table = 'pengguna_lulusan';

    protected $fillable = [
        'nama_perusahaan',
        'nama_penyelia',
        'jabatan_penyelia',
        'kontak_penyelia',
        'email_penyelia',
        'jumlah_lulusan',
        'durasi_lulusan_bekerja',
        'nomor_badan_hukum',
        'alamat_perusahaan',
        'kontak_perusahaan',
        'jenis_perusahaan',
        'cabang_kota',
        'cabang_negara',
    ];

    public function lulusans(): HasMany
    {
        return $this->hasMany(Lulusan::class, 'pengguna_lulusan_id');
    }
}
