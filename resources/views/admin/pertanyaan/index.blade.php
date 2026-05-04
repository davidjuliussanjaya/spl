@extends('layouts.app')

@section('title', 'Data Soal')

@section('content')
<div class="page-heading">
    <div class="page-title mb-4 pb-3 border-bottom">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center">
        
        <div class="mb-3 mb-md-0">
            <h3 class="fw-bold mb-1 text-dark">Data Pertanyaan</h3>
            <p class="text-muted mb-0 small">Kelola daftar soal dan kuesioner untuk survey lulusan.</p>
        </div>

        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 px-3 py-2 bg-white rounded-pill shadow-sm border">
                <li class="breadcrumb-item">
                    <a href="#" class="text-decoration-none text-primary">
                        <i class="bi bi-folder2-open me-1"></i> Survey
                    </a>
                </li>
                <li class="breadcrumb-item active fw-semibold text-secondary" aria-current="page">Pertanyaan</li>
            </ol>
        </nav>
        
    </div>
</div>

    <section class="section">
        <div class="card shadow-sm border-0">

            <div class="card-header d-flex justify-content-between align-items-center border-bottom mb-3 bg-white pt-4">
                <h5 class="mb-0 fw-bold">Data Soal Survey</h5>

                <a href="{{ route('addquestion') }}" class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm">
                    <i class="dripicons-plus me-1"></i> Buat Pertanyaan
                </a>
            </div>

            <div class="card-body">

                <div class="row mb-4 g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-bold small text-muted">Cari Soal:</label>
                        <input type="text" class="form-control" placeholder="Masukkan pertanyaan...">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-bold small text-muted">Tipe Soal:</label>
                        <select class="form-select">
                            <option value="">Semua Tipe</option>
                            <option value="multiple_choice">Multiple Choice</option>
                            <option value="essay">Essay</option>
                            <option value="rating">Rating</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-bold small text-muted">Status:</label>
                        <select class="form-select">
                            <option value="">Semua Status</option>
                            <option value="1">Aktif</option>
                            <option value="0">Non Aktif</option>
                        </select>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover table-striped align-middle" id="table1">
                        <thead class="table-light">
                            <tr>
                                <th width="5%">No</th>
                                <th width="30%">Soal</th>
                                <th width="15%">Kategori</th> <th width="10%">Kode</th>
                                <th width="10%">Tipe</th>
                                <th width="10%">Required</th>
                                <th width="5%">Status</th>
                                <th width="15%" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($soal as $index => $item)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $item->soal }}</td>
                                
                                {{-- Menampilkan Kategori --}}
                                <td>
                                    <span class="badge bg-light text-dark border">{{ $item->kategori ?? '-' }}</span>
                                </td>
                                
                                <td>{{ $item->kode ?? '-' }}</td>
                                
                                <td>
                                    @if($item->jenis_soal == 'multiple_choice')
                                        <span class="badge bg-info">Multiple Choice</span>
                                    @elseif($item->jenis_soal == 'essay')
                                        <span class="badge bg-secondary">Essay</span>
                                    @elseif($item->jenis_soal == 'rating')
                                        <span class="badge bg-dark">Rating</span>
                                    @endif
                                </td>
                                
                                <td>
                                    @if($item->is_required)
                                        <span class="badge bg-success">Yes</span>
                                    @else
                                        <span class="badge bg-light text-dark">No</span>
                                    @endif
                                </td>
                                
                                <td>
                                    @if($item->is_active)
                                        <span class="badge bg-success">Aktif</span>
                                    @else
                                        <span class="badge bg-danger">Non Aktif</span>
                                    @endif
                                </td>
                                
                                {{-- Kolom Aksi yang Diperbaiki --}}
                                <td>
                                    <div class="d-flex justify-content-center gap-2">
                                        <a href="{{ route('pertanyaan.edit', $item->id) }}" class="btn btn-warning btn-sm" title="Edit">
                                            <i class="dripicons-pencil"></i>
                                        </a>
                                        
                                        {{-- Class text-nowrap mencegah teks terputus ke bawah --}}
                                        <a href="{{ route('pertanyaan.switch', $item->id) }}" 
                                           class="btn {{ $item->is_active ? 'btn-outline-danger' : 'btn-outline-success' }} btn-sm text-nowrap" 
                                           title="{{ $item->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                                            <i class="{{ $item->is_active ? 'dripicons-power' : 'dripicons-checkmark' }}"></i>
                                            {{ $item->is_active ? 'Matikan' : 'Hidupkan' }}
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center py-4 text-muted">Data soal belum tersedia</td>
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