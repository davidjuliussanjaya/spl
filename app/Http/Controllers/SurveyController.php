<?php

namespace App\Http\Controllers;

use App\Http\Requests\SurveyBulkRequest;
use App\Http\Requests\SurveyStoreRequest;
use App\Http\Requests\SurveySubmitJawabanRequest;
use App\Http\Requests\SurveyUpdateRequest;
use App\Models\Lulusan;
use App\Models\PenggunaLulusan;
use App\Models\ResponJawaban;
use App\Models\Soal;
use App\Models\Survey;
use App\Models\SurveyArsip;
use App\Services\SurveyService;
use App\Support\DatabaseYearExpression;
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

    public function index(Request $request)
    {
        $tahunList = Survey::whereNotNull('tahun')->distinct()->orderByDesc('tahun')->pluck('tahun');
        $selectedTahun = $request->filled('tahun') ? $request->string('tahun')->trim()->toString() : null;
        $surveys = collect();

        // Daftar survei hanya ditampilkan setelah admin memilih periode.
        if ($selectedTahun) {
            $surveys = Survey::with(['lulusan', 'penggunalulusan'])
                ->where('tahun', $selectedTahun)
                ->when($request->filled('cari'), function ($query) use ($request) {
                    $term = $request->string('cari')->trim()->toString();
                    $query->where(function ($search) use ($term) {
                        $search->where('judul', 'like', "%{$term}%")
                            ->orWhere('access_code', 'like', "%{$term}%")
                            ->orWhereHas('lulusan', fn ($lulusan) => $lulusan->where('nama', 'like', "%{$term}%"))
                            ->orWhereHas('penggunalulusan', fn ($perusahaan) => $perusahaan->where('nama_perusahaan', 'like', "%{$term}%"));
                    });
                })
                ->when($request->filled('status'), fn ($query) => $query->where('is_completed', $request->status === 'selesai'))
                ->latest('created_at')
                ->paginate(10)
                ->withQueryString();
        }

        return view('admin.survey.index', compact('surveys', 'tahunList', 'selectedTahun'));
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
    $daftarSoal = \App\Models\Soal::with(['jawaban', 'kategori'])
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
            return redirect()->route('survey', ['tahun' => $request->tahun])
                ->with('success', 'Sesi Survey berhasil dibuat dan data instansi tersinkronisasi.');
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
    $survey = Survey::with(['lulusan', 'penggunalulusan', 'soals.kategori', 'soals.jawaban'])->findOrFail($id);

    // Survei yang selesai dibaca dari snapshot arsip, bukan relasi master.
    if ($survey->is_completed) {
        $arsip = SurveyArsip::where('survey_id', $survey->id)->first();

        if (! $arsip) {
            return redirect()->route('survey', ['tahun' => $survey->tahun])
                ->with('error', 'Survei sudah selesai, tetapi arsip permanennya belum tersedia.');
        }

        return view('admin.report.arsip-detail', [
            'arsip' => $arsip,
            'detailTitle' => 'Detail Survei Selesai',
            'backUrl' => route('survey', ['tahun' => $survey->tahun]),
            'backLabel' => 'Kembali ke Daftar Survei',
            'breadcrumbLabel' => 'Survei',
        ]);
    }

    $perusahaan = PenggunaLulusan::all();
    $lulusan    = Lulusan::all();
    $daftarSoal = Soal::with('kategori')->where('is_active', 1)->get();

    $responGrouped = $survey->is_completed
        ? ResponJawaban::with('jawaban')->where('survey_id', $id)->get()->groupBy('soal_id')
        : collect();

    return view('admin.survey.view', compact('survey', 'perusahaan', 'lulusan', 'daftarSoal', 'responGrouped'));
}

    public function bulkCreate()
    {
        $tahunList = \App\Models\Lulusan::selectRaw(DatabaseYearExpression::fromDateColumn('tahun_lulus') . ' as tahun')
            ->whereNotNull('pengguna_lulusan_id')
            ->distinct()
            ->orderByDesc('tahun')
            ->pluck('tahun');

        $daftarSoal = \App\Models\Soal::with(['jawaban', 'kategori'])
            ->where('is_active', 1)
            ->get();

        return view('admin.survey.bulk', compact('tahunList', 'daftarSoal'));
    }

    public function bulkStore(SurveyBulkRequest $request)
    {
        try {
            $surveys = $this->surveyService->createBulkSurveys($request->validated());
            $count = count($surveys);
            return redirect()->route('survey', ['tahun' => $request->tahun])
                ->with('success', "Berhasil membuat {$count} survey untuk lulusan tahun {$request->tahun_lulus}.");
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    public function getLulusanByTahun(Request $request)
    {
        $tahun = $request->tahun;
        $lulusan = \App\Models\Lulusan::whereYear('tahun_lulus', $tahun)
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

            return redirect()->route('survey')->with('success', 'Data Survey berhasil diperbarui.');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy($id)
    {
        $survey = Survey::findOrFail($id);

        if ($survey->is_completed) {
            return back()->with('error', 'Survei yang sudah selesai tersimpan sebagai arsip permanen dan tidak dapat dihapus.');
        }

        try {
            DB::transaction(function () use ($survey) {
                ResponJawaban::where('survey_id', $survey->id)->delete();
                $survey->soals()->detach();
                $survey->delete();
            });

            return redirect()->route('survey')->with('success', 'Survey berhasil dihapus.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus survey: ' . $e->getMessage());
        }
    }
}
