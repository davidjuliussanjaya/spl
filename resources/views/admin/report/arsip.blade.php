@extends('layouts.app')

@section('title', 'Arsip Survey')

@section('content')
<div class="page-heading">

    <div class="page-title mb-4 pb-3 border-bottom">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3 class="fw-bold mb-1">Arsip Survey</h3>
                <p class="text-muted mb-0 small">Data survey yang telah diisi, tersimpan permanen sebagai arsip.</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('report') }}">Cetak Laporan</a></li>
                        <li class="breadcrumb-item active">Arsip Survey</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <section class="section">

        {{-- Filter --}}
        <form method="GET" action="{{ route('report.arsip') }}" class="mb-3">
            <div class="card">
                <div class="card-body py-3">
                    <div class="row g-2 align-items-end">
                        <div class="col-12 col-md-3">
                            <label class="form-label small fw-semibold text-muted mb-1">Cari Nama / NIM / Perusahaan</label>
                            <input type="text" name="cari" class="form-control form-control-sm"
                                   placeholder="Ketik untuk mencari..."
                                   value="{{ request('cari') }}">
                        </div>
                        <div class="col-6 col-md-2">
                            <label class="form-label small fw-semibold text-muted mb-1">Tahun Instrumen</label>
                            <select name="tahun" class="form-select form-select-sm">
                                <option value="">Semua Tahun</option>
                                @foreach($tahunList as $t)
                                    <option value="{{ $t }}" {{ request('tahun') == $t ? 'selected' : '' }}>{{ $t }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-6 col-md-2">
                            <label class="form-label small fw-semibold text-muted mb-1">Fakultas</label>
                            <select name="fakultas" class="form-select form-select-sm">
                                <option value="">Semua Fakultas</option>
                                @foreach($fakultasList as $f)
                                    <option value="{{ $f }}" {{ request('fakultas') == $f ? 'selected' : '' }}>{{ $f }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-md-3">
                            <label class="form-label small fw-semibold text-muted mb-1">Program Studi</label>
                            <select name="program_studi" class="form-select form-select-sm">
                                <option value="">Semua Program Studi</option>
                                @foreach($prodiList as $p)
                                    <option value="{{ $p }}" {{ request('program_studi') == $p ? 'selected' : '' }}>{{ $p }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-6 col-md-1">
                            <button type="submit" class="btn btn-sm w-100 text-white" style="background:#8B1A2A;">
                                <i class="bi bi-search"></i> Cari
                            </button>
                        </div>
                        <div class="col-6 col-md-1">
                            <a href="{{ route('report.arsip') }}" class="btn btn-sm btn-outline-secondary w-100">
                                <i class="bi bi-x"></i> Reset
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        {{-- Tabel --}}
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="bi bi-archive-fill me-2" style="color:#8B1A2A;"></i>
                    Daftar Arsip
                </h5>
                <span class="text-muted small">
                    Total: <strong>{{ $arsip->total() }}</strong> arsip
                </span>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" style="font-size:0.83rem;">
                        <thead style="background:#8B1A2A;color:#fff;">
                            <tr>
                                <th class="px-3 py-3" style="width:44px;">#</th>
                                <th class="py-3">Lulusan</th>
                                <th class="py-3">Prodi / Fakultas</th>
                                <th class="py-3">Perusahaan</th>
                                <th class="py-3">Penyelia</th>
                                <th class="py-3 text-center" style="width:90px;">Thn.</th>
                                <th class="py-3" style="width:110px;">Tgl. Diisi</th>
                                <th class="py-3 text-center" style="width:90px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($arsip as $i => $item)
                            <tr>
                                <td class="px-3 text-muted">{{ $arsip->firstItem() + $i }}</td>
                                <td>
                                    <div class="fw-semibold">{{ $item->lulusan_nama ?? '-' }}</div>
                                    <div class="text-muted" style="font-size:0.75rem;">{{ $item->lulusan_nim ?? '-' }}</div>
                                </td>
                                <td>
                                    <div>{{ $item->lulusan_program_studi ?? '-' }}</div>
                                    @if($item->lulusan_fakultas)
                                        <span class="badge text-white" style="background:#8B1A2A;font-size:0.68rem;">
                                            {{ $item->lulusan_fakultas }}
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <div>{{ $item->perusahaan_nama ?? '-' }}</div>
                                    @if($item->perusahaan_cabang_kota)
                                        <div class="text-muted" style="font-size:0.75rem;">{{ $item->perusahaan_cabang_kota }}</div>
                                    @endif
                                </td>
                                <td>
                                    <div>{{ $item->penyelia_nama ?? '-' }}</div>
                                    @if($item->penyelia_jabatan)
                                        <div class="text-muted" style="font-size:0.75rem;">{{ $item->penyelia_jabatan }}</div>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($item->tahun_instrumen)
                                        <span class="badge" style="background:#FFF5D6;color:#92660A;font-size:0.72rem;">
                                            {{ $item->tahun_instrumen }}
                                        </span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td class="text-muted" style="white-space:nowrap;font-size:0.78rem;">
                                    {{ $item->submitted_at ? \Carbon\Carbon::parse($item->submitted_at)->format('d M Y') : '-' }}
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('report.arsip.detail', $item->id) }}"
                                       class="btn btn-sm text-white px-3"
                                       style="background:#8B1A2A;font-size:0.75rem;">
                                        <i class="bi bi-eye-fill me-1"></i>Lihat
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">
                                    <i class="bi bi-archive fs-2 d-block mb-2"></i>
                                    Tidak ada arsip yang sesuai filter.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if($arsip->hasPages())
            <div class="card-body pt-2 pb-3 border-top d-flex justify-content-between align-items-center flex-wrap gap-2">
                <p class="text-muted small mb-0">
                    Menampilkan <strong>{{ $arsip->firstItem() }}–{{ $arsip->lastItem() }}</strong>
                    dari <strong>{{ $arsip->total() }}</strong> arsip
                </p>
                {{ $arsip->links() }}
            </div>
            @endif
        </div>

    </section>
</div>
@endsection
