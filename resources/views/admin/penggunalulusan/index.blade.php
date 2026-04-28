@extends('layouts.app')

@section('title', 'Daftar Pengguna Lulusan')

@section('content')
<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Pengguna Lulusan</h3>
                <p class="text-subtitle text-muted">Daftar perusahaan dan instansi penyerap lulusan</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Pengguna Lulusan</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <section class="section">
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Daftar Instansi / Perusahaan</h5>
                <a href="{{ route('create') }}" class="btn btn-primary btn-sm rounded-pill">
                    <i class="bi bi-plus-circle me-1"></i> Tambah Pengguna
                </a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped" id="table1">
                        <thead>
                            <tr>
                                <th>Perusahaan</th>
                                <th>Penyelia (Atasan)</th>
                                <th>Kontak/Email</th>
                                <th>Jenis</th>
                                <th>Cakupan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pengguna as $item)
                            <tr>
                                <td>
                                    <span class="fw-bold text-primary">{{ $item->nama_perusahaan }}</span><br>
                                    <small class="text-muted">{{ Str::limit($item->alamat_perusahaan, 40) }}</small>
                                </td>
                                <td>{{ $item->nama_penyelia }}</td>
                                <td>
                                    <i class="bi bi-envelope small"></i> {{ $item->email_penyelia }}<br>
                                    <i class="bi bi-telephone small"></i> {{ $item->kontak_penyelia ?? '-' }}
                                </td>
                                <td>
                                    @php
                                        $badgeClass = [
                                            'government' => 'bg-info',
                                            'private' => 'bg-primary',
                                            'startup' => 'bg-warning',
                                            'nonprofit' => 'bg-secondary'
                                        ][$item->jenis_perusahaan] ?? 'bg-light';
                                    @endphp
                                    <span class="badge {{ $badgeClass }}">{{ ucfirst($item->jenis_perusahaan) }}</span>
                                </td>
                                <td>
                                    @if($item->cabang_negara)
                                        <span class="badge bg-light-info text-info">Internasional</span>
                                    @elseif($item->cabang_kota)
                                        <span class="badge bg-light-primary text-primary">Nasional</span>
                                    @else
                                        <span class="badge bg-light-secondary text-secondary">Lokal</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a href="#" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                                        <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection