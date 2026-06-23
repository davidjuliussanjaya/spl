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
                                    @php
                                        $editJenisOptions = [
                                            'Teknologi Informasi / Software / Digital',
                                            'Keuangan / Perbankan / Bisnis',
                                            'Manufaktur / Industri',
                                            'Perdagangan / Retail / E-Commerce',
                                            'Media / Kreatif / Desain',
                                            'Jasa (konsultan, pendidikan, dll)',
                                            'Pemerintahan',
                                            'BUMN / BUMD',
                                        ];
                                        $editCurrentJenis = old('jenis_perusahaan', $pengguna->jenis_perusahaan ?? '');
                                        $editIsLainnya = $editCurrentJenis !== '' && !in_array($editCurrentJenis, $editJenisOptions);
                                    @endphp
                                    <select name="jenis_perusahaan" id="edit_jenis_select" class="form-select" required
                                            onchange="document.getElementById('edit_jenis_lainnya_wrap').style.display=(this.value==='_lainnya_'?'block':'none')">
                                        <option value="" disabled {{ !$editCurrentJenis ? 'selected' : '' }}>Pilih Jenis Perusahaan</option>
                                        <option value="Teknologi Informasi / Software / Digital" {{ $editCurrentJenis === 'Teknologi Informasi / Software / Digital' ? 'selected' : '' }}>Teknologi Informasi / Software / Digital</option>
                                        <option value="Keuangan / Perbankan / Bisnis" {{ $editCurrentJenis === 'Keuangan / Perbankan / Bisnis' ? 'selected' : '' }}>Keuangan / Perbankan / Bisnis</option>
                                        <option value="Manufaktur / Industri" {{ $editCurrentJenis === 'Manufaktur / Industri' ? 'selected' : '' }}>Manufaktur / Industri</option>
                                        <option value="Perdagangan / Retail / E-Commerce" {{ $editCurrentJenis === 'Perdagangan / Retail / E-Commerce' ? 'selected' : '' }}>Perdagangan / Retail / E-Commerce</option>
                                        <option value="Media / Kreatif / Desain" {{ $editCurrentJenis === 'Media / Kreatif / Desain' ? 'selected' : '' }}>Media / Kreatif / Desain</option>
                                        <option value="Jasa (konsultan, pendidikan, dll)" {{ $editCurrentJenis === 'Jasa (konsultan, pendidikan, dll)' ? 'selected' : '' }}>Jasa (konsultan, pendidikan, dll)</option>
                                        <option value="Pemerintahan" {{ $editCurrentJenis === 'Pemerintahan' ? 'selected' : '' }}>Pemerintahan</option>
                                        <option value="BUMN / BUMD" {{ $editCurrentJenis === 'BUMN / BUMD' ? 'selected' : '' }}>BUMN / BUMD</option>
                                        <option value="_lainnya_" {{ $editIsLainnya ? 'selected' : '' }}>Lainnya...</option>
                                    </select>
                                    <div id="edit_jenis_lainnya_wrap" class="mt-2 {{ $editIsLainnya ? '' : 'd-none' }}">
                                        <input type="text" id="edit_jenis_lainnya_text" class="form-control"
                                               placeholder="Sebutkan jenis perusahaan..."
                                               value="{{ $editIsLainnya ? $editCurrentJenis : '' }}"
                                               oninput="document.getElementById('edit_jenis_select').value='_lainnya_'">
                                    </div>
                                </div>
                                <script>
                                document.querySelector('form').addEventListener('submit', function(e) {
                                    var sel = document.getElementById('edit_jenis_select');
                                    var text = document.getElementById('edit_jenis_lainnya_text');
                                    if (sel.value === '_lainnya_') {
                                        if (!text.value.trim()) {
                                            e.preventDefault();
                                            text.focus();
                                            text.classList.add('is-invalid');
                                            return;
                                        }
                                        sel.value = text.value.trim();
                                    }
                                });
                                </script>
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
                                    <label class="form-label small fw-bold">Jabatan Penyelia</label>
                                    <input type="text" name="jabatan_penyelia" class="form-control" placeholder="Contoh: HRD Manager, Direktur..." value="{{ old('jabatan_penyelia', $pengguna->jabatan_penyelia) }}">
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
                            
                            <div class="mb-3">
                                <label class="form-label small fw-bold">Jumlah Cabang Nasional</label>
                                <input type="number" name="cabang_kota" class="form-control" min="0"
                                       placeholder="Contoh: 3"
                                       value="{{ old('cabang_kota', $pengguna->cabang_kota ?? 0) }}">
                            </div>

                            <div class="mb-4">
                                <label class="form-label small fw-bold">Jumlah Cabang Luar Negeri</label>
                                <input type="number" name="cabang_negara" class="form-control" min="0"
                                       placeholder="Contoh: 2"
                                       value="{{ old('cabang_negara', $pengguna->cabang_negara ?? 0) }}">
                            </div>

                            <hr>

                            <div class="mb-3">
                                <label class="form-label small fw-bold">Jumlah Lulusan yang Pernah Bekerja</label>
                                <input type="number" name="jumlah_lulusan" class="form-control" min="0"
                                       placeholder="Contoh: 5"
                                       value="{{ old('jumlah_lulusan', $pengguna->jumlah_lulusan) }}">
                                <div class="form-text">Diperbarui otomatis saat instansi mengisi survey.</div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-bold">Durasi Rata-rata Bekerja (Bulan)</label>
                                <input type="number" name="durasi_lulusan_bekerja" class="form-control" min="0"
                                       placeholder="Contoh: 12"
                                       value="{{ old('durasi_lulusan_bekerja', $pengguna->durasi_lulusan_bekerja) }}">
                            </div>

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
