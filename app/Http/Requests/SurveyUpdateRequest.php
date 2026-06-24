<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SurveyUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'judul'               => 'required|string|max:255',
            'tahun'               => 'required|digits:4|integer',
            'lulusan_id'          => 'required',
            'pengguna_lulusan_id' => 'required',
            'soal_pilihan'        => 'required|array|min:1',
            'nama'                => 'nullable|string|max:255',
            'hp'                  => 'nullable|string|max:50',
            'email'               => 'nullable|email|max:255',
            'badan_hukum'         => 'nullable|string|max:255',
            'telp_perusahaan'     => 'nullable|string|max:50',
            'alamat_perusahaan'   => 'nullable|string',
        ];
    }
}
