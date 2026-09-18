<?php

namespace App\Services;

use App\Models\SurveyArsip;
use App\Models\Lulusan;
use App\Models\Fakultas;
use App\Models\ProgramStudi;
use App\Models\Survey;
use Illuminate\Support\Arr;

class DashboardService
{
    public function __construct(private SatisfactionScoreService $satisfactionScoreService)
    {
    }

    public function getDashboardData(array $filters = []): array
    {
        $periode = collect(Arr::wrap($filters['periode'] ?? []))
            ->filter(fn ($value) => filled($value))
            ->values()
            ->all();

        $filters['periode'] = $periode;

        $fakultas = $filters['fakultas'] ?? null;
        $programStudi = collect(Arr::wrap($filters['program_studi'] ?? []))
            ->filter(fn ($value) => filled($value))
            ->values()
            ->all();
        $filters['program_studi'] = $programStudi;
        $fakultasProdi = $this->getFakultasProdiOptions();

        if ($fakultas && !empty($programStudi)) {
            $programStudi = array_values(array_intersect($programStudi, $fakultasProdi[$fakultas] ?? []));
            $filters['program_studi'] = $programStudi;
        }

        $arsipQuery = SurveyArsip::query();

        if (!empty($periode)) {
            $arsipQuery->whereIn('periode_kode', $periode);
        }

        if ($fakultas) {
            $arsipQuery->where('lulusan_fakultas', $fakultas);
        }

        if (!empty($programStudi)) {
            $arsipQuery->whereIn('lulusan_program_studi', $programStudi);
        }

        $arsipList = $arsipQuery->get();
        $totalSurvey = $arsipList->count();
        $penggunaBySurvey = Survey::query()
            ->whereIn('id', $arsipList->pluck('survey_id')->filter()->unique())
            ->pluck('pengguna_lulusan_id', 'id');
        $totalResponden = $this->countUniqueRespondents($arsipList, $penggunaBySurvey);
        $totalLulusan = $this->getTotalLulusanDalamCakupan($periode, $fakultas, $programStudi);
        $respondenBelumMengisi = $this->getTotalRespondenBelumMengisi($periode, $fakultas, $programStudi);

        $ratingByKategori = [];
        $allRatings = [];

        foreach ($arsipList as $arsip) {
            foreach ($arsip->jawaban_json ?? [] as $item) {
                if (($item['jenis'] ?? '') !== 'rating' || !isset($item['nilai']) || $item['nilai'] === null) {
                    continue;
                }

                $kategori = $item['kategori'] ?? 'Lainnya';
                $nilai = (int) $item['nilai'];

                $ratingByKategori[$kategori][] = $nilai;
                $allRatings[] = $nilai;
            }
        }

        $skorKepuasan = $this->satisfactionScoreService->calculate(
            $allRatings,
            $totalResponden,
            $totalLulusan,
        );
        $rataKeseluruhan = $skorKepuasan['skor_akhir'];

        // Instrumen, kategori, dan label jawaban dapat berubah antarperiode. Karena
        // itu, hitung indeks setiap periode lebih dahulu; indeks globalnya kemudian
        // merupakan rata-rata skor akhir periode dengan bobot jumlah lulusan (NJ).
        $periodSatisfactionSummaries = $arsipList
            ->filter(fn ($arsip) => filled($arsip->periode_kode))
            ->groupBy(fn ($arsip) => (string) $arsip->periode_kode)
            ->map(function ($periodArsip, $period) use ($fakultas, $programStudi, $penggunaBySurvey) {
                $ratings = [];

                foreach ($periodArsip as $arsip) {
                    foreach ($arsip->jawaban_json ?? [] as $item) {
                        if (($item['jenis'] ?? '') !== 'rating' || !isset($item['nilai']) || $item['nilai'] === null) {
                            continue;
                        }

                        $nilai = (int) $item['nilai'];
                        $ratings[] = $nilai;
                    }
                }

                $totalRespondenPeriode = $this->countUniqueRespondents($periodArsip, $penggunaBySurvey);
                $totalLulusanPeriode = $this->getTotalLulusanDalamCakupan([$period], $fakultas, $programStudi);
                $skorPeriode = $this->satisfactionScoreService->calculate(
                    $ratings,
                    $totalRespondenPeriode,
                    $totalLulusanPeriode,
                );

                return [
                    'periode' => $period,
                    'periode_label' => $periodArsip->first()?->periode_nama ?: $period,
                    'tanggal_mulai' => $periodArsip->first()?->periode_tanggal_mulai,
                    'total_survey' => $periodArsip->count(),
                    'total_responden' => $totalRespondenPeriode,
                    'total_lulusan' => $totalLulusanPeriode,
                    'response_rate_pct' => round($skorPeriode['response_rate_pct'], 1),
                    'faktor_pembobot' => round($skorPeriode['faktor_pembobot'], 2),
                    'skor_murni' => round($skorPeriode['skor_murni'], 2),
                    'skor_akhir' => round($skorPeriode['skor_akhir'], 2),
                    'rumus' => $skorPeriode['rumus'],
                ];
            })
            ->sortByDesc('tanggal_mulai')
            ->values();

        $periodsWithPopulation = $periodSatisfactionSummaries
            ->filter(fn ($item) => $item['total_lulusan'] > 0);
        $totalBobotPeriode = $periodsWithPopulation->sum('total_lulusan');
        $globalPeriodScore = [
            'skor_akhir' => $totalBobotPeriode > 0
                ? round($periodsWithPopulation->sum(fn ($item) => $item['skor_akhir'] * $item['total_lulusan']) / $totalBobotPeriode, 2)
                : 0,
            'skor_murni' => $totalBobotPeriode > 0
                ? round($periodsWithPopulation->sum(fn ($item) => $item['skor_murni'] * $item['total_lulusan']) / $totalBobotPeriode, 2)
                : 0,
            'total_lulusan' => $totalBobotPeriode,
            'total_responden' => $periodsWithPopulation->sum('total_responden'),
            'total_periode' => $periodSatisfactionSummaries->count(),
        ];
        $globalPeriodScore['response_rate_pct'] = $globalPeriodScore['total_lulusan'] > 0
            ? round(($globalPeriodScore['total_responden'] / $globalPeriodScore['total_lulusan']) * 100, 1)
            : 0;
        $periodTrend = $periodSatisfactionSummaries->sortBy('tanggal_mulai')->values();
        $periodTrendLabels = $periodTrend->pluck('periode_label')->all();
        $periodTrendData = $periodTrend->pluck('skor_akhir')->all();

        $kategoriStats = collect($ratingByKategori)
            ->map(function ($nilai, $kategori) use ($totalResponden, $totalLulusan) {
                $skor = $this->satisfactionScoreService->calculate($nilai, $totalResponden, $totalLulusan);

                return (object) [
                    'kategori' => $kategori,
                    'rata_rata' => $skor['skor_akhir'],
                    'skor_murni' => $skor['skor_murni'],
                    'total_respon' => count($nilai),
                ];
            })
            ->sortByDesc('rata_rata')
            ->values();

        $chartLabels = $kategoriStats->pluck('kategori')->toArray();
        $chartData = $kategoriStats->pluck('rata_rata')->map(fn ($value) => round($value, 2))->toArray();
        $kategoriTerbaik = $kategoriStats->first();
        $kategoriTerlemah = $kategoriStats->last();

        $kepuasanPerKategori = collect($ratingByKategori)
            ->map(function ($nilai, $kategori) use ($totalResponden, $totalLulusan) {
                $total = count($nilai);
                $countSangatBaik = count(array_filter($nilai, fn ($value) => $value == 4));
                $countBaik = count(array_filter($nilai, fn ($value) => $value == 3));
                $countKurang = count(array_filter($nilai, fn ($value) => $value == 2));
                $countSangatKurang = count(array_filter($nilai, fn ($value) => $value == 1));

                $skor = $this->satisfactionScoreService->calculate($nilai, $totalResponden, $totalLulusan);

                return [
                    'kategori' => $kategori,
                    'total_respon' => $total,
                    'pct_sb' => $total > 0 ? round($countSangatBaik / $total * 100, 1) : 0,
                    'pct_b' => $total > 0 ? round($countBaik / $total * 100, 1) : 0,
                    'pct_k' => $total > 0 ? round($countKurang / $total * 100, 1) : 0,
                    'pct_sk' => $total > 0 ? round($countSangatKurang / $total * 100, 1) : 0,
                    'skor_murni' => $skor['skor_murni'],
                    'skor_akhir' => $skor['skor_akhir'],
                ];
            })
            ->sortKeys()
            ->values();

        $kategoriDetails = collect($ratingByKategori)
            ->map(function ($nilai, $kategori) use ($totalResponden, $totalLulusan) {
                $total = count($nilai);
                $counts = [
                    'sb' => count(array_filter($nilai, fn ($value) => $value == 4)),
                    'b' => count(array_filter($nilai, fn ($value) => $value == 3)),
                    'k' => count(array_filter($nilai, fn ($value) => $value == 2)),
                    'sk' => count(array_filter($nilai, fn ($value) => $value == 1)),
                ];

                $skor = $this->satisfactionScoreService->calculate($nilai, $totalResponden, $totalLulusan);

                return [
                    'kategori' => $kategori,
                    'rata_rata' => round($skor['skor_akhir'], 2),
                    'skor_murni' => round($skor['skor_murni'], 2),
                    'total_respon' => $total,
                    'counts' => $counts,
                    'percentages' => [
                        'sb' => $total > 0 ? round($counts['sb'] / $total * 100, 1) : 0,
                        'b' => $total > 0 ? round($counts['b'] / $total * 100, 1) : 0,
                        'k' => $total > 0 ? round($counts['k'] / $total * 100, 1) : 0,
                        'sk' => $total > 0 ? round($counts['sk'] / $total * 100, 1) : 0,
                    ],
                ];
            })
            ->sortBy('kategori')
            ->values();

        $countKategori = $kepuasanPerKategori->count();
        $sumSangatBaik = $kepuasanPerKategori->sum('pct_sb');
        $sumBaik = $kepuasanPerKategori->sum('pct_b');
        $sumKurang = $kepuasanPerKategori->sum('pct_k');
        $sumSangatKurang = $kepuasanPerKategori->sum('pct_sk');

        $kepuasanRingkasan = [
            'total' => [
                'sb' => round($sumSangatBaik, 1),
                'b' => round($sumBaik, 1),
                'k' => round($sumKurang, 1),
                'sk' => round($sumSangatKurang, 1),
            ],
            'rata' => $countKategori > 0 ? [
                'sb' => round($sumSangatBaik / $countKategori, 1),
                'b' => round($sumBaik / $countKategori, 1),
                'k' => round($sumKurang / $countKategori, 1),
                'sk' => round($sumSangatKurang / $countKategori, 1),
            ] : ['sb' => 0, 'b' => 0, 'k' => 0, 'sk' => 0],
        ];
        $totalResponKepuasan = count($allRatings);

        $respondenProdiStats = $arsipList
            ->groupBy(fn ($arsip) => $arsip->lulusan_program_studi ?: 'Tidak diketahui')
            ->map(fn ($items, $prodi) => [
                'prodi' => $prodi,
                'total' => $items->count(),
            ])
            ->sortByDesc('total')
            ->values();

        $respondenProdiLabels = $respondenProdiStats->pluck('prodi')->toArray();
        $respondenProdiData = $respondenProdiStats->pluck('total')->toArray();

        $prodiDetails = $arsipList
            ->groupBy(fn ($arsip) => $arsip->lulusan_program_studi ?: 'Tidak diketahui')
            ->map(function ($items, $prodi) {
                $jenisPerusahaan = $items
                    ->groupBy(fn ($arsip) => $arsip->perusahaan_jenis ?: 'Tidak diketahui')
                    ->map(fn ($group, $jenis) => [
                        'label' => $jenis,
                        'total' => $group->count(),
                    ])
                    ->sortByDesc('total')
                    ->values();

                return [
                    'prodi' => $prodi,
                    'fakultas' => $items->pluck('lulusan_fakultas')->filter()->unique()->implode(', ') ?: 'Tidak diketahui',
                    'total' => $items->count(),
                    'jenis_perusahaan' => $jenisPerusahaan,
                ];
            })
            ->values();

        $komentarTerbaru = $arsipList
            ->sortByDesc('submitted_at')
            ->map(function ($arsip) {
                $essay = collect($arsip->jawaban_json ?? [])->firstWhere('jenis', 'essay');

                if (!$essay || empty($essay['jawaban'])) {
                    return null;
                }

                return (object) [
                    'jawaban_text' => $essay['jawaban'],
                    'responden' => $arsip->penyelia_nama,
                    'soal_teks' => $essay['soal'] ?? null,
                    'nama_perusahaan' => $arsip->perusahaan_nama,
                ];
            })
            ->filter()
            ->values();

        $filterOptions = $this->getFilterOptions();

        return compact(
            'totalSurvey',
            'totalResponden',
            'respondenBelumMengisi',
            'totalLulusan',
            'rataKeseluruhan',
            'kategoriTerbaik',
            'kategoriTerlemah',
            'chartLabels',
            'chartData',
            'respondenProdiLabels',
            'respondenProdiData',
            'prodiDetails',
            'kepuasanPerKategori',
            'kepuasanRingkasan',
            'totalResponKepuasan',
            'skorKepuasan',
            'periodSatisfactionSummaries',
            'globalPeriodScore',
            'periodTrendLabels',
            'periodTrendData',
            'kategoriDetails',
            'komentarTerbaru',
            'filterOptions',
            'filters',
        );
    }

    private function countUniqueRespondents($arsipList, $penggunaBySurvey): int
    {
        return $arsipList
            ->map(function ($arsip) use ($penggunaBySurvey) {
                // Arsip lama belum memiliki pengguna_lulusan_id, sehingga gunakan
                // relasi survey yang masih tersedia. Data identitas penyelia menjadi
                // fallback untuk arsip yang survey asalnya sudah dihapus.
                $penggunaId = $arsip->pengguna_lulusan_id ?? $penggunaBySurvey->get($arsip->survey_id);

                if ($penggunaId) {
                    return 'pengguna:' . $penggunaId;
                }

                $email = strtolower(trim((string) $arsip->penyelia_email));
                if ($email !== '') {
                    return 'email:' . $email;
                }

                $kontak = preg_replace('/\D+/', '', (string) $arsip->penyelia_kontak);
                if ($kontak !== '') {
                    return 'kontak:' . $kontak;
                }

                return 'arsip:' . $arsip->id;
            })
            ->unique()
            ->count();
    }

    private function getTotalLulusanDalamCakupan(array $periode, ?string $fakultas, array $programStudi): int
    {
        $query = Lulusan::query()
            ->join('survey', 'lulusan.id', '=', 'survey.lulusan_id')
            ->select('lulusan.id')
            ->distinct();

        if (!empty($periode)) {
            $query->join('periode', 'periode.id', '=', 'survey.periode_id')
                ->whereIn('periode.kode_periode', $periode);
        }
        if ($fakultas) {
            $query->where('lulusan.fakultas', $fakultas);
        }
        if (!empty($programStudi)) {
            $query->whereIn('lulusan.program_studi', $programStudi);
        }

        return $query->count('lulusan.id');
    }

    /** Jumlah perusahaan/responden unik yang masih mempunyai survei belum diisi dalam cakupan filter. */
    private function getTotalRespondenBelumMengisi(array $periode, ?string $fakultas, array $programStudi): int
    {
        $query = Survey::query()
            ->join('lulusan', 'lulusan.id', '=', 'survey.lulusan_id')
            ->where('survey.is_completed', false);

        if (!empty($periode)) {
            $query->join('periode', 'periode.id', '=', 'survey.periode_id')
                ->whereIn('periode.kode_periode', $periode);
        }
        if ($fakultas) {
            $query->where('lulusan.fakultas', $fakultas);
        }
        if (!empty($programStudi)) {
            $query->whereIn('lulusan.program_studi', $programStudi);
        }

        return $query->distinct()->count('survey.pengguna_lulusan_id');
    }

    private function getFilterOptions(): array
    {
        $fakultasMaster = Fakultas::with('programStudis')->orderBy('kode')->get();
        $fakultasProdi = $fakultasMaster->mapWithKeys(fn ($fakultas) => [
            $fakultas->kode => $fakultas->programStudis->pluck('nama')->sort()->values()->all(),
        ])->all();
        $arsipProdi = SurveyArsip::query()
            ->whereNotNull('lulusan_program_studi')
            ->get(['lulusan_fakultas', 'lulusan_program_studi']);
        foreach ($arsipProdi as $arsip) {
            if (filled($arsip->lulusan_fakultas) && filled($arsip->lulusan_program_studi)) {
                $fakultasProdi[$arsip->lulusan_fakultas] = array_values(array_unique([
                    ...($fakultasProdi[$arsip->lulusan_fakultas] ?? []),
                    $arsip->lulusan_program_studi,
                ]));
            }
        }
        $fakultasList = collect(array_keys($fakultasProdi))->sort()->values();
        $prodiList = collect($fakultasProdi)->flatten()->unique()->sort()->values();
        $fakultasLabels = $fakultasMaster->pluck('nama', 'kode')->all();

        $periodeList = SurveyArsip::whereNotNull('periode_kode')
            ->orderByDesc('periode_tanggal_mulai')
            ->get(['periode_kode', 'periode_nama'])
            ->unique('periode_kode')
            ->mapWithKeys(fn ($arsip) => [$arsip->periode_kode => $arsip->periode_nama ?: $arsip->periode_kode]);

        return compact('periodeList', 'fakultasList', 'prodiList', 'fakultasProdi', 'fakultasLabels');
    }

    private function getFakultasProdiOptions(): array
    {
        $options = Fakultas::with('programStudis')->orderBy('kode')->get()
            ->mapWithKeys(fn ($fakultas) => [
                $fakultas->kode => $fakultas->programStudis->pluck('nama')->sort()->values()->all(),
            ])
            ->all();

        SurveyArsip::query()
            ->whereNotNull('lulusan_fakultas')
            ->whereNotNull('lulusan_program_studi')
            ->get(['lulusan_fakultas', 'lulusan_program_studi'])
            ->each(function ($arsip) use (&$options): void {
                $options[$arsip->lulusan_fakultas] = array_values(array_unique([
                    ...($options[$arsip->lulusan_fakultas] ?? []),
                    $arsip->lulusan_program_studi,
                ]));
            });

        return $options;
    }
}
