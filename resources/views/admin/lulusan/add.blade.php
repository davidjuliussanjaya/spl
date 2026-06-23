@extends('layouts.app')

@section('title', 'Tambah Data Lulusan')

@section('content')
<div class="page-heading">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold"><i class="bi bi-mortarboard-fill me-2 text-primary"></i>Tambah Data Lulusan</h4>
        <a href="javascript:history.back()" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
    </div>

    <section class="section">
        <form action="{{ route('lulusan.store') }}" method="POST">
            @csrf
            
            <div class="row">
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-body p-4">
                            <h6 class="fw-bold mb-4 text-uppercase small text-muted tracking-wider">Informasi Akademik</h6>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label small fw-bold">Nama Lengkap</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-0"><i class="bi bi-person text-muted"></i></span>
                                        <input type="text" name="nama" class="form-control modern-input" placeholder="Contoh: John Doe" required>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label small fw-bold">NIM</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-0"><i class="bi bi-hash text-muted"></i></span>
                                        <input type="text" name="nim" class="form-control modern-input" placeholder="Nomor Induk Mahasiswa" required>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label small fw-bold">Fakultas</label>
                                    <select name="fakultas" class="form-select modern-input" required>
                                        <option value="" selected disabled>Pilih Fakultas</option>
                                        <option value="FTI">Fakultas Teknologi dan Informatika</option>
                                        <option value="FDIK">Fakultas Desain dan Industri Kreatif</option>
                                        <option value="FEB">Fakultas Ekonomi dan Bisnis</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label small fw-bold">Program Studi</label>
                                    <select name="program_studi" class="form-select modern-input" required>
                                        <option value="" selected disabled>Pilih Prodi</option>
                                        <option value="Teknik Informatika">Teknik Informatika</option>
                                        <option value="Sistem Informasi">Sistem Informasi</option>
                                    </select>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label small fw-bold">Tahun Lulus</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-0"><i class="bi bi-calendar3 text-muted"></i></span>
                                        <input type="date" name="tahun_lulus" class="form-control modern-input" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-body p-4">
                            <h6 class="fw-bold mb-4 text-uppercase small text-muted tracking-wider">Pengaturan Data</h6>
                            
                            <div class="mb-4">
                                <label class="form-label small fw-bold">Perusahaan / Pengguna Lulusan</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0"><i class="bi bi-building text-muted"></i></span>
                                    <select name="pengguna_lulusan_id" class="form-select modern-input" required>
                                        <option value="" selected disabled>Pilih Perusahaan</option>
                                        @foreach($perusahaan as $item)
                                            <option value="{{ $item->id }}">{{ $item->nama_perusahaan }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <small class="text-muted" style="font-size: 0.7rem;">Pilih instansi yang menilai lulusan ini</small>
                            </div>

                            <div class="d-flex justify-content-between align-items-center p-3 bg-light rounded-3 mb-4">
                                <div>
                                    <span class="d-block fw-bold small">Status Lulusan</span>
                                    <small class="text-muted small">Aktifkan data ini</small>
                                </div>
                                <div class="form-check form-switch ms-3">
                                    <input class="form-check-input" type="checkbox" name="status" value="1" id="statusSwitch" checked>
                                </div>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary shadow-sm py-2 fw-bold rounded-3">
                                    <i class="bi bi-check-circle me-2"></i> Simpan Data
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </section>
</div>

<style>
    /* Tipografi & Spasi */
    .tracking-wider { letter-spacing: 0.05em; }
    
    /* Input Modern */
    .modern-input {
        border: 1.5px solid #f0f0f0;
        background-color: #fcfcfc;
        padding: 0.6rem 0.8rem;
        border-radius: 10px;
        transition: all 0.2s ease-in-out;
    }

    .modern-input:focus {
        background-color: #fff;
        border-color: #8B1A2A;
        box-shadow: 0 4px 12px rgba(67, 94, 190, 0.08);
    }

    .input-group-text {
        border-radius: 10px 0 0 10px !important;
    }

    .modern-input.form-control {
        border-radius: 0 10px 10px 0 !important;
    }

    /* Khusus select */
    select.modern-input {
        border-radius: 10px !important;
    }

    /* Switch Style */
    .form-check-input:checked {
        background-color: #8B1A2A;
        border-color: #8B1A2A;
    }

    .card {
        border-radius: 15px;
    }

    /* Hover effect pada button */
    .btn-primary {
        background-color: #8B1A2A;
        border: none;
    }
    .btn-primary:hover {
        background-color: #384ea1;
        transform: translateY(-1px);
    }
</style>
@endsection
