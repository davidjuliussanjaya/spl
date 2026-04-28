@extends('layouts.app')

@section('title', 'Tambah Pengguna Lulusan')

@section('content')
<div class="page-heading">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold"><i class="bi bi-building me-2 text-primary"></i>Tambah Instansi/Perusahaan</h4>
        <a href="javascript:history.back()" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
    </div>

    <section class="section">
        <form action="{{ route('pengguna.store') }}" method="POST">
            @csrf
            <div class="row">
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-body p-4">
                            <h6 class="fw-bold mb-4 text-uppercase small text-muted tracking-wider">Informasi Perusahaan</h6>
                            
                            <div class="mb-3">
                                <label class="form-label small fw-bold">Nama Perusahaan/Instansi</label>
                                <input type="text" name="nama_perusahaan" class="form-control" placeholder="PT. Nama Perusahaan" required>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label small fw-bold">Jenis Perusahaan</label>
                                    <select name="jenis_perusahaan" class="form-select" required>
                                        <option value="" selected disabled>Pilih Jenis</option>
                                        <option value="government">Government (BUMN/Instansi)</option>
                                        <option value="private">Private (Swasta)</option>
                                        <option value="startup">Startup</option>
                                        <option value="nonprofit">Non-Profit (Yayasan)</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label small fw-bold">Nomor Badan Hukum</label>
                                    <input type="text" name="nomor_badan_hukum" class="form-control" placeholder="No. AHU / NIB">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-bold">Alamat Perusahaan</label>
                                <textarea name="alamat_perusahaan" class="form-control" rows="3" placeholder="Jl. Alamat Lengkap..."></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-4">
                            <h6 class="fw-bold mb-4 text-uppercase small text-muted tracking-wider">Kontak Penyelia (Atasan Langsung)</h6>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label small fw-bold">Nama Penyelia</label>
                                    <input type="text" name="nama_penyelia" class="form-control" placeholder="Nama Atasan" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label small fw-bold">Email Penyelia</label>
                                    <input type="email" name="email_penyelia" class="form-control" placeholder="email@perusahaan.com" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label small fw-bold">Nomor WhatsApp/HP</label>
                                    <input type="text" name="kontak_penyelia" class="form-control" placeholder="0812...">
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
                                <input class="form-check-input" type="checkbox" name="cabang_kota" id="kota" value="1">
                                <label class="form-check-label ms-2" for="kota">Memiliki Cabang Nasional</label>
                            </div>

                            <div class="form-check form-switch mb-4">
                                <input class="form-check-input" type="checkbox" name="cabang_negara" id="negara" value="1">
                                <label class="form-check-label ms-2" for="negara">Memiliki Cabang Luar Negeri</label>
                            </div>

                            <hr>

                            <div class="mb-3">
                                <label class="form-label small fw-bold">Durasi Rata-rata Bekerja (Bulan)</label>
                                <input type="number" name="durasi_lulusan_bekerja" class="form-control" placeholder="Contoh: 12">
                            </div>

                            <div class="d-grid mt-4">
                                <button type="submit" class="btn btn-primary py-2 fw-bold shadow-sm">
                                    <i class="bi bi-cloud-arrow-up me-2"></i> Simpan Instansi
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