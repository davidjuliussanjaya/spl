<?php

namespace App\Http\Controllers;

use App\Exports\ReportExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $tahunList = DB::table('lulusan')
            ->whereNotNull('tahun_lulus')
            ->selectRaw('EXTRACT(YEAR FROM tahun_lulus) as tahun')
            ->distinct()
            ->orderByDesc('tahun')
            ->pluck('tahun');

        $fakultasList = DB::table('lulusan')
            ->whereNotNull('fakultas')
            ->distinct()
            ->orderBy('fakultas')
            ->pluck('fakultas');

        $prodiList = DB::table('lulusan')
            ->whereNotNull('program_studi')
            ->distinct()
            ->orderBy('program_studi')
            ->pluck('program_studi');

        $totalSurveySelesai = DB::table('survey')->where('is_completed', true)->count();

        $filters = $request->only(['tahun', 'fakultas', 'program_studi']);

        return view('admin.report.index', compact(
            'tahunList', 'fakultasList', 'prodiList',
            'totalSurveySelesai', 'filters'
        ));
    }

    public function download(Request $request)
    {
        $filters = $request->only(['tahun', 'fakultas', 'program_studi']);
        return (new ReportExport($filters))->download();
    }
}
