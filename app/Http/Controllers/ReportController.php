<?php

namespace App\Http\Controllers;

use App\Exports\ReportExport;
use App\Models\SurveyArsip;
use App\Support\DatabaseYearExpression;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $tahunList = DB::table('lulusan')
            ->whereNotNull('tahun_lulus')
            ->selectRaw(DatabaseYearExpression::fromDateColumn('tahun_lulus') . ' as tahun')
            ->distinct()
            ->orderByDesc('tahun')
            ->pluck('tahun');

        $prodiList = DB::table('lulusan')
            ->whereNotNull('program_studi')
            ->distinct()
            ->orderBy('program_studi')
            ->pluck('program_studi');

        $totalSurveySelesai = DB::table('survey')->where('is_completed', true)->count();

        $filters = [
            'tahun_dari' => $request->input('tahun_dari'),
            'tahun_sampai' => $request->input('tahun_sampai'),
            'program_studi' => collect(Arr::wrap($request->input('program_studi')))
                ->filter(fn ($programStudi) => filled($programStudi))
                ->values()
                ->all(),
        ];

        return view('admin.report.index', compact(
            'tahunList', 'prodiList',
            'totalSurveySelesai', 'filters'
        ));
    }

    public function download(Request $request)
    {
        $filters = [
            'tahun_dari' => $request->input('tahun_dari'),
            'tahun_sampai' => $request->input('tahun_sampai'),
            'program_studi' => collect(Arr::wrap($request->input('program_studi')))
                ->filter(fn ($programStudi) => filled($programStudi))
                ->values()
                ->all(),
        ];
        return (new ReportExport($filters))->download();
    }

    public function arsip(Request $request)
    {
        $query = SurveyArsip::query()->orderByDesc('submitted_at');

        if ($request->filled('tahun')) {
            $query->where('tahun_instrumen', $request->tahun);
        }
        if ($request->filled('fakultas')) {
            $query->where('lulusan_fakultas', $request->fakultas);
        }
        if ($request->filled('program_studi')) {
            $query->where('lulusan_program_studi', $request->program_studi);
        }
        if ($request->filled('cari')) {
            $cari = $request->cari;
            $query->where(function ($q) use ($cari) {
                $q->where('lulusan_nama', 'like', "%{$cari}%")
                  ->orWhere('lulusan_nim', 'like', "%{$cari}%")
                  ->orWhere('perusahaan_nama', 'like', "%{$cari}%")
                  ->orWhere('penyelia_nama', 'like', "%{$cari}%");
            });
        }

        $arsip = $query->paginate(10)->withQueryString();

        $tahunList   = SurveyArsip::whereNotNull('tahun_instrumen')->distinct()->orderByDesc('tahun_instrumen')->pluck('tahun_instrumen');
        $fakultasList = SurveyArsip::whereNotNull('lulusan_fakultas')->distinct()->orderBy('lulusan_fakultas')->pluck('lulusan_fakultas');
        $prodiList   = SurveyArsip::whereNotNull('lulusan_program_studi')->distinct()->orderBy('lulusan_program_studi')->pluck('lulusan_program_studi');

        return view('admin.report.arsip', compact('arsip', 'tahunList', 'fakultasList', 'prodiList'));
    }

    public function arsipDetail($id)
    {
        $arsip = SurveyArsip::findOrFail($id);
        return view('admin.report.arsip-detail', compact('arsip'));
    }
}
