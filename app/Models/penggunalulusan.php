<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class penggunalulusan extends Model
{
    use HasFactory;

    // Nama tabel secara eksplisit (opsional jika nama tabel sudah sesuai standar plural)
    protected $table = 'pengguna_lulusan';

    // Kolom yang boleh diisi (mass assignable)
    protected $fillable = [
        'nama_perusahaan',
        'nama_penyelia',
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

    /**
     * Relasi ke model Lulusan.
     * Satu perusahaan bisa memiliki banyak lulusan yang bekerja di sana.
     */
    public function lulusans(): HasMany
    {
        return $this->hasMany(lulusan::class, 'pengguna_lulusan_id');
    }
}
