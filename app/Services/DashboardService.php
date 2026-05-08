<?php

namespace App\Services;

use App\Models\lulusan;
use App\Models\ResponJawaban;
use App\Models\Survey;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    public function getDashboardData(): array
    {
        // 1. Total Survey yang sudah diisi
        $totalSurvey = Survey::where('is_completed', true)->count();

        // 2. Total Lulusan yang dinilai (Unik)
        $totalLulusan = Survey::where('is_completed', true)->distinct('lulusan_id')->count('lulusan_id');

        // 3. Rata-rata Nilai Keseluruhan (Skala 1-4)
        $rataKeseluruhan = ResponJawaban::whereHas('soal', function($q) {
            $q->where('jenis_soal', 'rating');
        })
        ->join('jawaban', 'respon_jawaban.jawaban_id', '=', 'jawaban.id')
        ->avg('jawaban.nilai');

        // 4. Hitung Rata-rata per Kategori
        $kategoriStats = ResponJawaban::join('soal', 'respon_jawaban.soal_id', '=', 'soal.id')
            ->join('kategoris', 'soal.kategori_id', '=', 'kategoris.id')
            ->join('jawaban', 'respon_jawaban.jawaban_id', '=', 'jawaban.id')
            ->where('soal.jenis_soal', 'rating')
            ->select('kategoris.nama_kategori as kategori', DB::raw('AVG(jawaban.nilai) as rata_rata'))
            ->groupBy('kategoris.nama_kategori')
            ->orderBy('rata_rata', 'desc')
            ->get();

        // Siapkan data untuk Chart Horizontal (Label & Data)
        $chartLabels = $kategoriStats->pluck('kategori')->toArray();
        $chartData = $kategoriStats->pluck('rata_rata')->map(function ($val) {
            return round($val, 2);
        })->toArray();

        // Kategori Terbaik & Terlemah (Untuk Quick Insights)
        $kategoriTerbaik = $kategoriStats->first();
        $kategoriTerlemah = $kategoriStats->last();

        // 5. Daftar Lulusan Terbaru
        $daftarLulusan = lulusan::latest()->take(10)->get();

        // 6. Umpan Balik Terbaru (Essay)
        $komentarTerbaru = ResponJawaban::with(['survey.pengguna_lulusan', 'soal'])
            ->whereHas('soal', function($q) {
                $q->where('jenis_soal', 'essay');
            })
            ->whereNotNull('jawaban_text')
            ->latest()
            ->take(5)
            ->get();

        return compact(
            'totalSurvey', 'totalLulusan', 'rataKeseluruhan', 
            'kategoriTerbaik', 'kategoriTerlemah', 
            'chartLabels', 'chartData', 
            'daftarLulusan', 'komentarTerbaru'
        );
    }
}
