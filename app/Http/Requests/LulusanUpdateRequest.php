<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LulusanUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama' => 'required|string|max:255',
            'nim' => ['required', 'string', Rule::unique('lulusan', 'nim')->ignore($this->route('id'))],
            'program_studi_id' => 'required|exists:program_studi,id',
            'fakultas_id' => 'required|exists:fakultas,id',
            'tahun_lulus' => 'required|date',
        ];
    }
}
