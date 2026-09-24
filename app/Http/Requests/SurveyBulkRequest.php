<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SurveyBulkRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'judul'          => 'required|string|max:255',
            'periode_id'     => [
                'required',
                Rule::exists('periode', 'id')->where(fn ($query) => $query
                    ->whereDate('tanggal_mulai', '<=', today())
                    ->whereDate('tanggal_berakhir', '>=', today())),
            ],
            'deskripsi'      => 'nullable|string',
            'tahun_lulus'    => 'required|digits:4|integer',
            'soal_pilihan'   => 'required|array|min:1',
            'soal_pilihan.*' => 'exists:soal,id',
            'kategori_urutan' => 'nullable|array',
            'kategori_urutan.*' => 'distinct|exists:kategoris,id',
        ];
    }

    public function messages(): array
    {
        return [
            'tahun_lulus.required' => 'Tahun lulus wajib dipilih.',
            'tahun_lulus.digits'   => 'Tahun lulus harus 4 digit.',
            'soal_pilihan.required' => 'Minimal pilih satu pertanyaan.',
            'periode_id.exists' => 'Pilih periode survei yang sedang berlangsung.',
        ];
    }
}
