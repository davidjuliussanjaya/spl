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

    <section class="card">
        <div class="card-header spl-toolbar">
            <div>
                <h4 class="spl-toolbar-title">Daftar sesi survei <span class="spl-filter-count">{{ $surveys->count() }} hasil</span></h4>
                <p class="spl-toolbar-subtitle">Cari berdasarkan judul, lulusan, perusahaan, atau kode akses.</p>
            </div>
        </div>

        <form action="{{ route('survey') }}" method="GET" class="spl-filter-panel">
            <div class="row g-3 align-items-end">
                <div class="col-12 col-lg-5">
                    <label for="cari" class="form-label">Cari survei</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                        <input id="cari" type="search" name="cari" class="form-control border-start-0" value="{{ request('cari') }}" placeholder="Judul, lulusan, perusahaan, atau kode">
                    </div>
                </div>
                <div class="col-6 col-lg-2">
                    <label for="tahun" class="form-label">Tahun</label>
                    <select id="tahun" name="tahun" class="form-select">
                        <option value="">Semua tahun</option>
                        @foreach($tahunList as $tahun)
                            <option value="{{ $tahun }}" @selected((string) request('tahun') === (string) $tahun)>{{ $tahun }}</option>
                        @endforeach
                    </select>
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
                    <a href="{{ route('survey') }}" class="btn btn-outline-secondary">Reset</a>
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
                                    <a href="{{ route('survey.edit', $survey->id) }}" class="spl-icon-action" title="Lihat atau ubah survei" aria-label="Lihat atau ubah survei {{ $survey->judul }}"><i class="bi bi-pencil-square"></i></a>
                                    <form action="{{ route('survey.destroy', $survey->id) }}" method="POST" onsubmit="return confirm('Hapus survei ini? Tindakan ini tidak dapat dibatalkan.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="spl-icon-action text-danger" title="Hapus survei" aria-label="Hapus survei {{ $survey->judul }}"><i class="bi bi-trash"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="spl-empty"><i class="bi bi-inbox"></i>Belum ada survei yang sesuai. Ubah filter atau buat survei baru.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>
@endsection
