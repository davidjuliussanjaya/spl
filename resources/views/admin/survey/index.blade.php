@extends('layouts.app')

@section('title', 'Daftar Survey')

@section('content')
<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Data Manajemen Survey</h3>
                <p class="text-subtitle text-muted">Daftar sesi survey yang telah dibuat untuk perusahaan.</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Survey</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    
    <section class="section">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center border-bottom">
                <h5 class="mb-0">Daftar Sesi Survey</h5>

                {{-- Pastikan route name sesuai dengan di web.php Anda --}}
                <div class="d-flex gap-2">
                    <a href="{{ route('survey.bulk') }}" class="btn btn-success btn-sm">
                        <i class="bi bi-layers"></i> Buat Survey Massal
                    </a>
                    <a href="{{ route('addsurvey') }}" class="btn btn-primary btn-sm">
                        <i class="bi bi-plus-circle"></i> Buat Survey Baru
                    </a>
                </div>
            </div>
            <div class="card-body mt-3">
                {{-- Filter Pencarian (Opsional, disesuaikan untuk konteks survey) --}}
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label class="form-label fw-bold">Judul Survey:</label>
                        <input type="text" class="form-control" placeholder="Cari judul...">
                    </div>

                    <div class="col-md-3 mb-3">
                        <label class="form-label fw-bold">Nama Perusahaan:</label>
                        <input type="text" class="form-control" placeholder="Cari perusahaan...">
                    </div>

                    <div class="col-md-3 mb-3">
                        <label class="form-label fw-bold">Alumni / Lulusan:</label>
                        <input type="text" class="form-control" placeholder="Cari alumni...">
                    </div>

                    <div class="col-md-3 mb-3">
                        <label class="form-label fw-bold">Status:</label>
                        <select class="form-select">
                            <option selected value="">Semua Status</option>
                            <option value="1">Selesai</option>
                            <option value="0">Belum Diisi</option>
                        </select>
                    </div>
                </div>

                {{-- Tabel Data Survey --}}
                <div class="table-responsive mt-3">
                    <table class="table table-striped table-hover" id="table1">
                        <thead class="table-light">
                            <tr>
                                <th width="5%">No</th>
                                <th>Judul Survey</th>
                                <th class="text-center" width="7%">Tahun</th>
                                <th>Perusahaan</th>
                                <th>Lulusan Terkait</th>
                                <th>Kode Akses</th>
                                <th>Status</th>
                                <th width="10%" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($surveys as $index => $survey)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td class="fw-bold">{{ $survey->judul }}</td>

                                    <td class="text-center">
                                        @if($survey->tahun)
                                            <span class="badge" style="background:#FFF5D6;color:#92660A;font-size:0.78rem;font-weight:700;">
                                                {{ $survey->tahun }}
                                            </span>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>

                                    {{-- Mengambil relasi perusahaan --}}
                                    <td>{{ $survey->penggunalulusan->nama_perusahaan ?? '-' }}</td>

                                    {{-- Mengambil relasi lulusan --}}
                                    <td>{{ $survey->lulusan->nama ?? '-' }}</td>

                                    <td>
                                        <span class="badge bg-light text-dark border" style="letter-spacing: 2px;">
                                            {{ $survey->access_code }}
                                        </span>
                                    </td>

                                    <td>
                                        @if($survey->is_completed)
                                            <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i> Selesai</span>
                                        @else
                                            <span class="badge bg-warning text-dark"><i class="bi bi-clock me-1"></i> Belum Diisi</span>
                                        @endif
                                    </td>
                                    
                                    <td class="text-center">
                                        {{-- Tombol Detail / Aksi --}}
                                        <div class="d-flex justify-content-center gap-1">
                                            <a href="{{ route('survey.edit', $survey->id) }}" class="btn btn-sm btn-info text-white" title="Lihat Detail">
    <i class="bi bi-eye"></i>
</a>
                                            <form action="{{ route('survey.destroy', $survey->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus survey ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" title="Hapus">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-4">
                                        Belum ada data survey yang dibuat.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
            </div>
        </div>
    </section>
</div>
@endsection
