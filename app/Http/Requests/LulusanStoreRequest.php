<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LulusanStoreRequest extends FormRequest
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
            'pengguna_lulusan_id' => 'required|exists:pengguna_lulusan,id',
            'nama'                => 'required|string|max:255',
            'nim'                 => 'required|string|unique:lulusan,nim',
            'program_studi'       => 'required|string',
            'fakultas'            => 'required|string|max:255',
            'tahun_lulus'         => 'required|date',
            'status'              => 'nullable',
        ];
    }
}
