<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PenggunaLulusanStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nama_perusahaan' => 'required|string|max:255',
            'nama_penyelia' => 'required|string|max:255',
            'email_penyelia' => 'required|email|unique:pengguna_lulusan,email_penyelia',
            'kontak_penyelia' => 'nullable|string',
            'jenis_perusahaan' => 'required|string|max:255',
            'alamat_perusahaan' => 'nullable|string',
            'cabang_kota' => 'nullable',
            'cabang_negara' => 'nullable',
        ];
    }
}
