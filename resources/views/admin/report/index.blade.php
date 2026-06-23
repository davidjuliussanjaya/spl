@extends('layouts.app')

@section('title', 'Cetak Laporan')

@section('content')
<div class="page-heading">
    <div class="page-title mb-4 pb-3 border-bottom">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3 class="fw-bold mb-1">Cetak Laporan Tracer Study</h3>
                <p class="text-muted mb-0 small">Pilih filter lalu unduh laporan dalam format Excel (.xlsx)</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Cetak Laporan</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <div class="row">
        {{-- Panel Filter --}}
        <div class="col-12 col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white pt-4 border-0">
                    <div class="d-flex align-items-center">
                        <div class="text-white rounded-circle d-flex align-items-center justify-content-center me-3"
                             style="width:38px;height:38px;background:#8B1A2A;">
                            <i class="bi bi-funnel-fill"></i>
                        </div>
                        <h5 class="fw-bold mb-0">Filter Laporan</h5>
                    </div>
                </div>

                <div class="card-body px-4 pb-4">
                    <form method="GET" action="{{ route('report.download') }}" id="reportForm" target="_blank">

                        <div class="mb-4">
                            <label class="form-label fw-bold small text-uppercase text-muted">
                                <i class="bi bi-calendar3 me-1"></i> Tahun Lulus
                            </label>
                            <select name="tahun" class="form-select">
                                <option value="">Semua Tahun</option>
                                @foreach($tahunList as $t)
                                    <option value="{{ $t }}" {{ ($filters['tahun'] ?? '') == $t ? 'selected' : '' }}>
                                        {{ $t }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-text">Kosongkan untuk semua tahun (1 sheet per tahun)</div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold small text-uppercase text-muted">
                                <i class="bi bi-building me-1"></i> Fakultas
                            </label>
                            <select name="fakultas" id="filterFakultas" class="form-select">
                                <option value="">Semua Fakultas</option>
                                @foreach($fakultasList as $f)
                                    <option value="{{ $f }}" {{ ($filters['fakultas'] ?? '') == $f ? 'selected' : '' }}>
                                        {{ $f }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold small text-uppercase text-muted">
                                <i class="bi bi-mortarboard me-1"></i> Program Studi
                            </label>
                            <select name="program_studi" class="form-select">
                                <option value="">Semua Program Studi</option>
                                @foreach($prodiList as $p)
                                    <option value="{{ $p }}" {{ ($filters['program_studi'] ?? '') == $p ? 'selected' : '' }}>
                                        {{ $p }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-lg fw-bold text-white" style="background:#8B1A2A;border-color:#8B1A2A;" onmouseover="this.style.background='#6C0215'" onmouseout="this.style.background='#8B1A2A'">
                                <i class="bi bi-file-earmark-excel-fill me-2"></i> Download Excel
                            </button>
                            <a href="{{ route('report') }}" class="btn btn-outline-secondary btn-sm">
                                <i class="bi bi-arrow-counterclockwise me-1"></i> Reset Filter
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Panel Info --}}
        <div class="col-12 col-lg-8 mt-4 mt-lg-0">

            {{-- Stat --}}
            <div class="row g-3 mb-4">
                <div class="col-6">
                    <div class="card border-0 shadow-sm text-center py-3">
                        <div class="card-body">
                            <i class="bi bi-check-circle-fill fs-2 mb-2" style="color:#8B1A2A;"></i>
                            <h3 class="fw-bold mb-0" style="color:#8B1A2A;">{{ $totalSurveySelesai }}</h3>
                            <p class="text-muted small mb-0">Survey Selesai Diisi</p>
                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="card border-0 shadow-sm text-center py-3">
                        <div class="card-body">
                            <i class="bi bi-calendar-range-fill fs-2 mb-2" style="color:#C9A227;"></i>
                            <h3 class="fw-bold mb-0" style="color:#C9A227;">{{ $tahunList->count() }}</h3>
                            <p class="text-muted small mb-0">Tahun Lulus Tersedia</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Info format laporan --}}
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white pt-4 border-0">
                    <h5 class="fw-bold mb-0"><i class="bi bi-info-circle me-2" style="color:#8B1A2A;"></i>Format Laporan</h5>
                </div>
                <div class="card-body px-4 pb-4">
                    <div class="row g-3">
                        <div class="col-12">
                            <div class="d-flex align-items-start p-3 rounded-3" style="background:#FFF5F7;">
                                <div class="text-white rounded p-2 me-3 flex-shrink-0" style="background:#8B1A2A;">
                                    <i class="bi bi-layout-text-window-reverse fs-5"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold mb-1">Sheet per Tahun Lulus</h6>
                                    <p class="text-muted small mb-0">Jika tidak memilih tahun, setiap tahun lulus akan menjadi sheet terpisah dalam satu file Excel.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="d-flex align-items-start p-3 rounded-3" style="background:#FFF5F7;">
                                <div class="text-white rounded p-2 me-3 flex-shrink-0" style="background:#C9A227;">
                                    <i class="bi bi-grid-3x3-gap-fill fs-5"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold mb-1">Data per Fakultas</h6>
                                    <p class="text-muted small mb-0">Setiap sheet dibagi per Fakultas (FTI, FDIK, FEB). Soal ditampilkan sesuai peruntukan masing-masing fakultas.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="d-flex align-items-start p-3 rounded-3" style="background:#FFF5F7;">
                                <div class="text-white rounded p-2 me-3 flex-shrink-0" style="background:#B91C3A;">
                                    <i class="bi bi-percent fs-5"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold mb-1">Tabel Ringkasan Distribusi</h6>
                                    <p class="text-muted small mb-0">Di bagian bawah setiap sheet terdapat tabel distribusi persentase per soal per program studi (% Sangat Baik, Baik, Kurang, Sangat Kurang).</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr class="my-3">

                    <h6 class="fw-bold mb-3 text-muted text-uppercase small">Kolom yang tersedia</h6>
                    <div class="row g-2">
                        @foreach(['NIM', 'Nama Lulusan', 'Program Studi', 'Nama Responden', 'Nama Perusahaan', 'Jenis Perusahaan', 'Cabang Kota', 'Cabang Negara', 'Nilai per soal (kode B1, C1, dst.)'] as $col)
                            <div class="col-auto">
                                <span class="badge bg-secondary-subtle text-secondary border">{{ $col }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
