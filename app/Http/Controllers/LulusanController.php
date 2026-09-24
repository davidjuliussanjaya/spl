<?php

namespace App\Http\Controllers;

use App\Http\Requests\LulusanStoreRequest;
use App\Http\Requests\LulusanUpdateRequest;
use App\Models\Lulusan;
use App\Models\PenggunaLulusan;
use App\Models\Fakultas;
use App\Models\ProgramStudi;
use App\Models\Survey;
use App\Models\SurveyArsip;
use App\Services\LulusanService;
use App\Services\SatisfactionScoreService;
use App\Services\DashboardService;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class LulusanController extends Controller
{
    protected $lulusanService;

    public function __construct(
        LulusanService $lulusanService,
        private DashboardService $dashboardService,
    )
    {
        $this->lulusanService = $lulusanService;
    }

    public function index(Request $request)
    {
        $filters = [
            'periode' => collect(Arr::wrap($request->input('periode', [])))
                ->filter(fn ($value) => filled($value))
                ->values()
                ->all(),
            'program_studi' => collect(Arr::wrap($request->input('program_studi', [])))
                ->filter(fn ($value) => filled($value))
                ->values()
                ->all(),
        ];
        $request->merge($filters);

        $lulusan = $this->lulusanService->getFilteredLulusan($request);
        $filterOptions = $this->dashboardService->getFilterOptions($filters['periode']);

        return view('admin.lulusan.index', compact('lulusan', 'filters', 'filterOptions'));
    }

    public function add()
    {
        $perusahaan = \App\Models\PenggunaLulusan::select('id', 'nama_perusahaan')->get();
        $fakultasList = Fakultas::with('programStudis')->orderBy('kode')->get();
        
        return view('admin.lulusan.add', compact('perusahaan', 'fakultasList'));
    }

    public function create()
    {
        $perusahaan = PenggunaLulusan::select('id', 'nama_perusahaan')->get();
        $fakultasList = Fakultas::with('programStudis')->orderBy('kode')->get();
        return view('lulusan.create', compact('perusahaan', 'fakultasList'));
    }

    public function show($id, SatisfactionScoreService $satisfactionScoreService)
    {
        $lulusan = Lulusan::with(['pengguna', 'fakultasMaster', 'programStudi'])->findOrFail($id);
        $completedSurvey = Survey::query()
            ->where('lulusan_id', $lulusan->id)
            ->where('is_completed', true)
            ->latest('updated_at')
            ->first();
        // Arsip lama tidak selalu memiliki NIM; survey_id adalah penghubung
        // utama yang stabil antara lulusan dan arsip responsnya.
        $surveyArsip = $completedSurvey
            ? SurveyArsip::where('survey_id', $completedSurvey->id)->first()
            : null;
        $surveyArsip ??= SurveyArsip::query()
            ->where('lulusan_nim', $lulusan->nim)
            ->latest('submitted_at')
            ->first();
        $hasCompletedSurvey = $completedSurvey !== null || $surveyArsip !== null;

        $ratingItems = collect($surveyArsip?->jawaban_json ?? [])
            ->filter(fn (array $jawaban) => ($jawaban['jenis'] ?? null) === 'rating'
                && isset($jawaban['nilai'])
                && $jawaban['nilai'] !== null);
        $indeksKepuasan = $ratingItems->isNotEmpty()
            ? $satisfactionScoreService->calculate($ratingItems->pluck('nilai')->all(), 1, 1)
            : null;
        $ratingKategori = $ratingItems
            ->groupBy(fn (array $jawaban) => $jawaban['kategori'] ?? 'Lainnya')
            ->map(fn ($jawaban, $kategori) => [
                'kategori' => $kategori,
                'indeks' => round($jawaban->avg(fn (array $item) => (float) $item['nilai']), 2),
                'jumlah' => $jawaban->count(),
            ])
            ->sortBy('kategori')
            ->values();

        $fakultasList = $hasCompletedSurvey
            ? collect()
            : Fakultas::with('programStudis')->orderBy('kode')->get();

        return view('admin.lulusan.show', compact(
            'lulusan',
            'surveyArsip',
            'hasCompletedSurvey',
            'indeksKepuasan',
            'ratingKategori',
            'fakultasList',
        ));
    }

    public function updateMahasiswa(LulusanUpdateRequest $request, $id)
    {
        $lulusan = Lulusan::findOrFail($id);

        try {
            $this->lulusanService->updateMahasiswa($lulusan, $request->validated());

            return redirect()->route('lulusan.show', $lulusan->id)
                ->with('success', 'Data mahasiswa berhasil diperbarui.');
        } catch (\InvalidArgumentException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function store(LulusanStoreRequest $request)
    {
        try {
            // Eksekusi Logika melalui Service
            $this->lulusanService->storeLulusan($request->validated());

            // Redirect dengan feedback
            return redirect()->route('lulusan') // Sesuaikan route index Anda
                ->with('success', 'Data lulusan berhasil ditambahkan!');
                
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Titik integrasi untuk sinkronisasi data mahasiswa dari REST API eksternal.
     *
     * Konfigurasi endpoint, autentikasi, dan pemetaan data akan ditambahkan
     * setelah detail API dari sumber data tersedia.
     */
    public function syncMahasiswa()
    {
        return redirect()->route('lulusan')->with(
            'error',
            'Sinkronisasi data mahasiswa belum dapat dijalankan karena konfigurasi REST API belum tersedia.'
        );
    }
}
