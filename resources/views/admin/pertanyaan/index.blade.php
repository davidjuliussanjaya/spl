@extends('layouts.app')

@section('title', 'Data Soal')

@section('content')
<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Data Pertanyaan</h3>
                <p class="text-subtitle text-muted">Daftar soal survey</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#">Survey</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Pertanyaan</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <section class="section">
        <div class="card">

            <!-- HEADER -->
            <div class="card-header d-flex justify-content-between align-items-center border-bottom mb-3">
                <h5 class="mb-0">Data Soal Survey</h5>

                <a href="{{ route('addquestion') }}" class="btn btn-primary btn-sm">
                    <i class="dripicons-plus"></i> Buat Pertanyaan
                </a>
            </div>

            <!-- BODY -->
            <div class="card-body">

                <!-- FILTER -->
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="form-label">Cari Soal:</label>
                        <input type="text" class="form-control" placeholder="Masukkan pertanyaan...">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Tipe Soal:</label>
                        <select class="form-select">
                            <option value="">Semua</option>
                            <option value="multiple_choice">Multiple Choice</option>
                            <option value="essay">Essay</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Status:</label>
                        <select class="form-select">
                            <option value="">Semua</option>
                            <option value="1">Aktif</option>
                            <option value="0">Non Aktif</option>
                        </select>
                    </div>
                </div>

                <!-- TABLE -->
                <table class="table table-striped" id="table1">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Soal</th>
                            <th>Kode</th>
                            <th>Tipe</th>
                            <th>Required</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>

                        @forelse($soal as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $item->soal }}</td>
                            <td>{{ $item->kode ?? '-' }}</td>
                            <td>
                                @if($item->jenis_soal == 'multiple_choice')
                                    <span class="badge bg-info">Multiple Choice</span>
                                @elseif($item->jenis_soal == 'essay')
                                    <span class="badge bg-secondary">Essay</span>
                                @endif
                            </td>
                            <td>
                                @if($item->is_required)
                                    <span class="badge bg-success">Yes</span>
                                @else
                                    <span class="badge bg-light">No</span>
                                @endif
                            </td>
                            <td>
                                @if($item->is_active)
                                    <span class="badge bg-success">Aktif</span>
                                @else
                                    <span class="badge bg-danger">Non Aktif</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('pertanyaan.edit', $item->id) }}" class="btn btn-warning btn-sm">
                                    <i class="dripicons-pencil"></i>
                                </a>
                                <a href="{{ route('pertanyaan.switch', $item->id) }}" 
                                class="btn {{ $item->is_active ? 'btn-outline-danger' : 'btn-outline-success' }} btn-sm" 
                                title="{{ $item->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                                    
                                    <i class="{{ $item->is_active ? 'dripicons-power' : 'dripicons-checkmark' }}"></i>
                                    {{ $item->is_active ? 'Matikan' : 'Hidupkan' }}
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center">Data soal belum tersedia</td>
                        </tr>
                        @endforelse

                    </tbody>
                </table>

            </div>
        </div>
    </section>
</div>
@endsection