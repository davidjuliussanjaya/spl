<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PertanyaanStoreRequest extends FormRequest
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
            'question'  => 'required|string',
            'kategori_id'  => 'required|exists:kategoris,id',
            'type'      => 'required|in:radio,rating,text',
            'allows_multiple_answers' => 'nullable|boolean',
            'allows_custom_answer' => 'nullable|boolean',
            'kode'      => ['nullable', 'string', 'max:255', Rule::unique('soal', 'kode')->ignore($this->route('id'))],
            'jawaban'   => 'exclude_if:type,text|required|array|min:1|max:5',
            'jawaban.*' => 'exclude_if:type,text|required|string',
            'nilai'     => 'exclude_if:type,text|required_if:type,rating|array',
            'nilai.*'   => 'exclude_if:type,text|nullable|numeric',
        ];
    }

    /**
     * Opsi kosong dari baris yang belum diisi tidak ikut divalidasi/disimpan.
     * Nilai opsi dibentuk ulang agar urutan penilaian selalu konsisten.
     */
    protected function prepareForValidation(): void
    {
        $kode = trim((string) $this->input('kode', ''));
        $type = $this->input('type');

        if ($type === 'text') {
            $this->merge([
                'kode' => $kode === '' ? null : $kode,
                'jawaban' => [],
                'nilai' => [],
            ]);

            return;
        }

        $jawaban = collect((array) $this->input('jawaban', []))
            ->map(fn ($teks) => trim((string) $teks))
            ->filter(fn ($teks) => $teks !== '')
            ->values()
            ->all();

        $nilaiAwal = count($jawaban) >= 5 ? 5 : 4;
        $nilai = array_map(
            fn ($index) => max($nilaiAwal - $index, 1),
            array_keys($jawaban),
        );

        $this->merge([
            'kode' => $kode === '' ? null : $kode,
            'jawaban' => $jawaban,
            'nilai' => $nilai,
        ]);
    }
}
