@extends('layouts.app')

@section('title', 'Tambah Survey')

@section('content')
<div class="page-heading">

    <div class="page-title mb-3">
        <h4>Form Survey</h4>
    </div>

    <section class="section">
<!-- {{ route('survey.store') }} -->
        <form action="" method="POST">
            @csrf

            <!-- ================= HEADER ================= -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-4">
                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                            <span class="fw-bold">A</span>
                        </div>
                        <h5 class="fw-bold m-0 text-dark">Informasi Institusi & Responden</h5>
                    </div>

                    <div class="row g-4">
                        <div class="col-lg-6 border-end-lg">
                            <h6 class="text-muted text-uppercase small fw-bold mb-3"><i class="bi bi-person me-2"></i>1. Identitas Responden</h6>
                            
                            <div class="mb-3">
                                <label class="form-label small text-secondary mb-1">Nama Lengkap</label>
                                <input type="text" name="nama" class="form-control line-input" placeholder="Masukkan nama sesuai KTP" required>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label small text-secondary mb-1">Nomor HP</label>
                                    <input type="text" name="hp" class="form-control line-input" placeholder="0812...">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label small text-secondary mb-1">Email</label>
                                    <input type="email" name="email" class="form-control line-input" placeholder="contoh@mail.com">
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <h6 class="text-muted text-uppercase small fw-bold mb-3"><i class="bi bi-building me-2"></i>2. Identitas Perusahaan</h6>
                            
                            <div class="mb-3">
                                <label class="form-label small text-secondary mb-1">Nama Instansi / Perusahaan</label>
                                <input type="text" name="nama_perusahaan" class="form-control line-input" placeholder="PT. Contoh Maju Jaya">
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label small text-secondary mb-1">Nomor Badan Hukum</label>
                                    <input type="text" name="badan_hukum" class="form-control line-input" placeholder="No. AHU / Akta">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label small text-secondary mb-1">Telepon Kantor</label>
                                    <input type="text" name="telp_perusahaan" class="form-control line-input" placeholder="(021) ...">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label small text-secondary mb-1">Alamat Lengkap Perusahaan</label>
                                <input type="text" name="alamat_perusahaan" class="form-control line-input" placeholder="Jl. Nama Jalan No. 123...">
                            </div>
                        </div>
                    </div>

                    <hr class="my-4 opacity-25">

                    <div class="row g-4">
                        <div class="col-md-4">
                            <label class="fw-bold small mb-2 d-block">e. Jenis Perusahaan</label>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" name="jenis[]" value="Pendidikan" id="checkEdu">
                                <label class="form-check-label" for="checkEdu">Pendidikan</label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input jenisCheck" type="checkbox" value="Industri" id="checkInd">
                                <label class="form-check-label" for="checkInd">Industri, sebutkan:</label>
                                <input type="text" name="industri_lain" class="line-input-sm w-100 mt-1" disabled>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label class="fw-bold small mb-2 d-block">f. Cabang di Kota Lain?</label>
                            <div class="d-flex gap-3 mt-2">
                                <div class="form-check">
                                    <input class="form-check-input cabangKota" type="radio" name="cabang_kota" value="ya" id="kotaYa">
                                    <label class="form-check-label" for="kotaYa">Ya</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input cabangKota" type="radio" name="cabang_kota" value="tidak" id="kotaTidak">
                                    <label class="form-check-label" for="kotaTidak">Tidak</label>
                                </div>
                            </div>
                            <input type="text" name="kota_lain" class="line-input-sm w-100 mt-2" placeholder="Sebutkan kota..." disabled>
                        </div>

                        <div class="col-md-4">
                            <label class="fw-bold small mb-2 d-block">g. Cabang Luar Negeri?</label>
                            <div class="d-flex gap-3 mt-2">
                                <div class="form-check">
                                    <input class="form-check-input cabangLuar" type="radio" name="cabang_luar" value="ya" id="luarYa">
                                    <label class="form-check-label" for="luarYa">Ya</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input cabangLuar" type="radio" name="cabang_luar" value="tidak" id="luarTidak">
                                    <label class="form-check-label" for="luarTidak">Tidak</label>
                                </div>
                            </div>
                            <input type="text" name="luar_negeri" class="line-input-sm w-100 mt-2" placeholder="Sebutkan negara..." disabled>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ================= SOAL DINAMIS ================= -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white pt-4 px-4 border-0">
                    <div class="d-flex align-items-center">
                        <div class="bg-dark text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                            <span class="fw-bold">B</span>
                        </div>
                        <h5 class="fw-bold m-0">Pertanyaan Survey</h5>
                    </div>
                    <p class="text-muted small ms-5 ps-2">Mohon isi jawaban sesuai dengan kondisi yang sebenarnya.</p>
                </div>

                <div class="card-body p-4">
                    @foreach($soal as $index => $s)
                    <div class="question-item mb-5 pb-4 {{ !$loop->last ? 'border-bottom-dashed' : '' }}">
                        <div class="d-flex">
                            <div class="question-number me-3 text-primary fw-bold fs-5">
                                {{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}.
                            </div>
                            <div class="flex-grow-1">
                                <label class="fw-bold text-dark fs-6 mb-3 d-block">
                                    {{ $s->soal }}
                                    @if($s->is_required)
                                        <span class="text-danger" title="Wajib diisi">*</span>
                                    @endif
                                </label>

                                <div class="ps-1">
                                    @if($s->jenis_soal == 'essay')
                                        <div class="mt-2">
                                            <textarea name="jawaban[{{ $s->id }}]" 
                                                    class="form-control modern-textarea" 
                                                    placeholder="Tuliskan jawaban Anda di sini..."
                                                    rows="3"
                                                    {{ $s->is_required ? 'required' : '' }}></textarea>
                                        </div>

                                    @else
                                        <div class="row g-2 mt-1"> @foreach($s->jawaban as $j)
                                            <div class="col-md-3 col-6"> <div class="option-card h-100">
                                                    <input type="radio" 
                                                        name="jawaban[{{ $s->id }}]" 
                                                        value="{{ $j->id }}" 
                                                        id="opt_{{ $s->id }}_{{ $j->id }}"
                                                        class="btn-check"
                                                        {{ $s->is_required ? 'required' : '' }}>
                                                    
                                                    <label class="btn btn-outline-light-custom w-100 h-100 text-start p-2 d-flex align-items-center" for="opt_{{ $s->id }}_{{ $j->id }}">
                                                        <div class="radio-indicator me-2 flex-shrink-0"></div>
                                                        <span class="text-dark small fw-medium text-truncate-2">{{ $j->jawaban }}</span>
                                                    </label>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach

                    <div class="mt-4 pt-3 border-top d-flex justify-content-between align-items-center">
                        <span class="text-muted small italic">* Menandakan kolom wajib diisi</span>
                        <button type="submit" class="btn btn-primary px-5 py-2 fw-bold shadow-sm rounded-pill">
                            Kirim Survey <i class="bi bi-send ms-2"></i>
                        </button>
                    </div>
                </div>
            </div>

        </form>

    </section>
</div>

<!-- ================= CSS ================= -->
<style>
/* Hilangkan border card dan gunakan bayangan halus */
.card {
    border-radius: 12px;
}

/* Style Input Garis Minimalis */
.line-input {
    border: none;
    border-bottom: 1.5px solid #dee2e6;
    border-radius: 0;
    padding: 0.5rem 0;
    background-color: transparent;
    transition: all 0.3s ease;
}

.line-input:focus {
    box-shadow: none;
    background-color: transparent;
    border-bottom-color: #435ebe; /* Warna Primary Laravel */
}

/* Input Kecil untuk keterangan */
.line-input-sm {
    border: none;
    border-bottom: 1px solid #dee2e6;
    background: transparent;
    font-size: 0.875rem;
    padding: 2px 0;
    transition: all 0.3s;
}

.line-input-sm:focus {
    outline: none;
    border-bottom-color: #435ebe;
}

/* Responsif border pemisah */
@media (max-width: 768px) {
    .col-6 {
        flex: 0 0 50%;
        max-width: 50%;
    }
}

/* Mempercantik checkbox/radio */
.form-check-input:checked {
    background-color: #435ebe;
    border-color: #435ebe;
}
</style>
<style>
/* Style Nomor Soal */
.question-number {
    font-family: 'Inter', sans-serif;
    opacity: 0.3;
}

/* Garis putus-putus antar soal */
.border-bottom-dashed {
    border-bottom: 1.5px dashed #e9ecef;
}

/* Textarea Modern */
.modern-textarea {
    border: 1.5px solid #eee;
    border-radius: 12px;
    padding: 15px;
    background-color: #fcfcfc;
    transition: all 0.3s;
}

.modern-textarea:focus {
    background-color: #fff;
    border-color: #435ebe;
    box-shadow: 0 4px 12px rgba(67, 94, 190, 0.08);
}

/* Custom Radio Button (Option Card) */
.option-card {
    position: relative;
}

/* Memastikan teks tidak merusak layout jika terlalu panjang */
.text-truncate-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;  
    overflow: hidden;
    line-height: 1.2;
    font-size: 0.85rem; /* Sedikit lebih kecil agar muat 4 kolom */
}

.btn-outline-light-custom {
    padding: 10px 8px !important;
    min-height: 50px;
    display: flex;
    align-items: center;
}

.btn-outline-light-custom:hover {
    border-color: #435ebe;
    background-color: rgba(67, 94, 190, 0.02);
}

.btn-check:checked + .btn-outline-light-custom {
    border-color: #435ebe;
    background-color: rgba(67, 94, 190, 0.05);
    box-shadow: 0 2px 8px rgba(67, 94, 190, 0.1);
}

/* Indikator bulat kustom */
.radio-indicator {
    width: 10px;
    height: 10px;
    border: 2px solid #ccc;
    border-radius: 50%;
}

.btn-check:checked + .btn-outline-light-custom .radio-indicator {
    background-color: #435ebe;
    border-color: #435ebe;
    box-shadow: 0 0 0 3px rgba(67, 94, 190, 0.2);
}

/* Button Kirim Modern */
.btn-primary {
    background-color: #435ebe;
    border: none;
    transition: transform 0.2s;
}
.btn-primary:hover {
    transform: translateY(-2px);
    background-color: #384ea1;
}
</style>
<!-- ================= JS ================= -->
<script>
document.querySelectorAll('.jenisCheck').forEach((el, i) => {
    el.addEventListener('change', function() {
        let input = this.parentElement.querySelector('input[type="text"]');
        input.disabled = !this.checked;
    });
});

document.querySelectorAll('.cabangKota').forEach(el => {
    el.addEventListener('change', function() {
        let input = document.querySelector('input[name="kota_lain"]');
        input.disabled = this.value !== 'ya';
    });
});

document.querySelectorAll('.cabangLuar').forEach(el => {
    el.addEventListener('change', function() {
        let input = document.querySelector('input[name="luar_negeri"]');
        input.disabled = this.value !== 'ya';
    });
});
</script>

@endsection