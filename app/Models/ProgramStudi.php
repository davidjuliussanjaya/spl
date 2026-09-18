<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProgramStudi extends Model
{
    protected $table = 'program_studi';

    protected $fillable = ['fakultas_id', 'kode', 'nama'];

    public function fakultas()
    {
        return $this->belongsTo(Fakultas::class);
    }

    public function lulusans()
    {
        return $this->hasMany(Lulusan::class);
    }
}
