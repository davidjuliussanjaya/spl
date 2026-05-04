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
            'nama_pengisi'  => 'required|string|max:255',
            'hp_pengisi'    => 'nullable|string|max:50',
            'email_pengisi' => 'nullable|email|max:255',
            'jawaban'       => 'required|array',
        ];
    }
}
