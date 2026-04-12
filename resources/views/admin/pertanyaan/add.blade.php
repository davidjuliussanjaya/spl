@extends('layouts.app')

@section('title', 'Buat Pertanyaan Lulusan')

@section('content')
<div class="page-heading">
    <div class="page-title mb-3">
        <div class="row">
            <div class="col-12">
                <h4>Buat Pertanyaan Lulusan</h4>
            </div>
        </div>
    </div>

    <section class="section">
        <div class="card">

            <!-- BODY -->
            <div class="card-body">
                <form action="{{ route('savequestion') }}" method="POST">
                    @csrf

                    <!-- Soal -->
                    <div class="form-group row mb-3">
                        <label class="col-md-3 col-form-label">Soal Pertanyaan:</label>
                        <div class="col-md-9">
                            <textarea class="form-control" name="question" placeholder="Question" required></textarea>
                        </div>
                    </div>

                    <!-- Required -->
                    <div class="form-group row mb-3">
                        <label class="col-md-3 col-form-label">Required:</label>
                        <div class="col-md-9">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="required" checked>
                                <label class="form-check-label">ON</label>
                            </div>
                        </div>
                    </div>

                    <!-- Tipe -->
                    <div class="form-group row mb-3">
                        <label class="col-md-3 col-form-label">Tipe Masukan:</label>
                        <div class="col-md-9">
                            <select class="form-select" name="type" id="typeSelect">
                                <option value="radio">Radio / Multiple Choice</option>
                                <option value="text">Text</option>
                            </select>
                        </div>
                    </div>

                    <!-- Kode -->
                    <div class="form-group row mb-3">
                        <label class="col-md-3 col-form-label">Kode Pertanyaan:</label>
                        <div class="col-md-9">
                            <input type="text" class="form-control" name="kode" placeholder="f101">
                            <small class="text-muted">Jika pertanyaan dari DIKTI wajib masukkan kode</small>
                        </div>
                    </div>

                    <!-- Pilihan Jawaban -->
                    <div class="form-group row mb-3" id="pilihanJawabanGroup">
                        <label class="col-md-3 col-form-label">Pilihan Jawaban:</label>
                        <div class="col-md-9">

                            <button type="button" id="addOption" class="btn btn-light-primary btn-sm mb-2">
                                Add
                            </button>

                            <div id="options">
                                <div class="d-flex align-items-center mb-2 option-item">
                                    <input type="radio" class="form-check-input me-2">
                                    <input type="text" name="jawaban[]" class="form-control me-2" placeholder="Jawaban">
                                    <input type="number" name="nilai[]" class="form-control me-2" style="width:80px" value="1">
                                    <button type="button" class="btn btn-danger btn-sm removeOption">x</button>
                                </div>

                                <div class="d-flex align-items-center mb-2 option-item">
                                    <input type="radio" class="form-check-input me-2">
                                    <input type="text" name="jawaban[]" class="form-control me-2" placeholder="Jawaban">
                                    <input type="number" name="nilai[]" class="form-control me-2" style="width:80px" value="2">
                                    <button type="button" class="btn btn-danger btn-sm removeOption">x</button>
                                </div>
                            </div>

                        </div>
                    </div>

                    <!-- BUTTON -->
                    <div class="text-end">
                        <a href="javascript:history.back()" class="btn btn-light-secondary btn-sm">
                            <i class="dripicons-chevron-left"></i> Kembali
                        </a>
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="dripicons-checkmark"></i> Simpan
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </section>
</div>

<!-- SCRIPT TAMBAH & HAPUS -->
 <script>
document.addEventListener('DOMContentLoaded', function() {
    const typeSelect = document.getElementById('typeSelect');
    const pilihanGroup = document.getElementById('pilihanJawabanGroup');

    function togglePilihan() {
        if (typeSelect.value === 'text') {
            pilihanGroup.style.display = 'none';
        } else {
            pilihanGroup.style.display = 'flex';
        }
    }

    // saat pertama load
    togglePilihan();

    // saat berubah
    typeSelect.addEventListener('change', togglePilihan);
});
</script>
<script>
document.getElementById('addOption').addEventListener('click', function() {
    let container = document.getElementById('options');

    let index = container.children.length + 1;

    let html = `
        <div class="d-flex align-items-center mb-2 option-item">
            <input type="radio" class="form-check-input me-2">
            <input type="text" name="jawaban[]" class="form-control me-2" placeholder="Jawaban">
            <input type="number" name="nilai[]" class="form-control me-2" style="width:80px" value="${index}">
            <button type="button" class="btn btn-danger btn-sm removeOption">x</button>
        </div>
    `;

    container.insertAdjacentHTML('beforeend', html);
});

document.addEventListener('click', function(e) {
    if (e.target.classList.contains('removeOption')) {
        e.target.parentElement.remove();
    }
});
</script>

@endsection