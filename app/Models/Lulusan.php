<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
        'program_studi_id',
        'fakultas_id',
        'tahun_lulus',
        'is_aggregate',
    ];

    protected $casts = [
        'tahun_lulus' => 'date',
        'is_aggregate' => 'boolean',
    ];

    public function pengguna(): BelongsTo
    {
        return $this->belongsTo(PenggunaLulusan::class, 'pengguna_lulusan_id');
    }

    public function fakultasMaster(): BelongsTo
    {
        return $this->belongsTo(Fakultas::class, 'fakultas_id');
    }

    public function programStudi(): BelongsTo
    {
        return $this->belongsTo(ProgramStudi::class, 'program_studi_id');
    }

    public function surveys(): HasMany
    {
        return $this->hasMany(Survey::class);
    }
}
