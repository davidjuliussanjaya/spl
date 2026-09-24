<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Soal extends Model
{
    protected $table = 'soal';

    protected $fillable = [
        'instrumen_id',
        'soal',
        'kode',
        'jenis_soal',
        'allows_multiple_answers',
        'allows_custom_answer',
        'kategori_id',
        'is_required',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'allows_multiple_answers' => 'boolean',
            'allows_custom_answer' => 'boolean',
            'is_required' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function instrumen()
    {
        return $this->belongsTo(Instrumen::class);
    }

    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'kategori_id');
    }

    public function jawaban()
    {
        return $this->hasMany(Jawaban::class);
    }

    public function surveys()
    {
        return $this->belongsToMany(Survey::class, 'survey_soal', 'soal_id', 'survey_id')
            ->withPivot('urutan')
            ->withTimestamps();
    }
}
