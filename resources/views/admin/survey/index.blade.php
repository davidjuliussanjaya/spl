@extends('layouts.app')

@section('title', 'Survei')

@section('content')
<div class="page-heading">
    <div class="spl-page-header">
        <div>
            <nav class="spl-breadcrumb" aria-label="Breadcrumb">
                <a href="{{ route('dashboard') }}">Dashboard</a><span>/</span><span>Survei</span>
            </nav>
            <h3>Manajemen Survei</h3>
            <p>Kelola pengiriman, status respons, dan akses survei perusahaan.</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('survey.bulk') }}" class="btn btn-outline-primary"><i class="bi bi-collection"></i> Buat massal</a>
            <a href="{{ route('addsurvey') }}" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Buat survei</a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success spl-alert" role="status"><i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger spl-alert" role="alert"><i class="bi bi-exclamation-circle-fill me-2"></i>{{ session('error') }}</div>
    @endif

    @if(! $selectedTahun)
        <section class="card spl-period-selector">
            <div class="card-header spl-period-selector-header">
                <div class="spl-period-heading">
                    <span class="spl-period-heading-icon" aria-hidden="true"><i class="bi bi-calendar3"></i></span>
                    <div>
                        <h4 class="spl-toolbar-title">Pilih periode survei</h4>
                        <p class="spl-toolbar-subtitle">Pilih periode untuk melihat daftar sesi, status respons, dan kode akses.</p>
                    </div>
                </div>
                <span class="spl-period-count">{{ $tahunList->count() }} periode tersedia</span>
            </div>
            <div class="card-body spl-period-selector-body">
                <div class="spl-period-grid">
                    @forelse($tahunList as $tahun)
                        <a href="{{ route('survey', ['tahun' => $tahun]) }}" class="spl-period-option" aria-label="Buka survei periode {{ $tahun }}">
                            <span class="spl-period-option-icon" aria-hidden="true"><i class="bi bi-calendar3"></i></span>
                            <span class="spl-period-option-copy">
                                <span class="spl-period-option-label">Periode survei</span>
                                <strong>{{ $tahun }}</strong>
                                <span class="spl-period-option-action">Lihat daftar sesi <i class="bi bi-arrow-right"></i></span>
                            </span>
                            <span class="spl-period-option-arrow" aria-hidden="true"><i class="bi bi-chevron-right"></i></span>
                        </a>
                    @empty
                        <div class="spl-empty"><i class="bi bi-inbox"></i>Belum ada periode survei. Buat survei baru untuk memulai.</div>
                    @endforelse
                </div>
            </div>
        </section>
    @else
    <section class="card">
        <div class="card-header spl-toolbar">
            <div>
                <h4 class="spl-toolbar-title">Daftar sesi survei periode {{ $selectedTahun }} <span class="spl-filter-count">{{ $surveys->total() }} hasil</span></h4>
                <p class="spl-toolbar-subtitle">Cari berdasarkan judul, lulusan, perusahaan, atau kode akses.</p>
            </div>
            <a href="{{ route('survey') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Pilih periode lain</a>
        </div>

        <form action="{{ route('survey') }}" method="GET" class="spl-filter-panel">
            <input type="hidden" name="tahun" value="{{ $selectedTahun }}">
            <div class="row g-3 align-items-end">
                <div class="col-12 col-lg-7">
                    <label for="cari" class="form-label">Cari survei</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                        <input id="cari" type="search" name="cari" class="form-control border-start-0" value="{{ request('cari') }}" placeholder="Judul, lulusan, perusahaan, atau kode">
                    </div>
                </div>
                <div class="col-6 col-lg-2">
                    <label for="status" class="form-label">Status</label>
                    <select id="status" name="status" class="form-select">
                        <option value="">Semua status</option>
                        <option value="belum" @selected(request('status') === 'belum')>Belum diisi</option>
                        <option value="selesai" @selected(request('status') === 'selesai')>Selesai</option>
                    </select>
                </div>
                <div class="col-12 col-lg-3 spl-filter-actions">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-funnel"></i> Terapkan</button>
                    <a href="{{ route('survey', ['tahun' => $selectedTahun]) }}" class="btn btn-outline-secondary">Reset</a>
                </div>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table spl-table" id="table1">
                <thead>
                    <tr>
                        <th>Survei</th><th>Periode</th><th>Perusahaan</th><th>Lulusan</th><th>Kode akses</th><th>Status</th><th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($surveys as $survey)
                        <tr>
                            <td><span class="spl-row-title">{{ $survey->judul }}</span></td>
                            <td><span class="badge bg-primary">{{ $survey->tahun ?? 'Tidak ada' }}</span></td>
                            <td>{{ $survey->penggunalulusan->nama_perusahaan ?? '-' }}</td>
                            <td>
                                <span class="spl-row-title">{{ $survey->lulusan->nama ?? '-' }}</span>
                                @if($survey->lulusan?->nim)<span class="spl-row-meta">{{ $survey->lulusan->nim }}</span>@endif
                            </td>
                            <td><code class="text-dark">{{ $survey->access_code }}</code></td>
                            <td>
                                @if($survey->is_completed)
                                    <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Selesai</span>
                                @else
                                    <span class="badge bg-warning"><i class="bi bi-clock me-1"></i>Belum diisi</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex justify-content-center gap-1">
                                    @if($survey->is_completed)
                                        <a href="{{ route('survey.edit', $survey->id) }}" class="spl-icon-action" title="Lihat detail arsip survei" aria-label="Lihat detail arsip survei {{ $survey->judul }}"><i class="bi bi-eye"></i></a>
                                    @else
                                        <a href="{{ route('survey.edit', $survey->id) }}" class="spl-icon-action" title="Lihat atau ubah survei" aria-label="Lihat atau ubah survei {{ $survey->judul }}"><i class="bi bi-pencil-square"></i></a>
                                        <form action="{{ route('survey.destroy', $survey->id) }}" method="POST" onsubmit="return confirm('Hapus survei ini? Tindakan ini tidak dapat dibatalkan.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="spl-icon-action text-danger" title="Hapus survei" aria-label="Hapus survei {{ $survey->judul }}"><i class="bi bi-trash"></i></button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="spl-empty"><i class="bi bi-inbox"></i>Belum ada survei yang sesuai. Ubah filter atau buat survei baru.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($surveys->hasPages())
            <div class="spl-pagination">
                <span>Menampilkan {{ $surveys->firstItem() }}–{{ $surveys->lastItem() }} dari {{ $surveys->total() }} data</span>
                {{ $surveys->links() }}
            </div>
        @endif
    </section>
    @endif
</div>
@endsection
