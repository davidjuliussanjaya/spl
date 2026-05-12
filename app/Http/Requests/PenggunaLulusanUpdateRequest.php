<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PenggunaLulusanUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama_perusahaan' => 'required|string|max:255',
            'nama_penyelia' => 'required|string|max:255',
            'email_penyelia' => 'required|email|unique:pengguna_lulusan,email_penyelia,' . $this->id,
            'kontak_penyelia' => 'nullable|string',
            'jenis_perusahaan' => 'required|string|max:255',
            'alamat_perusahaan' => 'nullable|string',
            'cabang_kota' => 'nullable',
            'cabang_negara' => 'nullable',
        ];
    }
}
