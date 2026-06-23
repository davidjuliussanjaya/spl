<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SurveySubmitJawabanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama_pengisi'      => 'required|string|max:255',
            'jabatan_pengisi'   => 'nullable|string|max:255',
            'hp_pengisi'        => 'nullable|string|max:50',
            'email_pengisi'     => 'nullable|email|max:255',
            'nama_perusahaan'   => 'required|string|max:255',
            'nomor_badan_hukum' => 'nullable|string|max:255',
            'jenis_perusahaan'  => 'nullable|string|max:255',
            'alamat_perusahaan' => 'nullable|string',
            'kontak_perusahaan' => 'nullable|string|max:255',
            'cabang_kota'       => 'nullable|integer|min:0',
            'cabang_negara'     => 'nullable|integer|min:0',
            'jumlah_lulusan_bekerja' => 'nullable|integer|min:0',
            'jawaban'                => 'nullable|array',
            'mc'                     => 'nullable|array',
            'mc.*'                   => 'nullable|array',
            'mc.*.*'                 => 'nullable|integer',
            'mc_custom'              => 'nullable|array',
            'mc_custom.*'            => 'nullable|string|max:1000',
        ];
    }
}
