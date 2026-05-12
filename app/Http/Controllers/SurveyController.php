<?php

namespace App\Http\Controllers;

use App\Http\Requests\SurveyBulkRequest;
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

    $fakultasLulusan = $survey->lulusan->fakultas ?? null;

    // Soal yang dipilih admin, difilter berdasarkan peruntukan fakultas lulusan
    $soal = Soal::whereHas('surveys', function($q) use ($survey) {
        $q->where('survey_id', $survey->id);
    })
    ->where(function($q) use ($fakultasLulusan) {
        $q->where('peruntukan_fakultas', 'Umum');
        if ($fakultasLulusan) {
            $q->orWhere('peruntukan_fakultas', $fakultasLulusan);
        }
    })
    ->with(['jawaban', 'kategori'])
    ->get();

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

    public function bulkCreate()
    {
        $tahunList = \App\Models\Lulusan::selectRaw('EXTRACT(YEAR FROM tahun_lulus) as tahun')
            ->whereNotNull('pengguna_lulusan_id')
            ->distinct()
            ->orderByDesc('tahun')
            ->pluck('tahun');

        $daftarSoal = \App\Models\Soal::with('jawaban')
            ->where('is_active', 1)
            ->get();

        return view('admin.survey.bulk', compact('tahunList', 'daftarSoal'));
    }

    public function bulkStore(SurveyBulkRequest $request)
    {
        try {
            $surveys = $this->surveyService->createBulkSurveys($request->validated());
            $count = count($surveys);
            return redirect()->route('survey')->with('success', "Berhasil membuat {$count} survey untuk lulusan tahun {$request->tahun_lulus}.");
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    public function getLulusanByTahun(Request $request)
    {
        $tahun = $request->tahun;
        $lulusan = \App\Models\Lulusan::whereRaw('EXTRACT(YEAR FROM tahun_lulus) = ?', [$tahun])
            ->whereNotNull('pengguna_lulusan_id')
            ->with('pengguna')
            ->get(['id', 'nama', 'nim', 'program_studi', 'pengguna_lulusan_id']);

        return response()->json($lulusan);
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
