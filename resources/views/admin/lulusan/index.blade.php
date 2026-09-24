@extends('layouts.app')

@section('title', 'Lulusan')

@push('styles')
<style>
    .lulusan-filter-field { min-width: 180px; }
    .lulusan-filter-field.prodi { min-width: 230px; }
    .lulusan-filter-dropdown { width: 100%; }
    .lulusan-filter-toggle { align-items: center; background: #fff; border: 1px solid var(--spl-border); border-radius: 8px; display: flex; gap: .5rem; justify-content: space-between; min-height: 38px; padding: .4rem .7rem; text-align: left; width: 100%; }
    .lulusan-filter-toggle span { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .lulusan-filter-menu { max-height: 230px; overflow-y: auto; padding: .45rem; width: 100%; }
    .lulusan-filter-option { align-items: center; border-radius: 6px; color: var(--spl-text); cursor: pointer; display: flex; font-size: .84rem; gap: .5rem; margin: 0; padding: .38rem .45rem; }
    .lulusan-filter-option:hover { background: var(--spl-brand-soft); }
    .lulusan-filter-option input { accent-color: var(--spl-brand); }
    @media (max-width: 767.98px) { .lulusan-filter-field, .lulusan-filter-field.prodi { min-width: 0; } }
</style>
@endpush

@section('content')
@php
    $activePeriode = $filters['periode'] ?? [];
    $selectedProdi = $filters['program_studi'] ?? [];
    $availableProdi = $filterOptions['prodiList']->all();
    $prodiPeriode = $filterOptions['prodiPeriode'];
@endphp
<div class="page-heading">
    <div class="spl-page-header">
        <div>
            <nav class="spl-breadcrumb" aria-label="Breadcrumb"><a href="{{ route('dashboard') }}">Dashboard</a><span>/</span><span>Lulusan</span></nav>
            <h3>Data Lulusan</h3>
            <p>Temukan dan kelola profil lulusan Universitas Dinamika.</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <form action="{{ route('lulusan.sync') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-outline-primary" title="Sinkronkan data mahasiswa dari REST API"><i class="bi bi-arrow-repeat"></i> Sinkron Data Mahasiswa</button>
            </form>
            <a href="{{ route('addgrad') }}" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Tambah lulusan</a>
        </div>
    </div>

    @if(session('success'))<div class="alert alert-success spl-alert" role="status"><i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}</div>@endif
    @if(session('error'))<div class="alert alert-danger spl-alert" role="alert"><i class="bi bi-exclamation-circle-fill me-2"></i>{{ session('error') }}</div>@endif

    <section class="card">
        <div class="card-header spl-toolbar"><div><h4 class="spl-toolbar-title">Daftar lulusan <span class="spl-filter-count">{{ $lulusan->total() }} hasil</span></h4><p class="spl-toolbar-subtitle">Gunakan pencarian dan filter untuk mempersempit data.</p></div></div>
        <form action="{{ route('lulusan') }}" method="GET" class="spl-filter-panel" data-lulusan-filter>
            <div class="row g-3 align-items-end">
                <div class="col-12 col-md-4 col-xl-3 lulusan-filter-field"><label for="nama" class="form-label">Nama lulusan</label><input id="nama" type="search" name="nama" class="form-control" value="{{ request('nama') }}" placeholder="Cari nama"></div>
                <div class="col-6 col-md-3 col-xl-2 lulusan-filter-field"><label for="nim" class="form-label">NIM</label><input id="nim" type="search" name="nim" class="form-control" value="{{ request('nim') }}" placeholder="Cari NIM"></div>
                <div class="col-6 col-md-3 col-xl-2 lulusan-filter-field">
                    <label class="form-label">Periode</label>
                    <div class="dropdown lulusan-filter-dropdown">
                        <button class="lulusan-filter-toggle" type="button" data-bs-toggle="dropdown" data-bs-auto-close="false" aria-expanded="false"><span>@if(empty($activePeriode)) Semua periode @elseif(count($activePeriode) === 1) {{ $filterOptions['periodeList'][$activePeriode[0]] ?? $activePeriode[0] }} @else {{ count($activePeriode) }} periode dipilih @endif</span><i class="bi bi-chevron-down"></i></button>
                        <div class="dropdown-menu lulusan-filter-menu">
                            @foreach($filterOptions['periodeList'] as $kodePeriode => $namaPeriode)
                                <label class="lulusan-filter-option"><input type="checkbox" name="periode[]" value="{{ $kodePeriode }}" @checked(in_array($kodePeriode, $activePeriode, true))><span>{{ $namaPeriode }}</span></label>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-4 col-xl-3 lulusan-filter-field prodi">
                    <label class="form-label">Program studi</label>
                    <div class="dropdown lulusan-filter-dropdown">
                        <button class="lulusan-filter-toggle" type="button" data-bs-toggle="dropdown" data-bs-auto-close="false" aria-expanded="false"><span>@if(empty($selectedProdi)) Semua prodi @elseif(count($selectedProdi) === 1) {{ $selectedProdi[0] }} @else {{ count($selectedProdi) }} prodi dipilih @endif</span><i class="bi bi-chevron-down"></i></button>
                        <div class="dropdown-menu lulusan-filter-menu">
                            @foreach($prodiPeriode as $prodi => $periodeProdi)
                                <label class="lulusan-filter-option" data-prodi-option data-periode='@json($periodeProdi)' @hidden(!in_array($prodi, $availableProdi, true))><input type="checkbox" name="program_studi[]" value="{{ $prodi }}" @checked(in_array($prodi, $selectedProdi, true))><span>{{ $prodi }}</span></label>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-3 col-xl-2 spl-filter-actions"><button type="submit" class="btn btn-primary"><i class="bi bi-funnel"></i> Terapkan</button><a href="{{ route('lulusan') }}" class="btn btn-outline-secondary">Reset</a></div>
            </div>
        </form>
        <div class="table-responsive">
            <table class="table spl-table" id="table1">
                <thead><tr><th>Lulusan</th><th>Fakultas</th><th>Program studi</th><th>Tahun lulus</th><th class="text-center">Aksi</th></tr></thead>
                <tbody>
                    @forelse($lulusan as $data)
                        <tr>
                            <td>
                                <span class="spl-row-title">{{ $data->nama }}</span>
                                @if($data->is_aggregate)<span class="badge bg-secondary ms-1">Arsip agregat</span>@endif
                                <span class="spl-row-meta">{{ $data->is_aggregate ? 'ID Arsip' : 'NIM' }} {{ $data->nim }}</span>
                            </td>
                            <td><span class="badge bg-primary">{{ $data->fakultasMaster?->kode ?? '-' }}</span><span class="spl-row-meta">{{ $data->fakultasMaster?->nama ?? '-' }}</span></td>
                            <td>{{ $data->programStudi?->nama ?? '-' }}</td><td>{{ $data->tahun_lulus?->format('Y') ?? '-' }}</td>
                            <td class="text-center"><a href="{{ route('lulusan.show', $data->id) }}" class="spl-icon-action" title="Lihat profil" aria-label="Lihat profil {{ $data->nama }}"><i class="bi bi-arrow-up-right"></i></a></td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="spl-empty"><i class="bi bi-search"></i>Data lulusan tidak ditemukan. Coba ubah kata pencarian atau filter.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($lulusan->hasPages())<div class="spl-pagination"><span>Menampilkan {{ $lulusan->firstItem() }}–{{ $lulusan->lastItem() }} dari {{ $lulusan->total() }} data</span>{{ $lulusan->links() }}</div>@endif
    </section>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const filter = document.querySelector('[data-lulusan-filter]');
        if (!filter) return;
        const periodeInputs = [...filter.querySelectorAll('input[name="periode[]"]')];
        const prodiOptions = [...filter.querySelectorAll('[data-prodi-option]')];
        const syncProdiByPeriode = function () {
            const selectedPeriode = periodeInputs.filter((input) => input.checked).map((input) => input.value);
            prodiOptions.forEach(function (option) {
                const periodeProdi = JSON.parse(option.dataset.periode || '[]');
                const tersedia = selectedPeriode.length === 0 || periodeProdi.some((periode) => selectedPeriode.includes(periode));
                const input = option.querySelector('input[name="program_studi[]"]');
                option.hidden = !tersedia;
                if (!tersedia && input?.checked) input.checked = false;
            });
        };
        periodeInputs.forEach((input) => input.addEventListener('change', syncProdiByPeriode));
        syncProdiByPeriode();
    });
</script>
@endpush
