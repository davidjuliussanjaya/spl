<?php

namespace App\Http\Controllers;

use App\Exports\ReportExport;
use App\Models\SurveyArsip;
use App\Models\Fakultas;
use App\Models\ProgramStudi;
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

        $prodiList = ProgramStudi::orderBy('nama')->get();

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
        $query = SurveyArsip::query()->latest('created_at');

        if ($request->filled('periode')) {
            $query->where('periode_kode', $request->periode);
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

        $periodeList = SurveyArsip::whereNotNull('periode_kode')
            ->orderByDesc('periode_tanggal_mulai')
            ->get(['periode_kode', 'periode_nama'])
            ->unique('periode_kode')
            ->mapWithKeys(fn ($arsip) => [$arsip->periode_kode => $arsip->periode_nama ?: $arsip->periode_kode]);
        $fakultasList = Fakultas::orderBy('kode')->get();
        $prodiList = ProgramStudi::orderBy('nama')->get();

        return view('admin.report.arsip', compact('arsip', 'periodeList', 'fakultasList', 'prodiList'));
    }

    public function arsipDetail($id)
    {
        $arsip = SurveyArsip::findOrFail($id);
        return view('admin.report.arsip-detail', compact('arsip'));
    }
}
