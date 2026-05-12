@extends('layouts.app')

@section('title', 'Edit Pengguna Lulusan')

@section('content')
<div class="page-heading">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold"><i class="bi bi-building me-2 text-primary"></i>Edit Instansi/Perusahaan</h4>
        <a href="{{ route('penggunalulusan') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
    </div>

    <section class="section">
        <form action="{{ route('penggunalulusan.update', $pengguna->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="row">
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-body p-4">
                            <h6 class="fw-bold mb-4 text-uppercase small text-muted tracking-wider">Informasi Perusahaan</h6>
                            
                            <div class="mb-3">
                                <label class="form-label small fw-bold">Nama Perusahaan/Instansi</label>
                                <input type="text" name="nama_perusahaan" class="form-control" placeholder="PT. Nama Perusahaan" value="{{ old('nama_perusahaan', $pengguna->nama_perusahaan) }}" required>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label small fw-bold">Jenis Perusahaan</label>
                                    <input type="text" name="jenis_perusahaan" class="form-control" placeholder="Contoh: BUMN, Swasta, Startup, Yayasan, dll." value="{{ old('jenis_perusahaan', $pengguna->jenis_perusahaan) }}" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label small fw-bold">Nomor Badan Hukum</label>
                                    <input type="text" name="nomor_badan_hukum" class="form-control" placeholder="No. AHU / NIB" value="{{ old('nomor_badan_hukum', $pengguna->nomor_badan_hukum) }}">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-bold">Alamat Perusahaan</label>
                                <textarea name="alamat_perusahaan" class="form-control" rows="3" placeholder="Jl. Alamat Lengkap...">{{ old('alamat_perusahaan', $pengguna->alamat_perusahaan) }}</textarea>
                            </div>
                        </div>
                    </div>

                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-4">
                            <h6 class="fw-bold mb-4 text-uppercase small text-muted tracking-wider">Kontak Penyelia (Atasan Langsung)</h6>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label small fw-bold">Nama Penyelia</label>
                                    <input type="text" name="nama_penyelia" class="form-control" placeholder="Nama Atasan" value="{{ old('nama_penyelia', $pengguna->nama_penyelia) }}" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label small fw-bold">Email Penyelia</label>
                                    <input type="email" name="email_penyelia" class="form-control" placeholder="email@perusahaan.com" value="{{ old('email_penyelia', $pengguna->email_penyelia) }}" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label small fw-bold">Nomor WhatsApp/HP</label>
                                    <input type="text" name="kontak_penyelia" class="form-control" placeholder="0812..." value="{{ old('kontak_penyelia', $pengguna->kontak_penyelia) }}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-body p-4">
                            <h6 class="fw-bold mb-4 text-uppercase small text-muted tracking-wider">Cakupan Wilayah</h6>
                            
                            <div class="form-check form-switch mb-3">
                                <input type="hidden" name="cabang_kota" value="0">
                                <input class="form-check-input" type="checkbox" name="cabang_kota" id="kota" value="1" {{ old('cabang_kota', $pengguna->cabang_kota) ? 'checked' : '' }}>
                                <label class="form-check-label ms-2" for="kota">Memiliki Cabang Nasional</label>
                            </div>

                            <div class="form-check form-switch mb-4">
                                <input type="hidden" name="cabang_negara" value="0">
                                <input class="form-check-input" type="checkbox" name="cabang_negara" id="negara" value="1" {{ old('cabang_negara', $pengguna->cabang_negara) ? 'checked' : '' }}>
                                <label class="form-check-label ms-2" for="negara">Memiliki Cabang Luar Negeri</label>
                            </div>

                            <hr>

                            <div class="d-grid mt-4">
                                <button type="submit" class="btn btn-primary py-2 fw-bold shadow-sm">
                                    <i class="bi bi-cloud-arrow-up me-2"></i> Perbarui Instansi
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
    .card { border-radius: 15px; }
    .form-control, .form-select {
        border-radius: 8px;
        padding: 0.6rem 0.8rem;
        border: 1px solid #dee2e6;
    }
    .form-control:focus, .form-select:focus {
        border-color: #435ebe;
        box-shadow: 0 0 0 0.25 row rgba(67, 94, 190, 0.1);
    }
    .tracking-wider { letter-spacing: 0.05em; }
</style>
@endsection
