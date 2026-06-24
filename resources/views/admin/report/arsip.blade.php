@extends('layouts.app')

@section('title', 'Arsip Survey')

@section('content')
<style>
    :root {
        --arsip-brand: #8b1a2a;
        --arsip-brand-dark: #6c0215;
        --arsip-soft: #fde8ec;
        --arsip-border: #e2e8f0;
        --arsip-muted: #64748b;
        --arsip-text: #0f172a;
        --arsip-bg: #f8fafc;
        --arsip-radius: 12px;
        --arsip-shadow: 0 1px 3px rgba(15, 23, 42, .06), 0 1px 2px rgba(15, 23, 42, .04);
    }

    .arsip-wrap { display: flex; flex-direction: column; gap: 1rem; }
    .arsip-header {
        display: flex; justify-content: space-between; align-items: flex-end;
        gap: 1rem; flex-wrap: wrap; padding-bottom: 1rem; border-bottom: 1px solid var(--arsip-border);
    }
    .arsip-title { margin: 0; font-size: 1.3rem; font-weight: 800; color: var(--arsip-text); }
    .arsip-subtitle { margin: .25rem 0 0; color: var(--arsip-muted); font-size: .86rem; }
    .breadcrumb { margin-bottom: 0; font-size: .8rem; }

    .arsip-card {
        background: #fff; border: 1px solid var(--arsip-border);
        border-radius: var(--arsip-radius); box-shadow: var(--arsip-shadow); overflow: hidden;
    }
    .arsip-filter { padding: .9rem 1rem; }
    .arsip-filter .form-label {
        font-size: .7rem; font-weight: 700; text-transform: uppercase;
        letter-spacing: .45px; color: var(--arsip-muted); margin-bottom: .28rem;
    }
    .arsip-filter .form-control,
    .arsip-filter .form-select {
        border-color: var(--arsip-border); border-radius: 8px; font-size: .83rem;
    }
    .arsip-filter .form-control:focus,
    .arsip-filter .form-select:focus {
        border-color: var(--arsip-brand); box-shadow: 0 0 0 3px rgba(139, 26, 42, .12);
    }
    .btn-arsip-primary {
        background: var(--arsip-brand); color: #fff; border: 1px solid var(--arsip-brand);
        border-radius: 8px; font-size: .8rem; font-weight: 700;
    }
    .btn-arsip-primary:hover { background: var(--arsip-brand-dark); color: #fff; border-color: var(--arsip-brand-dark); }
    .btn-arsip-light {
        border: 1px solid var(--arsip-border); border-radius: 8px;
        font-size: .8rem; font-weight: 700; color: var(--arsip-muted); background: #fff;
    }
    .btn-arsip-light:hover { background: var(--arsip-bg); color: var(--arsip-text); }

    .arsip-summary {
        display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: .75rem;
    }
    @media(max-width: 767px) { .arsip-summary { grid-template-columns: 1fr; } }
    .summary-card {
        background: #fff; border: 1px solid var(--arsip-border);
        border-radius: var(--arsip-radius); padding: .85rem 1rem; box-shadow: var(--arsip-shadow);
    }
    .summary-label {
        font-size: .7rem; color: var(--arsip-muted); font-weight: 700;
        letter-spacing: .45px; text-transform: uppercase;
    }
    .summary-value { margin-top: .25rem; font-size: 1.45rem; font-weight: 800; color: var(--arsip-text); line-height: 1; }
    .summary-note { margin-top: .25rem; font-size: .75rem; color: var(--arsip-muted); }

    .active-filters { display: flex; gap: .4rem; flex-wrap: wrap; align-items: center; }
    .filter-chip {
        display: inline-flex; align-items: center; gap: .3rem;
        padding: .22rem .6rem; border-radius: 999px;
        background: var(--arsip-soft); border: 1px solid #fbbcca;
        color: var(--arsip-brand-dark); font-size: .72rem; font-weight: 600;
    }

    .table-head {
        display: flex; align-items: center; justify-content: space-between; gap: .75rem;
        padding: .95rem 1rem; border-bottom: 1px solid var(--arsip-border);
    }
    .table-title { margin: 0; font-size: .98rem; font-weight: 800; color: var(--arsip-text); }
    .table-title i { color: var(--arsip-brand); }
    .table-total { color: var(--arsip-muted); font-size: .8rem; }
    .arsip-table { margin: 0; font-size: .83rem; }
    .arsip-table thead th {
        background: var(--arsip-bg); color: #475569; border-bottom: 1px solid var(--arsip-border);
        padding: .75rem 1rem; font-size: .7rem; text-transform: uppercase;
        letter-spacing: .45px; white-space: nowrap;
    }
    .arsip-table tbody td { padding: .82rem 1rem; border-color: #edf2f7; vertical-align: middle; }
    .arsip-table tbody tr:hover td { background: #fffafb; }
    .row-number { color: var(--arsip-muted); font-weight: 700; width: 48px; }
    .primary-text { color: var(--arsip-text); font-weight: 700; line-height: 1.25; }
    .secondary-text { color: var(--arsip-muted); font-size: .75rem; margin-top: .15rem; line-height: 1.3; }
    .badge-fakultas {
        display: inline-flex; align-items: center; padding: .16rem .48rem;
        border-radius: 999px; background: var(--arsip-soft); color: var(--arsip-brand-dark);
        font-size: .68rem; font-weight: 800; margin-top: .25rem;
    }
    .badge-year {
        display: inline-flex; align-items: center; justify-content: center;
        padding: .18rem .55rem; border-radius: 999px;
        background: #fff7dc; color: #92660a; font-size: .72rem; font-weight: 800;
    }
    .btn-detail {
        background: var(--arsip-brand); color: #fff; border-radius: 8px;
        font-size: .75rem; font-weight: 700; padding: .34rem .72rem;
        display: inline-flex; gap: .3rem; align-items: center; text-decoration: none;
    }
    .btn-detail:hover { background: var(--arsip-brand-dark); color: #fff; }
    .empty-arsip { text-align: center; padding: 3rem 1rem; color: var(--arsip-muted); }
    .empty-arsip i { display: block; font-size: 2rem; margin-bottom: .55rem; opacity: .55; }
    .pagination-wrap {
        display: flex; justify-content: space-between; align-items: center;
        gap: .75rem; flex-wrap: wrap; padding: .75rem 1rem; border-top: 1px solid var(--arsip-border);
    }
</style>

@php
    $fakultasLabels = [
        'FTI' => 'Fakultas Teknologi dan Informatika',
        'FEB' => 'Fakultas Ekonomi dan Bisnis',
        'FDIK' => 'Fakultas Desain dan Industri Kreatif',
    ];
    $hasFilters = request()->filled('cari') || request()->filled('tahun') || request()->filled('fakultas') || request()->filled('program_studi');
@endphp

<div class="arsip-wrap">
    <div class="arsip-header">
        <div>
            <h3 class="arsip-title">Arsip Survey</h3>
            <p class="arsip-subtitle">Data survey yang sudah diisi dan disimpan permanen sebagai arsip evaluasi lulusan.</p>
        </div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('report') }}">Cetak Laporan</a></li>
                <li class="breadcrumb-item active">Arsip Survey</li>
            </ol>
        </nav>
    </div>

    <section class="section arsip-wrap">
        <form method="GET" action="{{ route('report.arsip') }}" class="arsip-card arsip-filter">
            <div class="row g-2 align-items-end">
                <div class="col-12 col-lg-4">
                    <label class="form-label">Pencarian</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" name="cari" class="form-control border-start-0"
                               placeholder="Nama, NIM, perusahaan, atau penyelia"
                               value="{{ request('cari') }}">
                    </div>
                </div>
                <div class="col-6 col-lg-2">
                    <label class="form-label">Tahun Instrumen</label>
                    <select name="tahun" class="form-select form-select-sm">
                        <option value="">Semua Tahun</option>
                        @foreach($tahunList as $t)
                            <option value="{{ $t }}" {{ request('tahun') == $t ? 'selected' : '' }}>{{ $t }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-lg-2">
                    <label class="form-label">Fakultas</label>
                    <select name="fakultas" class="form-select form-select-sm">
                        <option value="">Semua Fakultas</option>
                        @foreach($fakultasList as $f)
                            <option value="{{ $f }}" {{ request('fakultas') == $f ? 'selected' : '' }}>
                                {{ $fakultasLabels[$f] ?? $f }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-lg-2">
                    <label class="form-label">Program Studi</label>
                    <select name="program_studi" class="form-select form-select-sm">
                        <option value="">Semua Program Studi</option>
                        @foreach($prodiList as $p)
                            <option value="{{ $p }}" {{ request('program_studi') == $p ? 'selected' : '' }}>{{ $p }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-lg-1">
                    <button type="submit" class="btn btn-sm btn-arsip-primary w-100">
                        Terapkan
                    </button>
                </div>
                <div class="col-6 col-lg-1">
                    <a href="{{ route('report.arsip') }}" class="btn btn-sm btn-arsip-light w-100">
                        Reset
                    </a>
                </div>
            </div>

            @if($hasFilters)
                <div class="active-filters mt-3 pt-3 border-top">
                    <span class="small text-muted fw-semibold me-1">Filter aktif:</span>
                    @if(request('cari')) <span class="filter-chip"><i class="bi bi-search"></i>{{ request('cari') }}</span>@endif
                    @if(request('tahun')) <span class="filter-chip"><i class="bi bi-calendar3"></i>{{ request('tahun') }}</span>@endif
                    @if(request('fakultas')) <span class="filter-chip"><i class="bi bi-building"></i>{{ $fakultasLabels[request('fakultas')] ?? request('fakultas') }}</span>@endif
                    @if(request('program_studi')) <span class="filter-chip"><i class="bi bi-mortarboard"></i>{{ request('program_studi') }}</span>@endif
                </div>
            @endif
        </form>

        <div class="arsip-summary">
            <div class="summary-card">
                <div class="summary-label">Total Arsip</div>
                <div class="summary-value">{{ number_format($arsip->total()) }}</div>
                <div class="summary-note">Data sesuai filter saat ini</div>
            </div>
            <div class="summary-card">
                <div class="summary-label">Halaman Ini</div>
                <div class="summary-value">{{ $arsip->count() }}</div>
                <div class="summary-note">Arsip yang sedang ditampilkan</div>
            </div>
            <div class="summary-card">
                <div class="summary-label">Per Halaman</div>
                <div class="summary-value">{{ $arsip->perPage() }}</div>
                <div class="summary-note">Gunakan filter untuk mempersempit data</div>
            </div>
        </div>

        <div class="arsip-card">
            <div class="table-head">
                <h5 class="table-title">
                    <i class="bi bi-archive-fill me-2"></i>Daftar Arsip Survey
                </h5>
                <span class="table-total">Total <strong>{{ number_format($arsip->total()) }}</strong> arsip</span>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle arsip-table">
                    <thead>
                        <tr>
                            <th style="width:48px;">#</th>
                            <th>Lulusan</th>
                            <th>Program Studi</th>
                            <th>Perusahaan</th>
                            <th>Penyelia</th>
                            <th class="text-center" style="width:95px;">Tahun</th>
                            <th style="width:125px;">Tanggal Isi</th>
                            <th class="text-center" style="width:90px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($arsip as $i => $item)
                            <tr>
                                <td class="row-number">{{ $arsip->firstItem() + $i }}</td>
                                <td>
                                    <div class="primary-text">{{ $item->lulusan_nama ?? '-' }}</div>
                                    <div class="secondary-text">NIM {{ $item->lulusan_nim ?? '-' }}</div>
                                </td>
                                <td>
                                    <div class="primary-text">{{ $item->lulusan_program_studi ?? '-' }}</div>
                                    @if($item->lulusan_fakultas)
                                        <span class="badge-fakultas">{{ $fakultasLabels[$item->lulusan_fakultas] ?? $item->lulusan_fakultas }}</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="primary-text">{{ $item->perusahaan_nama ?? '-' }}</div>
                                    <div class="secondary-text">
                                        {{ implode(', ', array_filter([$item->perusahaan_cabang_kota, $item->perusahaan_cabang_negara])) ?: 'Lokasi belum tersedia' }}
                                    </div>
                                </td>
                                <td>
                                    <div class="primary-text">{{ $item->penyelia_nama ?? '-' }}</div>
                                    <div class="secondary-text">{{ $item->penyelia_jabatan ?? 'Jabatan belum tersedia' }}</div>
                                </td>
                                <td class="text-center">
                                    @if($item->tahun_instrumen)
                                        <span class="badge-year">{{ $item->tahun_instrumen }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="text-muted" style="white-space:nowrap;font-size:.78rem;">
                                    {{ $item->submitted_at ? \Carbon\Carbon::parse($item->submitted_at)->format('d M Y') : '-' }}
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('report.arsip.detail', $item->id) }}" class="btn-detail">
                                        <i class="bi bi-eye-fill"></i>Lihat
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8">
                                    <div class="empty-arsip">
                                        <i class="bi bi-archive"></i>
                                        <div class="fw-bold text-dark mb-1">Tidak ada arsip yang sesuai</div>
                                        <div>Ubah kata kunci atau kosongkan filter untuk melihat semua arsip survey.</div>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($arsip->hasPages())
                <div class="pagination-wrap">
                    <p class="text-muted small mb-0">
                        Menampilkan <strong>{{ $arsip->firstItem() }}-{{ $arsip->lastItem() }}</strong>
                        dari <strong>{{ $arsip->total() }}</strong> arsip
                    </p>
                    {{ $arsip->links() }}
                </div>
            @endif
        </div>
    </section>
</div>
@endsection
