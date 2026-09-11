<?php

namespace App\Services;

use App\Models\SurveyArsip;
use App\Models\lulusan;
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
            $arsipQuery->whereIn('tahun_instrumen', $periode);
        }

        if ($fakultas) {
            $arsipQuery->where('lulusan_fakultas', $fakultas);
        }

        if (!empty($programStudi)) {
            $arsipQuery->whereIn('lulusan_program_studi', $programStudi);
        }

        $arsipList = $arsipQuery->get();
        $totalSurvey = $arsipList->count();
        $totalLulusan = $this->getTotalLulusanDalamCakupan($periode, $fakultas, $programStudi);

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
            $totalSurvey,
            $totalLulusan,
        );
        $rataKeseluruhan = $skorKepuasan['skor_akhir'];

        $kategoriStats = collect($ratingByKategori)
            ->map(function ($nilai, $kategori) use ($totalSurvey, $totalLulusan) {
                $skor = $this->satisfactionScoreService->calculate($nilai, $totalSurvey, $totalLulusan);

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
            ->map(function ($nilai, $kategori) use ($totalSurvey, $totalLulusan) {
                $total = count($nilai);
                $countSangatBaik = count(array_filter($nilai, fn ($value) => $value == 4));
                $countBaik = count(array_filter($nilai, fn ($value) => $value == 3));
                $countKurang = count(array_filter($nilai, fn ($value) => $value == 2));
                $countSangatKurang = count(array_filter($nilai, fn ($value) => $value == 1));

                $skor = $this->satisfactionScoreService->calculate($nilai, $totalSurvey, $totalLulusan);

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
            ->map(function ($nilai, $kategori) use ($totalSurvey, $totalLulusan) {
                $total = count($nilai);
                $counts = [
                    'sb' => count(array_filter($nilai, fn ($value) => $value == 4)),
                    'b' => count(array_filter($nilai, fn ($value) => $value == 3)),
                    'k' => count(array_filter($nilai, fn ($value) => $value == 2)),
                    'sk' => count(array_filter($nilai, fn ($value) => $value == 1)),
                ];

                $skor = $this->satisfactionScoreService->calculate($nilai, $totalSurvey, $totalLulusan);

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
            'kategoriDetails',
            'komentarTerbaru',
            'filterOptions',
            'filters',
        );
    }

    private function getTotalLulusanDalamCakupan(array $periode, ?string $fakultas, array $programStudi): int
    {
        $query = lulusan::query()
            ->join('survey', 'lulusan.id', '=', 'survey.lulusan_id')
            ->select('lulusan.id')
            ->distinct();

        if (!empty($periode)) {
            $query->whereIn('survey.tahun', $periode);
        }
        if ($fakultas) {
            $query->where('lulusan.fakultas', $fakultas);
        }
        if (!empty($programStudi)) {
            $query->whereIn('lulusan.program_studi', $programStudi);
        }

        return $query->count('lulusan.id');
    }

    private function getFilterOptions(): array
    {
        $fakultasProdi = $this->getFakultasProdiOptions();
        $fakultasList = collect(array_keys($fakultasProdi));
        $prodiList = collect($fakultasProdi)->flatten()->values();
        $fakultasLabels = $this->getFakultasLabels();

        $periodeList = SurveyArsip::whereNotNull('tahun_instrumen')
            ->distinct()
            ->orderByDesc('tahun_instrumen')
            ->pluck('tahun_instrumen');

        return compact('periodeList', 'fakultasList', 'prodiList', 'fakultasProdi', 'fakultasLabels');
    }

    private function getFakultasProdiOptions(): array
    {
        $fromData = SurveyArsip::query()
            ->whereNotNull('lulusan_fakultas')
            ->whereNotNull('lulusan_program_studi')
            ->get(['lulusan_fakultas', 'lulusan_program_studi'])
            ->groupBy('lulusan_fakultas')
            ->map(fn ($items) => $items
                ->pluck('lulusan_program_studi')
                ->filter()
                ->unique()
                ->sort()
                ->values()
                ->all())
            ->sortKeys()
            ->all();

        return !empty($fromData) ? $fromData : [
            'FTI' => ['Manajemen Informatika', 'Sistem Informasi', 'Teknik Informatika'],
            'FEB' => ['Akuntansi', 'Ekonomi Pembangunan', 'Manajemen'],
            'FDIK' => ['Desain Komunikasi Visual', 'Ilmu Komunikasi', 'Jurnalistik'],
        ];
    }

    private function getFakultasLabels(): array
    {
        return [
            'FTI' => 'Fakultas Teknologi dan Informatika',
            'FEB' => 'Fakultas Ekonomi dan Bisnis',
            'FDIK' => 'Fakultas Desain dan Industri Kreatif',
        ];
    }
}
