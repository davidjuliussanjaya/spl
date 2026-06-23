@extends('layouts.app')

@section('title', 'Detail Lulusan')

@section('content')
<div class="page-heading">
    @php
        $namaFakultas = [
            'FTI'  => 'Fakultas Teknologi dan Informatika',
            'FDIK' => 'Fakultas Desain dan Industri Kreatif',
            'FEB'  => 'Fakultas Ekonomi dan Bisnis',
        ];
    @endphp

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold"><i class="bi bi-mortarboard-fill me-2 text-primary"></i>Detail Lulusan</h4>
        <a href="{{ route('lulusan') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
    </div>

    <section class="section">
        <div class="row">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body p-4">
                        <h6 class="fw-bold mb-4 text-uppercase small text-muted tracking-wider">Informasi Akademik</h6>

                        <div class="row mb-3">
                            <div class="col-sm-4 text-muted small fw-semibold">Nama Lengkap</div>
                            <div class="col-sm-8 fw-bold">{{ $lulusan->nama }}</div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-sm-4 text-muted small fw-semibold">NIM</div>
                            <div class="col-sm-8">{{ $lulusan->nim }}</div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-sm-4 text-muted small fw-semibold">Fakultas</div>
                            <div class="col-sm-8">{{ $namaFakultas[$lulusan->fakultas] ?? $lulusan->fakultas }}</div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-sm-4 text-muted small fw-semibold">Program Studi</div>
                            <div class="col-sm-8">{{ $lulusan->program_studi }}</div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-sm-4 text-muted small fw-semibold">Tahun Lulus</div>
                            <div class="col-sm-8">{{ $lulusan->tahun_lulus->format('Y') }}</div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-sm-4 text-muted small fw-semibold">Status</div>
                            <div class="col-sm-8">
                                @if($lulusan->status)
                                    <span class="badge bg-light-success">Bekerja</span>
                                @else
                                    <span class="badge bg-light-danger">Belum Bekerja</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body p-4">
                        <h6 class="fw-bold mb-4 text-uppercase small text-muted tracking-wider">Pengguna Lulusan</h6>

                        @if($lulusan->pengguna)
                            <div class="mb-2">
                                <span class="small text-muted d-block">Nama Perusahaan</span>
                                <span class="fw-bold">{{ $lulusan->pengguna->nama_perusahaan }}</span>
                            </div>
                            @if($lulusan->pengguna->nama_pic)
                            <div class="mb-2">
                                <span class="small text-muted d-block">PIC / Narahubung</span>
                                <span>{{ $lulusan->pengguna->nama_pic }}</span>
                            </div>
                            @endif
                            @if($lulusan->pengguna->email)
                            <div class="mb-2">
                                <span class="small text-muted d-block">Email</span>
                                <span>{{ $lulusan->pengguna->email }}</span>
                            </div>
                            @endif
                            @if($lulusan->pengguna->no_hp)
                            <div class="mb-2">
                                <span class="small text-muted d-block">No. HP</span>
                                <span>{{ $lulusan->pengguna->no_hp }}</span>
                            </div>
                            @endif
                        @else
                            <p class="text-muted small">Tidak ada data perusahaan terkait.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
