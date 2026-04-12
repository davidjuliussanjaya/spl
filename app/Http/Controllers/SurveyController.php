<?php

namespace App\Http\Controllers;

use App\Models\soal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SurveyController extends Controller
{
    public function index()
    {
        return view('admin.survey.index');
    }

    public function add()
    {
        $soal = soal::with('jawaban')
        ->where('is_active', 1)
        ->get();

    return view('admin.survey.add', compact('soal'));
    }

     public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            // ========================
            // SIMPAN DATA RESPONDEN
            // ========================
            $responden = Responden::create([
                'nama' => $request->nama,
                'hp' => $request->hp,
                'email' => $request->email,
                'nama_perusahaan' => $request->nama_perusahaan,
                'badan_hukum' => $request->badan_hukum,
                'alamat_perusahaan' => $request->alamat_perusahaan,
                'telp_perusahaan' => $request->telp_perusahaan,
                'jenis' => isset($request->jenis) ? implode(',', $request->jenis) : null,
                'industri_lain' => $request->industri_lain,
                'jasa_lain' => $request->jasa_lain,
                'cabang_kota' => $request->cabang_kota,
                'kota_lain' => $request->kota_lain,
                'cabang_luar' => $request->cabang_luar,
                'luar_negeri' => $request->luar_negeri,
            ]);

            // ========================
            // SIMPAN JAWABAN
            // ========================
            if ($request->jawaban) {
                foreach ($request->jawaban as $soal_id => $jawaban) {

                    DB::table('jawaban_user')->insert([
                        'responden_id' => $responden->id,
                        'soal_id' => $soal_id,
                        'jawaban' => is_numeric($jawaban) ? null : $jawaban,
                        'jawaban_id' => is_numeric($jawaban) ? $jawaban : null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('survey.create')
                ->with('success', 'Survey berhasil dikirim!');
        } catch (\Exception $e) {

            DB::rollback();

            return back()->with('error', 'Gagal menyimpan: ' . $e->getMessage());
        }
    }
}
