@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<style>
    /* ── Tokens ── */
    :root {
        --blue-50:  #eff6ff;
        --blue-100: #dbeafe;
        --blue-200: #bfdbfe;
        --blue-500: #3b82f6;
        --blue-600: #2563eb;
        --blue-700: #1d4ed8;
        --blue-900: #1e3a8a;
        --slate-50:  #f8fafc;
        --slate-100: #f1f5f9;
        --slate-200: #e2e8f0;
        --slate-400: #94a3b8;
        --slate-500: #64748b;
        --slate-700: #334155;
        --slate-900: #0f172a;
        --radius: 12px;
        --shadow: 0 1px 3px rgba(0,0,0,.06), 0 1px 2px rgba(0,0,0,.04);
        --shadow-md: 0 4px 16px rgba(0,0,0,.07);
    }

    /* ── Layout ── */
    .db-wrap { display: flex; flex-direction: column; gap: 1.25rem; }

    /* ── Page Header ── */
    .db-header { display: flex; justify-content: space-between; align-items: flex-end; flex-wrap: wrap; gap: .75rem; }
    .db-header h3 { font-size: 1.25rem; font-weight: 700; color: var(--slate-900); margin: 0; }
    .db-header p  { font-size: .82rem; color: var(--slate-400); margin: .2rem 0 0; }
    .btn-report {
        font-size: .8rem; font-weight: 600; padding: .45rem 1.1rem;
        background: var(--blue-600); color: #fff; border: none;
        border-radius: 8px; display: inline-flex; align-items: center; gap: .35rem;
        transition: background .15s;
        text-decoration: none;
    }
    .btn-report:hover { background: var(--blue-700); color: #fff; }

    /* ── Filter Bar ── */
    .filter-bar {
        background: #fff; border: 1px solid var(--slate-200);
        border-radius: var(--radius); padding: .85rem 1.1rem;
        box-shadow: var(--shadow);
    }
    .filter-bar .form-label { font-size: .72rem; font-weight: 600; color: var(--slate-400); text-transform: uppercase; letter-spacing: .5px; margin-bottom: .3rem; }
    .filter-bar .form-select { font-size: .83rem; border-color: var(--slate-200); border-radius: 8px; color: var(--slate-700); }
    .filter-bar .form-select:focus { border-color: var(--blue-500); box-shadow: 0 0 0 3px rgba(59,130,246,.15); }
    .btn-apply  { font-size: .8rem; font-weight: 600; padding: .42rem 1rem; background: var(--blue-600); color: #fff; border: none; border-radius: 8px; }
    .btn-apply:hover { background: var(--blue-700); color: #fff; }
    .btn-reset  { font-size: .8rem; padding: .42rem .85rem; border-radius: 8px; }
    .chip {
        display: inline-flex; align-items: center; gap: .3rem;
        font-size: .74rem; font-weight: 500; padding: .22rem .65rem;
        border-radius: 50px; background: var(--blue-50);
        color: var(--blue-700); border: 1px solid var(--blue-200);
    }

    /* ── Stat Cards ── */
    .stat-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem; }
    @media(max-width:1199px) { .stat-grid { grid-template-columns: repeat(2, 1fr); } }
    @media(max-width:575px)  { .stat-grid { grid-template-columns: repeat(2, 1fr); gap: .6rem; } }

    .stat-card {
        background: #fff; border: 1px solid var(--slate-200);
        border-radius: var(--radius); padding: 1.2rem 1.25rem;
        box-shadow: var(--shadow); position: relative; overflow: hidden;
        transition: box-shadow .2s, transform .2s;
    }
    .stat-card:hover { box-shadow: var(--shadow-md); transform: translateY(-2px); }
    .stat-card::before {
        content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px;
        background: var(--blue-500);
    }
    .stat-card.green::before  { background: #22c55e; }
    .stat-card.amber::before  { background: #f59e0b; }
    .stat-card.indigo::before { background: #6366f1; }

    .stat-top { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: .85rem; }
    .stat-label { font-size: .72rem; font-weight: 600; text-transform: uppercase; letter-spacing: .6px; color: var(--slate-400); }
    .stat-icon-wrap {
        width: 36px; height: 36px; border-radius: 9px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1rem; background: var(--blue-50); color: var(--blue-600);
        flex-shrink: 0;
    }
    .stat-card.green  .stat-icon-wrap { background: #f0fdf4; color: #16a34a; }
    .stat-card.amber  .stat-icon-wrap { background: #fffbeb; color: #d97706; }
    .stat-card.indigo .stat-icon-wrap { background: #eef2ff; color: #4f46e5; }

    .stat-value { font-size: 1.8rem; font-weight: 800; color: var(--slate-900); line-height: 1; }
    .stat-unit  { font-size: .78rem; font-weight: 400; color: var(--slate-400); margin-left: .25rem; }
    .stat-bar-track { height: 4px; background: var(--slate-100); border-radius: 99px; margin-top: .7rem; overflow: hidden; }
    .stat-bar-fill  { height: 100%; border-radius: 99px; background: var(--blue-500); }
    .stat-card.green  .stat-bar-fill { background: #22c55e; }
    .stat-card.amber  .stat-bar-fill { background: #f59e0b; }
    .stat-sub { font-size: .73rem; color: var(--slate-400); margin-top: .4rem; }

    /* ── Panel (generic card) ── */
    .panel {
        background: #fff; border: 1px solid var(--slate-200);
        border-radius: var(--radius); box-shadow: var(--shadow); overflow: hidden;
    }
    .panel-header {
        display: flex; justify-content: space-between; align-items: center;
        padding: .9rem 1.25rem; border-bottom: 1px solid var(--slate-100);
    }
    .panel-title { font-size: .88rem; font-weight: 700; color: var(--slate-700); margin: 0; }
    .panel-link  { font-size: .75rem; color: var(--blue-600); font-weight: 600; text-decoration: none; }
    .panel-link:hover { color: var(--blue-700); }
    .panel-body  { padding: 1.1rem 1.25rem; }

    /* ── Two-col row ── */
    .two-col { display: grid; grid-template-columns: 1fr 380px; gap: 1rem; }
    @media(max-width:1199px) { .two-col { grid-template-columns: 1fr; } }
    .two-col-eq { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
    @media(max-width:991px)  { .two-col-eq { grid-template-columns: 1fr; } }

    /* ── Fokus cards ── */
    .insight-item { display: flex; gap: .85rem; align-items: flex-start; padding: .9rem; border-radius: 10px; }
    .insight-item + .insight-item { margin-top: .6rem; }
    .insight-item.up   { background: #f0fdf4; }
    .insight-item.down { background: #fff7ed; }
    .insight-ico {
        width: 34px; height: 34px; border-radius: 9px; flex-shrink: 0;
        display: flex; align-items: center; justify-content: center; font-size: .95rem;
    }
    .insight-item.up   .insight-ico { background: #dcfce7; color: #16a34a; }
    .insight-item.down .insight-ico { background: #ffedd5; color: #ea580c; }
    .insight-title  { font-size: .78rem; font-weight: 700; margin-bottom: .2rem; }
    .insight-item.up   .insight-title { color: #15803d; }
    .insight-item.down .insight-title { color: #c2410c; }
    .insight-body  { font-size: .8rem; color: var(--slate-700); line-height: 1.5; }
    .insight-body strong { color: var(--slate-900); }

    .gap-row { display: flex; justify-content: space-between; font-size: .75rem; margin-bottom: .3rem; }
    .gap-row .g-up   { color: #16a34a; font-weight: 700; }
    .gap-row .g-mid  { color: var(--slate-400); }
    .gap-row .g-down { color: #ea580c; font-weight: 700; }
    .gap-track { height: 5px; background: var(--slate-100); border-radius: 99px; overflow: hidden; }
    .gap-fill  { height: 100%; background: linear-gradient(90deg, #22c55e, #f59e0b); border-radius: 99px; }

    /* ── Lulusan table ── */
    .tbl-lulusan { width: 100%; border-collapse: collapse; }
    .tbl-lulusan thead th {
        font-size: .72rem; text-transform: uppercase; letter-spacing: .5px;
        color: var(--slate-400); font-weight: 600; padding: .6rem 1rem;
        background: var(--slate-50); border-bottom: 1px solid var(--slate-100);
    }
    .tbl-lulusan tbody td { padding: .72rem 1rem; border-bottom: 1px solid var(--slate-100); font-size: .83rem; vertical-align: middle; }
    .tbl-lulusan tbody tr:last-child td { border-bottom: none; }
    .tbl-lulusan tbody tr:hover td { background: var(--slate-50); }
    .av {
        width: 32px; height: 32px; border-radius: 8px; flex-shrink: 0;
        background: var(--blue-600); color: #fff;
        font-size: .71rem; font-weight: 700;
        display: flex; align-items: center; justify-content: center;
    }
    .av-name  { font-size: .84rem; font-weight: 600; color: var(--slate-900); line-height: 1.2; }
    .av-nim   { font-size: .73rem; color: var(--slate-400); }
    .badge-pill {
        display: inline-block; font-size: .7rem; font-weight: 600;
        padding: .2rem .6rem; border-radius: 99px; white-space: nowrap;
    }
    .badge-active   { background: #dcfce7; color: #15803d; }
    .badge-inactive { background: #fef9c3; color: #a16207; }

    /* ── Feedback list ── */
    .fb-item { padding: .95rem 1.25rem; border-bottom: 1px solid var(--slate-100); }
    .fb-item:last-child { border-bottom: none; }
    .fb-quote {
        font-size: .83rem; color: var(--slate-700); line-height: 1.6;
        font-style: italic; padding: .55rem .85rem;
        background: var(--blue-50); border-left: 3px solid var(--blue-500);
        border-radius: 0 8px 8px 0; margin-bottom: .5rem;
    }
    .fb-meta  { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: .35rem; }
    .fb-who   { font-size: .75rem; }
    .fb-who strong { color: var(--slate-700); font-weight: 600; }
    .fb-who span   { color: var(--slate-400); }
    .fb-tag {
        font-size: .7rem; font-weight: 500; padding: .18rem .6rem;
        background: var(--blue-50); color: var(--blue-700);
        border: 1px solid var(--blue-200); border-radius: 99px;
    }

    .empty-state { text-align: center; padding: 2.5rem 1rem; color: var(--slate-400); font-size: .85rem; }
    .empty-state i { display: block; font-size: 1.75rem; margin-bottom: .5rem; opacity: .4; }

    /* ── Tabel Kepuasan ── */
    .tbl-kepuasan { width: 100%; border-collapse: collapse; font-size: .83rem; }
    .tbl-kepuasan thead th {
        padding: .65rem 1.1rem; font-size: .72rem; font-weight: 700;
        text-align: center; white-space: nowrap; letter-spacing: .3px;
        border-bottom: 2px solid transparent;
    }
    .tbl-kepuasan thead .th-name {
        text-align: left; background: var(--slate-50);
        color: var(--slate-500); border-bottom-color: var(--slate-200);
        width: 26%;
    }
    .tbl-kepuasan thead .th-sb  { background: #1e7e34; color: #fff; border-bottom-color: #145226; }
    .tbl-kepuasan thead .th-b   { background: #0b5ed7; color: #fff; border-bottom-color: #0849a8; }
    .tbl-kepuasan thead .th-k   { background: #d4690c; color: #fff; border-bottom-color: #a45009; }
    .tbl-kepuasan thead .th-sk  { background: #bb2d3b; color: #fff; border-bottom-color: #902130; }
    .tbl-kepuasan thead .th-bar {
        background: var(--slate-50); color: var(--slate-500);
        border-bottom-color: var(--slate-200); min-width: 130px;
    }

    .tbl-kepuasan tbody td {
        padding: .6rem 1.1rem; border-bottom: 1px solid var(--slate-100);
        text-align: center; vertical-align: middle;
    }
    .tbl-kepuasan tbody td:first-child { text-align: left; font-weight: 500; color: var(--slate-700); }
    .tbl-kepuasan tbody tr:nth-child(even) td { background: var(--slate-50); }
    .tbl-kepuasan tbody tr:hover td { background: #eff6ff; transition: background .1s; }

    .pct-sb { color: #15803d; font-weight: 700; }
    .pct-b  { color: #1d4ed8; font-weight: 700; }
    .pct-k  { color: #c2410c; font-weight: 700; }
    .pct-sk { color: #b91c1c; font-weight: 700; }

    /* Stacked mini-bar */
    .dist-bar { display: flex; height: 9px; border-radius: 99px; overflow: hidden; gap: 1px; min-width: 110px; }
    .dist-bar .seg { border-radius: 0; transition: width .4s ease; }
    .dist-bar .seg-sb { background: #22c55e; }
    .dist-bar .seg-b  { background: #3b82f6; }
    .dist-bar .seg-k  { background: #f97316; }
    .dist-bar .seg-sk { background: #ef4444; }

    /* Footer total / rata-rata */
    .tbl-kepuasan tfoot td {
        padding: .68rem 1.1rem; font-weight: 700; text-align: center;
        font-size: .82rem;
    }
    .tbl-kepuasan tfoot td:first-child { text-align: left; }
    .tbl-kepuasan tfoot .row-total td {
        background: #e2e8f0; color: var(--slate-700);
        border-top: 2px solid var(--slate-300);
    }
    .tbl-kepuasan tfoot .row-rata td {
        background: #cbd5e1; color: var(--slate-900);
        border-top: 1px solid var(--slate-400);
    }

    /* Legend pill */
    .leg { display: inline-flex; align-items: center; gap: .3rem; font-size: .72rem; color: var(--slate-500); }
    .leg-dot { width: 9px; height: 9px; border-radius: 50%; flex-shrink: 0; }
</style>
<div class="db-wrap">

    {{-- Header --}}
    <div class="db-header">
        <div>
            <h3>Dashboard Evaluasi Lulusan</h3>
            <p>Ringkasan performa dan kualitas lulusan Universitas Dinamika di dunia kerja.</p>
        </div>
        <a href="{{ route('report') }}" class="btn-report">
            <i class="bi bi-printer"></i> Cetak Laporan
        </a>
    </div>

    {{-- Filter Bar --}}
    <div class="filter-bar">
        <form method="GET" action="{{ route('dashboard') }}" id="filterForm">
            <div class="row g-2 align-items-end">
                <div class="col-12 col-md-auto d-flex align-items-center pe-3">
                    <span style="font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.6px;color:var(--slate-400);">
                        <i class="bi bi-sliders me-1" style="color:var(--blue-500);"></i>Filter
                    </span>
                </div>
                <div class="col-6 col-md">
                    <label class="form-label">Tahun Survey</label>
                    <select name="tahun" class="form-select form-select-sm filter-select">
                        <option value="">Semua Tahun</option>
                        @foreach($filterOptions['tahunList'] as $t)
                            <option value="{{ $t }}" {{ ($filters['tahun'] ?? '') == $t ? 'selected' : '' }}>{{ $t }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-md">
                    <label class="form-label">Fakultas</label>
                    <select name="fakultas" class="form-select form-select-sm filter-select">
                        <option value="">Semua Fakultas</option>
                        @foreach($filterOptions['fakultasList'] as $f)
                            <option value="{{ $f }}" {{ ($filters['fakultas'] ?? '') == $f ? 'selected' : '' }}>{{ $f }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-md">
                    <label class="form-label">Program Studi</label>
                    <select name="program_studi" class="form-select form-select-sm filter-select">
                        <option value="">Semua Prodi</option>
                        @foreach($filterOptions['prodiList'] as $p)
                            <option value="{{ $p }}" {{ ($filters['program_studi'] ?? '') == $p ? 'selected' : '' }}>{{ $p }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-md">
                    <label class="form-label">Jenis Perusahaan</label>
                    <select name="jenis_perusahaan" class="form-select form-select-sm filter-select">
                        <option value="">Semua Jenis</option>
                        @foreach($filterOptions['jenisPerusahaanList'] as $jenis)
                            <option value="{{ $jenis }}" {{ ($filters['jenis_perusahaan'] ?? '') == $jenis ? 'selected' : '' }}>{{ $jenis }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-auto d-flex gap-2">
                    <button type="submit" class="btn btn-apply">
                        <i class="bi bi-search me-1"></i> Terapkan
                    </button>
                    @if(array_filter($filters))
                        <a href="{{ route('dashboard') }}" class="btn btn-reset btn-outline-secondary">
                            <i class="bi bi-x-lg"></i>
                        </a>
                    @endif
                </div>
            </div>

            @if(array_filter($filters))
                <div class="d-flex flex-wrap gap-2 align-items-center mt-2 pt-2 border-top">
                    <span style="font-size:.72rem;color:var(--slate-400);">Aktif:</span>
                    @if(!empty($filters['tahun']))        <span class="chip"><i class="bi bi-calendar3"></i> {{ $filters['tahun'] }}</span>@endif
                    @if(!empty($filters['fakultas']))     <span class="chip"><i class="bi bi-building"></i> {{ $filters['fakultas'] }}</span>@endif
                    @if(!empty($filters['program_studi']))<span class="chip"><i class="bi bi-book"></i> {{ $filters['program_studi'] }}</span>@endif
                    @if(!empty($filters['jenis_perusahaan']))<span class="chip"><i class="bi bi-briefcase"></i> {{ $filters['jenis_perusahaan'] }}</span>@endif
                </div>
            @endif
        </form>
    </div>

    {{-- Stat Cards --}}
    <div class="stat-grid">

        <div class="stat-card">
            <div class="stat-top">
                <span class="stat-label">Total Responden</span>
                <div class="stat-icon-wrap"><i class="bi bi-buildings-fill"></i></div>
            </div>
            <div>
                <span class="stat-value">{{ $totalSurvey ?? 0 }}</span>
                <span class="stat-unit">instansi</span>
            </div>
        </div>

        <div class="stat-card indigo">
            <div class="stat-top">
                <span class="stat-label">Lulusan Dinilai</span>
                <div class="stat-icon-wrap"><i class="bi bi-mortarboard-fill"></i></div>
            </div>
            <div>
                <span class="stat-value">{{ $totalLulusan ?? 0 }}</span>
                <span class="stat-unit">orang</span>
            </div>
        </div>

        @php $pct = min(100, round((($rataKeseluruhan ?? 0) / 4) * 100)); @endphp
        <div class="stat-card green">
            <div class="stat-top">
                <span class="stat-label">Indeks Kepuasan</span>
                <div class="stat-icon-wrap"><i class="bi bi-star-fill"></i></div>
            </div>
            <div>
                <span class="stat-value" style="color:#16a34a;">{{ number_format($rataKeseluruhan ?? 0, 2) }}</span>
                <span class="stat-unit">/ 4.00</span>
            </div>
            <div class="stat-bar-track">
                <div class="stat-bar-fill" style="--w:{{ $pct }}%;width:var(--w);"></div>
            </div>
            <div class="stat-sub">{{ $pct }}% dari skor maksimal</div>
        </div>

        <div class="stat-card amber">
            <div class="stat-top">
                <span class="stat-label">Kategori Terbaik</span>
                <div class="stat-icon-wrap"><i class="bi bi-trophy-fill"></i></div>
            </div>
            <div class="fw-bold" style="font-size:.92rem;color:var(--slate-900);line-height:1.3;margin-bottom:.25rem;">
                {{ $kategoriTerbaik->kategori ?? '-' }}
            </div>
            <div class="stat-sub" style="color:#d97706;">
                Skor {{ number_format($kategoriTerbaik->rata_rata ?? 0, 2) }} / 4.00
            </div>
        </div>

    </div>

    {{-- Chart + Fokus --}}
    <div class="two-col">

        <div class="panel">
            <div class="panel-header">
                <h6 class="panel-title">Rata-Rata Penilaian per Kategori</h6>
            </div>
            <div class="panel-body" style="padding-top:.5rem;">
                <div id="chart-kinerja"></div>
            </div>
        </div>

        <div class="panel">
            <div class="panel-header">
                <h6 class="panel-title">Ringkasan Kinerja</h6>
            </div>
            <div class="panel-body">

                <div class="insight-item up">
                    <div class="insight-ico"><i class="bi bi-arrow-up-right"></i></div>
                    <div>
                        <div class="insight-title">Kekuatan Lulusan</div>
                        <div class="insight-body">
                            Unggul pada <strong>{{ $kategoriTerbaik->kategori ?? '-' }}</strong>
                            dengan skor <strong>{{ number_format($kategoriTerbaik->rata_rata ?? 0, 2) }}</strong> / 4.00
                        </div>
                    </div>
                </div>

                <div class="insight-item down">
                    <div class="insight-ico"><i class="bi bi-arrow-down-right"></i></div>
                    <div>
                        <div class="insight-title">Area Peningkatan</div>
                        <div class="insight-body">
                            Aspek <strong>{{ $kategoriTerlemah->kategori ?? '-' }}</strong>
                            perlu perhatian lebih, skor <strong>{{ number_format($kategoriTerlemah->rata_rata ?? 0, 2) }}</strong> / 4.00
                        </div>
                    </div>
                </div>

                @if($kategoriTerbaik && $kategoriTerlemah)
                @php
                    $gap    = ($kategoriTerbaik->rata_rata ?? 0) - ($kategoriTerlemah->rata_rata ?? 0);
                    $gapPct = min(100, round(($gap / 4) * 100));
                @endphp
                <div class="mt-3 pt-3" style="border-top:1px solid var(--slate-100);">
                    <div style="font-size:.72rem;font-weight:600;color:var(--slate-400);text-transform:uppercase;letter-spacing:.5px;margin-bottom:.5rem;">
                        Selisih Skor
                    </div>
                    <div class="gap-row">
                        <span class="g-up">{{ number_format($kategoriTerbaik->rata_rata ?? 0, 2) }}</span>
                        <span class="g-mid">gap {{ number_format($gap, 2) }}</span>
                        <span class="g-down">{{ number_format($kategoriTerlemah->rata_rata ?? 0, 2) }}</span>
                    </div>
                    <div class="gap-track">
                        <div class="gap-fill" style="--w:{{ $gapPct }}%;width:var(--w);"></div>
                    </div>
                </div>
                @endif

            </div>
        </div>

    </div>

    {{-- Tabel Tingkat Kepuasan Pengguna --}}
    <div class="panel">
        <div class="panel-header">
            <div>
                <h6 class="panel-title">Tingkat Kepuasan Pengguna</h6>
                <p style="font-size:.74rem;color:var(--slate-400);margin:.1rem 0 0;">Distribusi penilaian responden per kategori kompetensi lulusan</p>
            </div>
            <div class="d-flex flex-wrap gap-3 align-items-center">
                <span class="leg"><span class="leg-dot" style="background:#22c55e;"></span>Sangat Baik</span>
                <span class="leg"><span class="leg-dot" style="background:#3b82f6;"></span>Baik</span>
                <span class="leg"><span class="leg-dot" style="background:#f97316;"></span>Kurang</span>
                <span class="leg"><span class="leg-dot" style="background:#ef4444;"></span>Sangat Kurang</span>
            </div>
        </div>

        @if($kepuasanPerKategori->isEmpty())
            <div class="empty-state"><i class="bi bi-bar-chart"></i>Belum ada data penilaian.</div>
        @else
        <div style="overflow-x:auto;">
            <table class="tbl-kepuasan">
                <thead>
                    <tr>
                        <th class="th-name">Jenis Kemampuan</th>
                        <th class="th-sb">Sangat Baik (4)</th>
                        <th class="th-b">Baik (3)</th>
                        <th class="th-k">Kurang (2)</th>
                        <th class="th-sk">Sangat Kurang (1)</th>
                        <th class="th-bar">Distribusi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($kepuasanPerKategori as $kat)
                    <tr>
                        <td>{{ $kat['kategori'] }}</td>
                        <td class="pct-sb">{{ $kat['pct_sb'] }}%</td>
                        <td class="pct-b">{{ $kat['pct_b'] }}%</td>
                        <td class="pct-k">{{ $kat['pct_k'] }}%</td>
                        <td class="pct-sk">{{ $kat['pct_sk'] }}%</td>
                        <td>
                            <div class="dist-bar">
                                @if($kat['pct_sb'] > 0)<div class="seg seg-sb" style="width:{{ $kat['pct_sb'] }}%;"></div>@endif
                                @if($kat['pct_b']  > 0)<div class="seg seg-b"  style="width:{{ $kat['pct_b']  }}%;"></div>@endif
                                @if($kat['pct_k']  > 0)<div class="seg seg-k"  style="width:{{ $kat['pct_k']  }}%;"></div>@endif
                                @if($kat['pct_sk'] > 0)<div class="seg seg-sk" style="width:{{ $kat['pct_sk'] }}%;"></div>@endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="row-total">
                        <td>Total</td>
                        <td>{{ $kepuasanRingkasan['total']['sb'] }}%</td>
                        <td>{{ $kepuasanRingkasan['total']['b']  }}%</td>
                        <td>{{ $kepuasanRingkasan['total']['k']  }}%</td>
                        <td>{{ $kepuasanRingkasan['total']['sk'] }}%</td>
                        <td></td>
                    </tr>
                    <tr class="row-rata">
                        <td>Rata &#8209; Rata</td>
                        <td>{{ $kepuasanRingkasan['rata']['sb'] }}%</td>
                        <td>{{ $kepuasanRingkasan['rata']['b']  }}%</td>
                        <td>{{ $kepuasanRingkasan['rata']['k']  }}%</td>
                        <td>{{ $kepuasanRingkasan['rata']['sk'] }}%</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
        @endif
    </div>

    {{-- Lulusan + Feedback --}}
    <div class="two-col-eq">

        <div class="panel">
            <div class="panel-header">
                <h6 class="panel-title">Lulusan Terbaru</h6>
                <a href="{{ route('lulusan') }}" class="panel-link">Lihat semua <i class="bi bi-arrow-right"></i></a>
            </div>
            <table class="tbl-lulusan">
                <thead>
                    <tr>
                        <th style="width:42%;">Lulusan</th>
                        <th>Prodi</th>
                        <th class="text-center">Lulus</th>
                        <th class="text-center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($daftarLulusan as $l)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="av">{{ strtoupper(substr($l->nama, 0, 2)) }}</div>
                                    <div>
                                        <div class="av-name">{{ $l->nama }}</div>
                                        <div class="av-nim">{{ $l->nim }}</div>
                                    </div>
                                </div>
                            </td>
                            <td style="color:var(--slate-500);">{{ Str::limit($l->program_studi, 24) }}</td>
                            <td class="text-center" style="font-weight:600;color:var(--slate-700);">
                                {{ $l->tahun_lulus ? $l->tahun_lulus->format('Y') : '-' }}
                            </td>
                            <td class="text-center">
                                @if($l->status)
                                    <span class="badge-pill badge-active">Bekerja</span>
                                @else
                                    <span class="badge-pill badge-inactive">Belum</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4"><div class="empty-state"><i class="bi bi-inbox"></i>Belum ada data lulusan.</div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="panel">
            <div class="panel-header">
                <h6 class="panel-title">Umpan Balik Terbaru</h6>
            </div>
            @forelse($komentarTerbaru as $komen)
                <div class="fb-item">
                    <div class="fb-quote">"{{ Str::limit($komen->jawaban_text, 150) }}"</div>
                    <div class="fb-meta">
                        <div class="fb-who">
                            <strong>{{ $komen->nama_perusahaan ?? 'Anonim' }}</strong>
                            @if($komen->responden)<span> · {{ $komen->responden }}</span>@endif
                        </div>
                        <span class="fb-tag">{{ Str::limit($komen->soal_teks ?? 'Essay', 28) }}</span>
                    </div>
                </div>
            @empty
                <div class="empty-state"><i class="bi bi-chat-dots"></i>Belum ada umpan balik.</div>
            @endforelse
        </div>

    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    document.querySelectorAll('.filter-select').forEach(el =>
        el.addEventListener('change', () => document.getElementById('filterForm').submit())
    );

    var chartData   = <?= json_encode($chartData) ?>;
    var chartLabels = <?= json_encode($chartLabels) ?>;
    var barH        = Math.max(240, chartLabels.length * 46);

    new ApexCharts(document.querySelector('#chart-kinerja'), {
        series : [{ name: 'Skor', data: chartData }],
        chart  : { type: 'bar', height: barH, toolbar: { show: false }, fontFamily: 'inherit' },
        plotOptions: { bar: { horizontal: true, borderRadius: 5, barHeight: '52%' } },
        colors : ['#2563eb'],
        fill   : { type: 'gradient', gradient: { shade: 'light', type: 'horizontal', gradientToColors: ['#60a5fa'], stops: [0,100] } },
        dataLabels: {
            enabled: true, textAnchor: 'start', offsetX: 4,
            style: { colors: ['#fff'], fontSize: '11px', fontWeight: 600 },
            formatter: v => v.toFixed(2)
        },
        xaxis: {
            categories: chartLabels, max: 4,
            labels: { style: { fontSize: '11px', colors: '#64748b' } },
            axisBorder: { show: false }, axisTicks: { show: false }
        },
        yaxis: { labels: { style: { fontSize: '11px', fontWeight: 500, colors: '#334155' } } },
        grid : { xaxis: { lines: { show: true } }, borderColor: '#f1f5f9', strokeDashArray: 4 },
        tooltip: { theme: 'light', y: { title: { formatter: () => 'Skor: ' } } }
    }).render();
</script>
@endsection
