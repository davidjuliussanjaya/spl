@extends('layouts.app')

@section('title', 'Lulusan')

@section('content')
@php
    $namaFakultas = [
        'FTI' => 'Fakultas Teknologi dan Informatika',
        'FDIK' => 'Fakultas Desain dan Industri Kreatif',
        'FEB' => 'Fakultas Ekonomi dan Bisnis',
    ];
    $prodiList = ['Manajemen Informatika', 'Sistem Informasi', 'Teknik Informatika', 'Akuntansi', 'Ekonomi Pembangunan', 'Manajemen', 'Desain Komunikasi Visual', 'Ilmu Komunikasi', 'Jurnalistik'];
@endphp
<div class="page-heading">
    <div class="spl-page-header">
        <div>
            <nav class="spl-breadcrumb" aria-label="Breadcrumb"><a href="{{ route('dashboard') }}">Dashboard</a><span>/</span><span>Lulusan</span></nav>
            <h3>Data Lulusan</h3>
            <p>Temukan dan kelola profil lulusan Universitas Dinamika.</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('addgrad') }}" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Tambah lulusan</a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success spl-alert" role="status"><i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}</div>
    @endif

    <section class="card">
        <div class="card-header spl-toolbar">
            <div>
                <h4 class="spl-toolbar-title">Daftar lulusan <span class="spl-filter-count">{{ $lulusan->count() }} hasil</span></h4>
                <p class="spl-toolbar-subtitle">Gunakan pencarian dan filter untuk mempersempit data.</p>
            </div>
        </div>
        <form action="{{ route('lulusan') }}" method="GET" class="spl-filter-panel">
            <div class="row g-3 align-items-end">
                <div class="col-12 col-md-4 col-xl-3">
                    <label for="nama" class="form-label">Nama lulusan</label>
                    <input id="nama" type="search" name="nama" class="form-control" value="{{ request('nama') }}" placeholder="Cari nama">
                </div>
                <div class="col-6 col-md-3 col-xl-2">
                    <label for="nim" class="form-label">NIM</label>
                    <input id="nim" type="search" name="nim" class="form-control" value="{{ request('nim') }}" placeholder="Cari NIM">
                </div>
                <div class="col-6 col-md-3 col-xl-2">
                    <label for="fakultas" class="form-label">Fakultas</label>
                    <select id="fakultas" name="fakultas" class="form-select">
                        <option value="Select">Semua fakultas</option>
                        @foreach($namaFakultas as $kode => $label)
                            <option value="{{ $kode }}" @selected(request('fakultas') === $kode)>{{ $kode }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-4 col-xl-3">
                    <label for="prodi" class="form-label">Program studi</label>
                    <select id="prodi" name="prodi" class="form-select">
                        <option value="Select">Semua program studi</option>
                        @foreach($prodiList as $prodi)
                            <option value="{{ $prodi }}" @selected(request('prodi') === $prodi)>{{ $prodi }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-md-2 col-xl-1">
                    <label for="dari" class="form-label">Lulus dari</label>
                    <input id="dari" type="number" min="2000" max="2100" name="dari" class="form-control" value="{{ request('dari') }}" placeholder="2020">
                </div>
                <div class="col-6 col-md-2 col-xl-1">
                    <label for="sampai" class="form-label">Sampai</label>
                    <input id="sampai" type="number" min="2000" max="2100" name="sampai" class="form-control" value="{{ request('sampai') }}" placeholder="2026">
                </div>
                <div class="col-6 col-md-3 col-xl-2">
                    <label for="status_kerja" class="form-label">Status</label>
                    <select id="status_kerja" name="status_kerja" class="form-select">
                        <option value="Select">Semua status</option>
                        <option value="Bekerja" @selected(request('status_kerja') === 'Bekerja')>Bekerja</option>
                        <option value="Belum Bekerja" @selected(request('status_kerja') === 'Belum Bekerja')>Belum bekerja</option>
                    </select>
                </div>
                <div class="col-12 col-md-3 col-xl-2 spl-filter-actions">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-funnel"></i> Terapkan</button>
                    <a href="{{ route('lulusan') }}" class="btn btn-outline-secondary">Reset</a>
                </div>
            </div>
        </form>
        <div class="table-responsive">
            <table class="table spl-table" id="table1">
                <thead><tr><th>Lulusan</th><th>Fakultas</th><th>Program studi</th><th>Tahun lulus</th><th>Status</th><th class="text-center">Aksi</th></tr></thead>
                <tbody>
                    @forelse($lulusan as $data)
                        <tr>
                            <td><span class="spl-row-title">{{ $data->nama }}</span><span class="spl-row-meta">NIM {{ $data->nim }}</span></td>
                            <td><span class="badge bg-primary">{{ $data->fakultas }}</span><span class="spl-row-meta">{{ $namaFakultas[$data->fakultas] ?? $data->fakultas }}</span></td>
                            <td>{{ $data->program_studi }}</td>
                            <td>{{ $data->tahun_lulus?->format('Y') ?? '-' }}</td>
                            <td>@if($data->status)<span class="badge bg-success"><i class="bi bi-briefcase me-1"></i>Bekerja</span>@else<span class="badge bg-danger"><i class="bi bi-hourglass-split me-1"></i>Belum bekerja</span>@endif</td>
                            <td class="text-center"><a href="{{ route('lulusan.show', $data->id) }}" class="spl-icon-action" title="Lihat profil" aria-label="Lihat profil {{ $data->nama }}"><i class="bi bi-arrow-up-right"></i></a></td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="spl-empty"><i class="bi bi-search"></i>Data lulusan tidak ditemukan. Coba ubah kata pencarian atau filter.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>
@endsection
