<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
            'tahun'          => 'required|digits:4|integer',
            'deskripsi'      => 'nullable|string',
            'tahun_lulus'    => 'required|digits:4|integer',
            'soal_pilihan'   => 'required|array|min:1',
            'soal_pilihan.*' => 'exists:soal,id',
        ];
    }

    public function messages(): array
    {
        return [
            'tahun_lulus.required' => 'Tahun lulus wajib dipilih.',
            'tahun_lulus.digits'   => 'Tahun lulus harus 4 digit.',
            'soal_pilihan.required' => 'Minimal pilih satu pertanyaan.',
        ];
    }
}
