<?php

namespace App\Http\Requests;

use App\Models\Survey;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

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
            'jumlah_lulusan_bekerja' => 'required|integer|min:1',
            'jawaban'                => 'nullable|array',
            'jawaban.*'              => 'nullable',
            'mc'                     => 'nullable|array',
            'mc.*'                   => 'nullable',
            'mc.*.*'                 => 'nullable|integer',
            'mc_custom'              => 'nullable|array',
            'mc_custom.*'            => 'nullable|string|max:1000',
        ];
    }

    /**
     * Persyaratan jawaban mengikuti soal yang memang dipilih untuk sesi survei,
     * bukan hanya atribut HTML di halaman pengisian.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $survey = Survey::where('access_code', $this->route('code'))->first();

            if (! $survey) {
                return;
            }

            $soals = $survey->soals()
                ->with('jawaban:id,soal_id')
                ->get();

            foreach ($soals as $soal) {
                $soalId = $soal->id;

                if ($soal->jenis_soal === 'multiple_choice') {
                    $rawJawaban = $this->input("mc.{$soalId}");

                    if (! $soal->allows_multiple_answers && is_array($rawJawaban)) {
                        $validator->errors()->add("mc.{$soalId}", 'Pertanyaan ini hanya dapat memiliki satu jawaban.');
                        continue;
                    }

                    $jawabanTerpilih = collect(is_array($rawJawaban) ? $rawJawaban : [$rawJawaban])
                        ->filter(fn ($id) => $id !== null && $id !== '')
                        ->map(fn ($id) => (int) $id)
                        ->intersect($soal->jawaban->pluck('id'));

                    if ($soal->is_required) {
                        $jawabanLainnya = trim((string) $this->input("mc_custom.{$soalId}", ''));

                        if ($jawabanTerpilih->isEmpty() && $jawabanLainnya === '') {
                            $validator->errors()->add("mc.{$soalId}", 'Pilih minimal satu jawaban atau isi pilihan lainnya untuk pertanyaan wajib ini.');
                        }
                    }

                    continue;
                }

                if (! $soal->is_required) {
                    continue;
                }

                if ($soal->jenis_soal === 'essay') {
                    if (! filled($this->input("jawaban.{$soalId}"))) {
                        $validator->errors()->add("jawaban.{$soalId}", 'Pertanyaan wajib ini harus diisi.');
                    }

                    continue;
                }

                $jawabanId = (int) $this->input("jawaban.{$soalId}");
                if (! $soal->jawaban->contains('id', $jawabanId)) {
                    $validator->errors()->add("jawaban.{$soalId}", 'Pilih salah satu jawaban untuk pertanyaan wajib ini.');
                }
            }
        });
    }
}
