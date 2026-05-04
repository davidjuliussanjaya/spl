<?php

namespace App\Http\Controllers;

use App\Http\Requests\SurveyStoreRequest;
use App\Http\Requests\SurveySubmitJawabanRequest;
use App\Http\Requests\SurveyUpdateRequest;
use App\Models\lulusan;
use App\Models\penggunalulusan;
use App\Models\ResponJawaban;
use App\Models\soal;
use App\Models\Survey;
use App\Services\SurveyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SurveyController extends Controller
{
    protected $surveyService;

    public function __construct(SurveyService $surveyService)
    {
        $this->surveyService = $surveyService;
    }

public function index()
{
    // Mengambil semua data survey, diurutkan dari yang terbaru (latest)
    // with() digunakan untuk memuat relasi (Eager Loading) agar tidak terjadi N+1 problem dan query lebih cepat
    $surveys = Survey::with(['lulusan', 'penggunalulusan'])->latest()->get();

    // Mengirim variabel $surveys ke halaman view
    return view('admin.survey.index', compact('surveys'));
}

    public function getPerusahaanData($id)
    {
        $data = \App\Models\PenggunaLulusan::find($id);
        return response()->json($data);
    }
    public function add()
{
    $perusahaan = \App\Models\PenggunaLulusan::all();
    
    // Ambil semua soal aktif untuk dipilih oleh Admin
    $daftarSoal = \App\Models\Soal::with('jawaban')
        ->where('is_active', 1)
        ->get();

    // Tambahkan data lulusan jika diperlukan di form
    $lulusan = \App\Models\Lulusan::all(); 

    return view('admin.survey.add', compact('perusahaan', 'daftarSoal', 'lulusan'));
}

    public function store(SurveyStoreRequest $request)
    {
        try {
            $this->surveyService->createSurvey($request->validated());
            return redirect()->route('survey')->with('success', 'Sesi Survey berhasil dibuat dan data instansi tersinkronisasi.');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }
    public function verifyCode(Request $request)
{
    $survey = Survey::where('access_code', $request->code)
                    ->where('is_completed', false)
                    ->first();

    if (!$survey) {
        return back()->with('error', 'Kode akses tidak valid atau survey telah selesai.');
    }

    return redirect()->route('survey.fill', $survey->access_code);
}

public function fill($code)
{
    $survey = Survey::with(['lulusan', 'penggunalulusan'])
                    ->where('access_code', $code)
                    ->firstOrFail();

    // Ambil soal hanya yang dipilih admin (lewat tabel pivot survey_soal)
    $soal = Soal::whereHas('surveys', function($q) use ($survey) {
        $q->where('survey_id', $survey->id);
    })->with('jawaban')->get();

    return view('fill_page', compact('survey', 'soal'));
}
    public function submitJawaban(SurveySubmitJawabanRequest $request, $code)
    {
        $survey = Survey::where('access_code', $code)->firstOrFail();

        try {
            $this->surveyService->submitJawaban($survey, $request->validated());

            return redirect('/')->with('success', 'Terima kasih, kuesioner evaluasi berhasil terkirim dan data Anda telah dicatat!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menyimpan jawaban: ' . $e->getMessage())->withInput();
        }
    }
public function edit($id)
{
    // Ambil survey berserta relasinya
    $survey = Survey::with(['lulusan', 'penggunalulusan', 'soals'])->findOrFail($id);
    
    $perusahaan = PenggunaLulusan::all();
    $lulusan = lulusan::all();
    $daftarSoal = Soal::where('is_active', 1)->get();

    return view('admin.survey.view', compact('survey', 'perusahaan', 'lulusan', 'daftarSoal'));
}

    public function update(SurveyUpdateRequest $request, $id)
    {
        $survey = Survey::findOrFail($id);

        if ($survey->is_completed) {
            return back()->with('error', 'Survey sudah diisi dan tidak dapat diubah lagi.');
        }

        try {
            $this->surveyService->updateSurvey($survey, $request->validated());

            return redirect()->route('survey.index')->with('success', 'Data Survey berhasil diperbarui.');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }
}
