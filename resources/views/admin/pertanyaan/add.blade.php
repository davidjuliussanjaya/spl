@extends('layouts.app')

@section('title', 'Buat Pertanyaan Lulusan')

@section('content')
<div class="page-heading">
    <div class="page-title mb-4 d-flex justify-content-between align-items-center">
        <div>
            <h4 class="mb-1 fw-bold">Buat Pertanyaan Baru</h4>
            <p class="text-muted small mb-0">Tambahkan daftar pertanyaan untuk form survey lulusan.</p>
        </div>
        <div>
            <a href="javascript:history.back()" class="btn btn-light-secondary btn-sm rounded-pill px-3">
                <i class="bi bi-arrow-left me-1"></i> Kembali
            </a>
        </div>
    </div>

    <section class="section">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-4 p-md-5">
                <form action="{{ route('savequestion') }}" method="POST">
                    @csrf

                    <div class="mb-4">
                        <label class="form-label fw-bold text-dark">Soal Pertanyaan <span class="text-danger">*</span></label>
                        <textarea class="form-control modern-input" name="question" rows="3" placeholder="Tuliskan pertanyaan Anda di sini..." required></textarea>
                    </div>

                    <div class="row g-4 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-dark">Kategori Pertanyaan <span class="text-danger">*</span></label>
                            <select class="form-select modern-input" name="kategori_id" required>
                                <option value="" disabled selected>-- Pilih Kategori --</option>
                                @foreach($kategoris as $kategori)
                                    <option value="{{ $kategori->id }}">{{ $kategori->nama_kategori }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold text-dark">Peruntukan Fakultas <span class="text-danger">*</span></label>
                            <select class="form-select modern-input" name="peruntukan_fakultas" required>
                                <option value="Umum" selected>Umum (Semua Fakultas)</option>
                                <option value="FTI">Fakultas Teknologi dan Informatika (FTI)</option>
                                <option value="FDIK">Fakultas Desain dan Industri Kreatif (FDIK)</option>
                                <option value="FEB">Fakultas Ekonomi dan Bisnis (FEB)</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold text-dark">Tipe Masukan <span class="text-danger">*</span></label>
                            <select class="form-select modern-input" name="type" id="typeSelect">
                                <option value="radio">Pilihan Ganda (Radio / Rating)</option>
                                <option value="text">Teks Bebas (Essay)</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold text-dark">Kode Pertanyaan</label>
                            <input type="text" class="form-control modern-input" name="kode" placeholder="Contoh: f101, B1, C2...">
                            <small class="text-muted mt-1 d-block"><i class="bi bi-info-circle me-1"></i>Wajib diisi jika merujuk pada standar DIKTI.</small>
                        </div>

                        <div class="col-md-6 d-flex align-items-center pt-md-4">
                            <div class="form-check form-switch fs-5">
                                <input class="form-check-input" type="checkbox" name="required" id="requiredSwitch" checked>
                                <label class="form-check-label fs-6 text-dark ms-2 mt-1" for="requiredSwitch">Tandai Wajib Diisi (Required)</label>
                            </div>
                        </div>
                    </div>

                    <hr class="my-5 opacity-25">

                    <div id="pilihanJawabanGroup">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <label class="form-label fw-bold text-dark m-0">Atur Pilihan Jawaban</label>
                            <button type="button" id="addOption" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                <i class="bi bi-plus-circle me-1"></i> Tambah Opsi
                            </button>
                        </div>

                        <div class="bg-light p-4 rounded-4 border" style="border-style: dashed !important;">
                            <div id="options">
                                <div class="d-flex align-items-center mb-3 option-item">
                                    <div class="text-muted me-3"><i class="bi bi-record-circle"></i></div>
                                    <input type="text" name="jawaban[]" class="form-control modern-input me-2" placeholder="Teks Jawaban (Cth: Sangat Baik)">
                                    <input type="number" name="nilai[]" class="form-control modern-input me-2 text-center" style="width: 100px;" placeholder="Nilai" value="4" title="Bobot Nilai">
                                    <button type="button" class="btn btn-light-danger btn-sm rounded-circle px-2 py-1 removeOption" title="Hapus Opsi">
                                        <i class="bi bi-x-lg pointer-events-none"></i>
                                    </button>
                                </div>
                                <div class="d-flex align-items-center mb-3 option-item">
                                    <div class="text-muted me-3"><i class="bi bi-record-circle"></i></div>
                                    <input type="text" name="jawaban[]" class="form-control modern-input me-2" placeholder="Teks Jawaban (Cth: Baik)">
                                    <input type="number" name="nilai[]" class="form-control modern-input me-2 text-center" style="width: 100px;" placeholder="Nilai" value="3" title="Bobot Nilai">
                                    <button type="button" class="btn btn-light-danger btn-sm rounded-circle px-2 py-1 removeOption" title="Hapus Opsi">
                                        <i class="bi bi-x-lg pointer-events-none"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="text-end mt-5">
                        <button type="submit" class="btn btn-primary btn-lg rounded-pill px-5 shadow-sm fw-bold">
                            Simpan Pertanyaan <i class="bi bi-check2-circle ms-2"></i>
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </section>
</div>

<style>
    /* Styling Input Fields */
    .modern-input {
        border: 1.5px solid #e2e8f0;
        border-radius: 10px;
        padding: 0.6rem 1rem;
        background-color: #f8fafc;
        transition: all 0.3s ease;
    }
    .modern-input:focus {
        background-color: #ffffff;
        border-color: #8B1A2A;
        box-shadow: 0 0 0 4px rgba(67, 94, 190, 0.1);
    }

    /* Form Switch Customization */
    .form-switch .form-check-input {
        width: 2.5em;
        height: 1.25em;
        cursor: pointer;
    }
    .form-switch .form-check-input:checked {
        background-color: #8B1A2A;
        border-color: #8B1A2A;
    }

    /* Option Item Animation */
    .option-item {
        animation: fadeIn 0.3s ease-in-out;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* Custom Danger Button for Remove */
    .btn-light-danger {
        background-color: #fef2f2;
        color: #ef4444;
        border: 1px solid #fee2e2;
        transition: all 0.2s;
    }
    .btn-light-danger:hover {
        background-color: #ef4444;
        color: #ffffff;
    }
    
    .pointer-events-none {
        pointer-events: none;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const typeSelect = document.getElementById('typeSelect');
    const pilihanGroup = document.getElementById('pilihanJawabanGroup');

    // Toggle tampilan opsi jawaban berdasarkan tipe soal
    function togglePilihan() {
        if (typeSelect.value === 'text') {
            pilihanGroup.style.display = 'none';
        } else {
            pilihanGroup.style.display = 'block';
        }
    }

    togglePilihan(); // Eksekusi saat pertama load
    typeSelect.addEventListener('change', togglePilihan); // Eksekusi saat dropdown berubah
});

// Logic Tambah Opsi Jawaban
document.getElementById('addOption').addEventListener('click', function() {
    let container = document.getElementById('options');
    // Menghitung urutan nilai otomatis (default mengecil atau membesar)
    let index = container.children.length + 1;

    let html = `
        <div class="d-flex align-items-center mb-3 option-item">
            <div class="text-muted me-3"><i class="bi bi-record-circle"></i></div>
            <input type="text" name="jawaban[]" class="form-control modern-input me-2" placeholder="Teks Jawaban">
            <input type="number" name="nilai[]" class="form-control modern-input me-2 text-center" style="width: 100px;" placeholder="Nilai" value="${index}">
            <button type="button" class="btn btn-light-danger btn-sm rounded-circle px-2 py-1 removeOption" title="Hapus Opsi">
                <i class="bi bi-x-lg pointer-events-none"></i>
            </button>
        </div>
    `;

    container.insertAdjacentHTML('beforeend', html);
});

// Logic Hapus Opsi Jawaban (Event Delegation)
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('removeOption')) {
        e.target.parentElement.remove();
    }
});
</script>
@endsection