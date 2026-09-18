<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Fakultas extends Model
{
    protected $table = 'fakultas';

    protected $fillable = ['kode', 'nama'];

    public function programStudis()
    {
        return $this->hasMany(ProgramStudi::class);
    }

    public function lulusans()
    {
        return $this->hasMany(Lulusan::class);
    }

}
