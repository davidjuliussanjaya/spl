<?php

namespace App\Services;

use App\Models\lulusan;
use App\Models\penggunalulusan;
use App\Models\ResponJawaban;
use App\Models\Survey;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    public function getDashboardData(array $filters = []): array
    {
        $tahun           = $filters['tahun'] ?? null;
        $fakultas        = $filters['fakultas'] ?? null;
        $programStudi    = $filters['program_studi'] ?? null;
        $jenisPerusahaan = $filters['jenis_perusahaan'] ?? null;

        // ---------------------------------------------------------------
        // 1. Total Survey selesai
        // ---------------------------------------------------------------
        $totalSurvey = $this->baseSurveyQuery($tahun, $fakultas, $programStudi, $jenisPerusahaan)
            ->count();

        // ---------------------------------------------------------------
        // 2. Total Lulusan unik yang dinilai
        // ---------------------------------------------------------------
        $totalLulusan = $this->baseSurveyQuery($tahun, $fakultas, $programStudi, $jenisPerusahaan)
            ->distinct()
            ->count('survey.lulusan_id');

        // ---------------------------------------------------------------
        // 3. Rata-rata nilai keseluruhan
        // ---------------------------------------------------------------
        $rataKeseluruhan = $this->baseResponQuery($tahun, $fakultas, $programStudi, $jenisPerusahaan)
            ->avg('jawaban.nilai');

        // ---------------------------------------------------------------
        // 4. Rata-rata per Kategori
        // ---------------------------------------------------------------
        $kategoriStats = $this->baseResponQuery($tahun, $fakultas, $programStudi, $jenisPerusahaan)
            ->join('kategoris', 'soal.kategori_id', '=', 'kategoris.id')
            ->select('kategoris.nama_kategori as kategori', DB::raw('AVG(jawaban.nilai) as rata_rata'))
            ->groupBy('kategoris.nama_kategori')
            ->orderBy('rata_rata', 'desc')
            ->get();

        $chartLabels      = $kategoriStats->pluck('kategori')->toArray();
        $chartData        = $kategoriStats->pluck('rata_rata')->map(fn($v) => round($v, 2))->toArray();
        $kategoriTerbaik  = $kategoriStats->first();
        $kategoriTerlemah = $kategoriStats->last();

        // ---------------------------------------------------------------
        // 4b. Distribusi kepuasan per kategori (% tiap rating)
        // ---------------------------------------------------------------
        $kepuasanRaw = $this->baseResponQuery($tahun, $fakultas, $programStudi, $jenisPerusahaan)
            ->join('kategoris', 'soal.kategori_id', '=', 'kategoris.id')
            ->select(
                'kategoris.nama_kategori as kategori',
                DB::raw('COUNT(CASE WHEN jawaban.nilai = 4 THEN 1 END) as cnt_sb'),
                DB::raw('COUNT(CASE WHEN jawaban.nilai = 3 THEN 1 END) as cnt_b'),
                DB::raw('COUNT(CASE WHEN jawaban.nilai = 2 THEN 1 END) as cnt_k'),
                DB::raw('COUNT(CASE WHEN jawaban.nilai = 1 THEN 1 END) as cnt_sk'),
                DB::raw('COUNT(*) as total')
            )
            ->groupBy('kategoris.nama_kategori')
            ->orderBy('kategoris.nama_kategori')
            ->get();

        $kepuasanPerKategori = $kepuasanRaw->map(function ($row) {
            $total = $row->total ?: 1;
            return [
                'kategori' => $row->kategori,
                'pct_sb'   => round($row->cnt_sb / $total * 100, 1),
                'pct_b'    => round($row->cnt_b  / $total * 100, 1),
                'pct_k'    => round($row->cnt_k  / $total * 100, 1),
                'pct_sk'   => round($row->cnt_sk / $total * 100, 1),
            ];
        });

        $countKat = $kepuasanPerKategori->count();
        $sumSB = $kepuasanPerKategori->sum('pct_sb');
        $sumB  = $kepuasanPerKategori->sum('pct_b');
        $sumK  = $kepuasanPerKategori->sum('pct_k');
        $sumSK = $kepuasanPerKategori->sum('pct_sk');

        $kepuasanRingkasan = [
            'total' => [
                'sb' => round($sumSB, 1), 'b' => round($sumB, 1),
                'k'  => round($sumK,  1), 'sk' => round($sumSK, 1),
            ],
            'rata'  => $countKat > 0 ? [
                'sb' => round($sumSB / $countKat, 1), 'b' => round($sumB / $countKat, 1),
                'k'  => round($sumK  / $countKat, 1), 'sk' => round($sumSK / $countKat, 1),
            ] : ['sb' => 0, 'b' => 0, 'k' => 0, 'sk' => 0],
        ];

        // ---------------------------------------------------------------
        // 5. Daftar Lulusan terbaru
        // ---------------------------------------------------------------
        $lulusanQuery = lulusan::query();
        if ($fakultas)     $lulusanQuery->where('fakultas', $fakultas);
        if ($programStudi) $lulusanQuery->where('program_studi', $programStudi);
        $daftarLulusan = $lulusanQuery->latest()->take(10)->get();

        // ---------------------------------------------------------------
        // 6. Umpan Balik Terbaru (Essay)
        // ---------------------------------------------------------------
        $komentarTerbaru = $this->baseSurveyQuery($tahun, $fakultas, $programStudi, $jenisPerusahaan)
            ->join('respon_jawaban', 'survey.id', '=', 'respon_jawaban.survey_id')
            ->join('soal as s_essay', 'respon_jawaban.soal_id', '=', 's_essay.id')
            ->join('pengguna_lulusan', 'survey.pengguna_lulusan_id', '=', 'pengguna_lulusan.id')
            ->where('s_essay.jenis_soal', 'essay')
            ->whereNotNull('respon_jawaban.jawaban_text')
            ->select(
                'respon_jawaban.jawaban_text',
                'respon_jawaban.responden',
                's_essay.soal as soal_teks',
                'pengguna_lulusan.nama_perusahaan'
            )
            ->orderBy('respon_jawaban.created_at', 'desc')
            ->limit(5)
            ->get();

        // ---------------------------------------------------------------
        // 7. Opsi filter untuk dropdown
        // ---------------------------------------------------------------
        $filterOptions = $this->getFilterOptions();

        return compact(
            'totalSurvey', 'totalLulusan', 'rataKeseluruhan',
            'kategoriTerbaik', 'kategoriTerlemah',
            'chartLabels', 'chartData',
            'kepuasanPerKategori', 'kepuasanRingkasan',
            'daftarLulusan', 'komentarTerbaru',
            'filterOptions', 'filters'
        );
    }

    // Base query untuk survey selesai + semua filter
    private function baseSurveyQuery($tahun, $fakultas, $programStudi, $jenisPerusahaan)
    {
        $q = DB::table('survey')->where('survey.is_completed', true);

        if ($tahun) {
            $q->whereRaw("EXTRACT(YEAR FROM survey.updated_at) = ?", [$tahun]);
        }
        if ($fakultas || $programStudi) {
            $q->join('lulusan', 'survey.lulusan_id', '=', 'lulusan.id');
            if ($fakultas)     $q->where('lulusan.fakultas', $fakultas);
            if ($programStudi) $q->where('lulusan.program_studi', $programStudi);
        }
        if ($jenisPerusahaan) {
            $q->join('pengguna_lulusan as pl', 'survey.pengguna_lulusan_id', '=', 'pl.id')
              ->where('pl.jenis_perusahaan', $jenisPerusahaan);
        }

        return $q;
    }

    // Base query untuk respon jawaban rating + semua filter
    private function baseResponQuery($tahun, $fakultas, $programStudi, $jenisPerusahaan)
    {
        $q = DB::table('respon_jawaban')
            ->join('survey', 'respon_jawaban.survey_id', '=', 'survey.id')
            ->join('soal', 'respon_jawaban.soal_id', '=', 'soal.id')
            ->join('jawaban', 'respon_jawaban.jawaban_id', '=', 'jawaban.id')
            ->where('soal.jenis_soal', 'rating')
            ->where('survey.is_completed', true);

        if ($tahun) {
            $q->whereRaw("EXTRACT(YEAR FROM survey.updated_at) = ?", [$tahun]);
        }
        if ($fakultas || $programStudi) {
            $q->join('lulusan', 'survey.lulusan_id', '=', 'lulusan.id');
            if ($fakultas)     $q->where('lulusan.fakultas', $fakultas);
            if ($programStudi) $q->where('lulusan.program_studi', $programStudi);
        }
        if ($jenisPerusahaan) {
            $q->join('pengguna_lulusan as pl', 'survey.pengguna_lulusan_id', '=', 'pl.id')
              ->where('pl.jenis_perusahaan', $jenisPerusahaan);
        }

        return $q;
    }

    private function getFilterOptions(): array
    {
        $tahunList = DB::table('survey')
            ->where('is_completed', true)
            ->selectRaw('EXTRACT(YEAR FROM updated_at) as tahun')
            ->distinct()
            ->orderByDesc('tahun')
            ->pluck('tahun');

        $fakultasList = lulusan::whereNotNull('fakultas')
            ->distinct()
            ->orderBy('fakultas')
            ->pluck('fakultas');

        $prodiList = lulusan::whereNotNull('program_studi')
            ->distinct()
            ->orderBy('program_studi')
            ->pluck('program_studi');

        $jenisPerusahaanList = penggunalulusan::whereNotNull('jenis_perusahaan')
            ->distinct()
            ->orderBy('jenis_perusahaan')
            ->pluck('jenis_perusahaan');

        return compact('tahunList', 'fakultasList', 'prodiList', 'jenisPerusahaanList');
    }
}
