@extends('layouts.app')

@section('title', 'Daftar Lulusan')

@section('content')
<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Data Lulusan</h3>
                <p class="text-subtitle text-muted">Manajemen data lulusan dan filter pencarian</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#">Lulusan</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Index</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <section class="section">
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center border-bottom mb-3">
                <h5 class="mb-0">Data Lulusan Universitas Dinamika</h5>
                <div>
                    <a href="#" class="btn btn-success btn-sm rounded-pill">
                        <i class="bi bi-file-earmark-excel"></i> Export Excel
                    </a>
                    <a href="{{ route('addgrad') }}" class="btn btn-primary btn-sm rounded-pill">
                        <i class="bi bi-plus-circle"></i> Tambah Lulusan
                    </a>
                </div>
            </div>
            
            <div class="card-body">
                <form action="{{ url()->current() }}" method="GET" class="row mb-4">
                    <div class="col-md-3 mb-3">
                        <label class="form-label small fw-bold">Nama Lulusan</label>
                        <input type="text" name="nama" class="form-control" value="{{ request('nama') }}" placeholder="Cari nama...">
                    </div>

                    <div class="col-md-3 mb-3">
                        <label class="form-label small fw-bold">NIM Lulusan</label>
                        <input type="text" name="nim" class="form-control" value="{{ request('nim') }}" placeholder="Cari NIM...">
                    </div>

                    <div class="col-md-2 mb-3">
                        <label class="form-label small fw-bold">Program Studi</label>
                        <select name="prodi" class="form-select">
                            <option value="Select">Semua</option>
                            <option value="Sistem Informasi" {{ request('prodi') == 'Sistem Informasi' ? 'selected' : '' }}>Sistem Informasi</option>
                            <option value="Teknik Informatika" {{ request('prodi') == 'Teknik Informatika' ? 'selected' : '' }}>Teknik Informatika</option>
                        </select>
                    </div>

                    <div class="col-md-2 mb-3">
                        <label class="form-label small fw-bold">Tahun Lulus (Range)</label>
                        <div class="input-group">
                            <input type="number" name="dari" class="form-control" placeholder="2020" value="{{ request('dari') }}">
                            <input type="number" name="sampai" class="form-control" placeholder="2024" value="{{ request('sampai') }}">
                        </div>
                    </div>

                    <div class="col-md-2 mb-3">
                        <label class="form-label small fw-bold">Status</label>
                        <select name="status_kerja" class="form-select">
                            <option value="Select">Semua</option>
                            <option value="Bekerja" {{ request('status_kerja') == 'Bekerja' ? 'selected' : '' }}>Bekerja</option>
                            <option value="Belum Bekerja" {{ request('status_kerja') == 'Belum Bekerja' ? 'selected' : '' }}>Belum Bekerja</option>
                        </select>
                    </div>

                    <div class="col-12 d-flex justify-content-end gap-2">
                        <a href="{{ url()->current() }}" class="btn btn-light-secondary btn-sm">Reset</a>
                        <button type="submit" class="btn btn-primary btn-sm px-4">Cari</button>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-striped" id="table1">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama</th>
                                <th>Nim</th>
                                <th>Prodi</th>
                                <th>Tahun Lulus</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($lulusan as $index => $data)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $data->nama }}</td>
                                <td>{{ $data->nim }}</td>
                                <td>{{ $data->program_studi }}</td>
                                <td>{{ $data->tahun_lulus->format('Y') }}</td>
                                <td>
                                    @if($data->status)
                                        <span class="badge bg-light-success">Bekerja</span>
                                    @else
                                        <span class="badge bg-light-danger">Belum Bekerja</span>
                                    @endif
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-info text-white"><i class="bi bi-eye"></i></button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center">Data tidak ditemukan</td>
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