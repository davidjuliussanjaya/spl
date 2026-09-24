<?php

namespace App\Http\Controllers;

use App\Http\Requests\SurveyBulkRequest;
use App\Http\Requests\SurveyStoreRequest;
use App\Http\Requests\SurveySubmitJawabanRequest;
use App\Http\Requests\SurveyUpdateRequest;
use App\Models\Lulusan;
use App\Models\PenggunaLulusan;
use App\Models\Periode;
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
        // Delapan kartu menghasilkan maksimal dua baris pada grid desktop (4 kolom).
        $periodeList = Periode::withCount('surveys')
            ->orderByDesc('tanggal_mulai')
            ->paginate(8)
            ->withQueryString();
        $selectedPeriode = $request->filled('periode_id')
            ? Periode::find($request->integer('periode_id'))
            : null;
        $surveys = collect();

        // Daftar survei hanya ditampilkan setelah admin memilih periode.
        if ($selectedPeriode) {
            $surveys = Survey::with(['lulusan', 'penggunalulusan', 'periode'])
                ->where('periode_id', $selectedPeriode->id)
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

        return view('admin.survey.index', compact('surveys', 'periodeList', 'selectedPeriode'));
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
    $daftarSoal = \App\Models\Soal::with(['jawaban', 'kategori.fakultas'])
        ->where('is_active', 1)
        ->get();

    // Tambahkan data lulusan jika diperlukan di form
    $lulusan = \App\Models\Lulusan::with('fakultasMaster')->get();

    $periodes = Periode::query()
        ->whereDate('tanggal_mulai', '<=', today())
        ->whereDate('tanggal_berakhir', '>=', today())
        ->orderByDesc('tanggal_mulai')
        ->get();

    return view('admin.survey.add', compact('perusahaan', 'daftarSoal', 'lulusan', 'periodes'));
}

    public function store(SurveyStoreRequest $request)
    {
        try {
            $this->surveyService->createSurvey($request->validated());
            return redirect()->route('survey', ['periode_id' => $request->periode_id])
                ->with('success', 'Sesi Survey berhasil dibuat dan data instansi tersinkronisasi.');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }
public function verifyCode(Request $request)
{
    $code = trim((string) $request->input('code'));

    $survey = Survey::with('periode')->where('access_code', $code)
                    ->where('is_completed', false)
                    ->where('is_active', true)
                    ->first();

    if (! $survey || ! $survey->periode?->isBerlangsung()) {
        return back()
            ->withInput()
            ->with('error', 'Kode akses tidak valid, survei tidak aktif atau telah selesai, atau periode pengisian belum berlangsung.');
    }

    return redirect()->route('survey.fill', $survey->access_code);
}

public function fill($code)
{
    $survey = Survey::with(['lulusan.programStudi', 'lulusan.fakultasMaster', 'penggunalulusan', 'periode'])
                    ->where('access_code', $code)
                    ->firstOrFail();

    $this->ensurePeriodIsOpen($survey);

    // Semua soal yang dipilih admin ditampilkan berdasarkan kategori instrumennya.
    $soal = $survey->soals()
    ->with(['jawaban', 'kategori'])
    // Urutan ini berasal dari susunan kategori saat admin membuat survei.
    ->orderByRaw('CASE WHEN survey_soal.urutan IS NULL THEN 1 ELSE 0 END')
    ->orderBy('survey_soal.urutan')
    ->get();

    return view('fill_page', compact('survey', 'soal'));
}
    public function submitJawaban(SurveySubmitJawabanRequest $request, $code)
    {
        $survey = Survey::with('periode')->where('access_code', $code)->firstOrFail();
        $this->ensurePeriodIsOpen($survey);

        try {
            $this->surveyService->submitJawaban($survey, $request->validated());

            return redirect('/')
                ->with('success', 'Jawaban Anda telah tersimpan dengan aman.')
                ->with('clear_survey_draft', $survey->access_code);
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menyimpan jawaban: ' . $e->getMessage())->withInput();
        }
    }
public function edit($id)
{
    $survey = Survey::with(['lulusan', 'penggunalulusan', 'periode', 'soals.kategori', 'soals.jawaban'])->findOrFail($id);

    // Survei yang selesai dibaca dari snapshot arsip, bukan relasi master.
    if ($survey->is_completed) {
        $arsip = SurveyArsip::where('survey_id', $survey->id)->first();

        if (! $arsip) {
            return redirect()->route('survey', ['periode_id' => $survey->periode_id])
                ->with('error', 'Survei sudah selesai, tetapi arsip permanennya belum tersedia.');
        }

        return view('admin.report.arsip-detail', [
            'arsip' => $arsip,
            'detailTitle' => 'Detail Survei Selesai',
            'backUrl' => route('survey', ['periode_id' => $survey->periode_id]),
            'backLabel' => 'Kembali ke Daftar Survei',
            'breadcrumbLabel' => 'Survei',
        ]);
    }

    $perusahaan = PenggunaLulusan::all();
    $lulusan = Lulusan::with('fakultasMaster')->get();
    $daftarSoal = Soal::with('kategori.fakultas')->where('is_active', 1)->get();
    $periodes = Periode::query()
        ->whereDate('tanggal_mulai', '<=', today())
        ->whereDate('tanggal_berakhir', '>=', today())
        ->orderByDesc('tanggal_mulai')
        ->get();

    $responGrouped = $survey->is_completed
        ? ResponJawaban::with('jawaban')->where('survey_id', $id)->get()->groupBy('soal_id')
        : collect();

    $selectedCategoryIds = $survey->soals
        ->sortBy(fn ($soal) => $soal->pivot->urutan ?? PHP_INT_MAX)
        ->pluck('kategori_id')
        ->filter()
        ->unique()
        ->values()
        ->all();

    return view('admin.survey.view', compact('survey', 'perusahaan', 'lulusan', 'daftarSoal', 'periodes', 'responGrouped', 'selectedCategoryIds'));
}

    public function bulkCreate()
    {
        $tahunList = \App\Models\Lulusan::selectRaw(DatabaseYearExpression::fromDateColumn('tahun_lulus') . ' as tahun')
            ->whereNotNull('pengguna_lulusan_id')
            ->distinct()
            ->orderByDesc('tahun')
            ->pluck('tahun');

        $daftarSoal = \App\Models\Soal::with(['jawaban', 'kategori.fakultas'])
            ->where('is_active', 1)
            ->get();

        $periodes = Periode::query()
            ->whereDate('tanggal_mulai', '<=', today())
            ->whereDate('tanggal_berakhir', '>=', today())
            ->orderByDesc('tanggal_mulai')
            ->get();

        return view('admin.survey.bulk', compact('tahunList', 'daftarSoal', 'periodes'));
    }

    public function bulkStore(SurveyBulkRequest $request)
    {
        try {
            $result = $this->surveyService->createBulkSurveys($request->validated());
            $count = count($result['surveys']);
            $message = "Berhasil membuat {$count} survey untuk lulusan tahun {$request->tahun_lulus}.";

            return redirect()->route('survey', ['periode_id' => $request->periode_id])
                ->with('success', $message);
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    public function getLulusanByTahun(Request $request)
    {
        $tahun = $request->tahun;
        $lulusan = \App\Models\Lulusan::whereYear('tahun_lulus', $tahun)
            ->whereNotNull('pengguna_lulusan_id')
            ->with(['pengguna', 'programStudi'])
            ->get(['id', 'nama', 'nim', 'program_studi_id', 'pengguna_lulusan_id'])
            ->each(fn ($lulusan) => $lulusan->setAttribute('program_studi', $lulusan->programStudi?->nama));

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

            return redirect()->route('survey', ['periode_id' => $request->periode_id])->with('success', 'Data Survey berhasil diperbarui.');
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

            return redirect()->route('survey', ['periode_id' => $survey->periode_id])->with('success', 'Survey berhasil dihapus.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus survey: ' . $e->getMessage());
        }
    }

    private function ensurePeriodIsOpen(Survey $survey): void
    {
        if ($survey->is_completed) {
            abort(403, 'Survei ini sudah selesai diisi.');
        }

        if (! $survey->is_active) {
            abort(403, 'Survei ini tidak aktif.');
        }

        if (! $survey->periode || ! $survey->periode->isBerlangsung()) {
            abort(403, 'Survei tidak dapat diisi di luar tanggal periode yang ditentukan.');
        }
    }
}
