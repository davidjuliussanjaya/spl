@extends('layouts.app')

@section('title', 'Buat Survey Massal')

@section('content')
<div class="page-heading">
    <div class="page-title mb-3">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h4>Buat Survey Massal</h4>
                <p class="text-subtitle text-muted">Buat survey sekaligus untuk semua lulusan berdasarkan tahun lulus.</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('survey') }}">Survey</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Buat Massal</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <section class="section">
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <form action="{{ route('survey.bulk.store') }}" method="POST" id="bulkForm">
            @csrf

            {{-- Card A: Info Survey --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-4">
                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3"
                            style="width: 40px; height: 40px;">
                            <span class="fw-bold">A</span>
                        </div>
                        <h5 class="fw-bold m-0 text-dark">Informasi Survey</h5>
                    </div>

                    <div class="row">
                        <div class="col-md-12 mb-4">
                            <label class="form-label small text-secondary mb-1 fw-bold text-uppercase">
                                Judul Survey <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="judul" class="form-control line-input fs-5 @error('judul') is-invalid @enderror"
                                placeholder="Contoh: Survey Kepuasan Pengguna Lulusan 2024" required
                                value="{{ old('judul') }}">
                            @error('judul')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-12 mb-2">
                            <label class="form-label small text-secondary mb-1 fw-bold text-uppercase">
                                Deskripsi / Instruksi (Opsional)
                            </label>
                            <textarea name="deskripsi" class="form-control modern-textarea" rows="3"
                                placeholder="Tambahkan instruksi pengisian survey untuk perusahaan...">{{ old('deskripsi') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Card B: Pilih Tahun Lulus --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-4">
                        <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center me-3"
                            style="width: 40px; height: 40px;">
                            <span class="fw-bold">B</span>
                        </div>
                        <h5 class="fw-bold m-0 text-dark">Pilih Tahun Lulus</h5>
                    </div>

                    <div class="row align-items-end">
                        <div class="col-md-4 mb-3">
                            <label class="form-label small text-secondary mb-1 fw-bold text-uppercase">
                                Tahun Lulus <span class="text-danger">*</span>
                            </label>
                            <select name="tahun_lulus" id="tahun_lulus" class="form-select line-input @error('tahun_lulus') is-invalid @enderror" required>
                                <option value="">-- Pilih Tahun --</option>
                                @foreach($tahunList as $tahun)
                                    <option value="{{ $tahun }}" {{ old('tahun_lulus') == $tahun ? 'selected' : '' }}>
                                        {{ $tahun }}
                                    </option>
                                @endforeach
                            </select>
                            @error('tahun_lulus')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <button type="button" id="btn_preview" class="btn btn-outline-success px-4">
                                <i class="bi bi-search me-1"></i> Lihat Preview Lulusan
                            </button>
                        </div>
                    </div>

                    {{-- Preview tabel lulusan --}}
                    <div id="preview_lulusan" class="d-none mt-3">
                        <div class="alert alert-info d-flex align-items-center mb-3" id="preview_info">
                            <i class="bi bi-info-circle-fill me-2"></i>
                            <span id="preview_count_text"></span>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm">
                                <thead class="table-light">
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Lulusan</th>
                                        <th>NIM</th>
                                        <th>Program Studi</th>
                                        <th>Perusahaan</th>
                                    </tr>
                                </thead>
                                <tbody id="preview_tbody"></tbody>
                            </table>
                        </div>
                    </div>

                    <div id="no_lulusan" class="d-none">
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            Tidak ada lulusan dengan tahun tersebut yang memiliki data perusahaan terhubung.
                        </div>
                    </div>
                </div>
            </div>

            {{-- Card C: Pilih Pertanyaan --}}
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white pt-4 px-4 border-0">
                    <div class="d-flex align-items-center">
                        <div class="bg-dark text-white rounded-circle d-flex align-items-center justify-content-center me-3"
                            style="width: 40px; height: 40px;">
                            <span class="fw-bold">C</span>
                        </div>
                        <h5 class="fw-bold m-0">Pilih Pertanyaan yang Digunakan</h5>
                    </div>
                    <p class="text-muted small ms-5 ps-2">
                        Pilih pertanyaan yang ingin dimasukkan. Pertanyaan berlabel <span class="badge bg-secondary">Umum</span> akan muncul di semua survey.
                        Pertanyaan berlabel <span class="badge bg-primary">FTI</span> / <span class="badge bg-warning text-dark">FDIK</span> / <span class="badge bg-success">FEB</span>
                        hanya akan ditambahkan ke survey lulusan dari fakultas yang sesuai.
                    </p>
                </div>

                <div class="card-body p-4">
                    @error('soal_pilihan')
                        <div class="alert alert-danger">{{ $message }}</div>
                    @enderror

                    @php
                        $badgeFakultas = ['Umum'=>'secondary','FTI'=>'primary','FDIK'=>'warning','FEB'=>'success'];
                    @endphp

                    <div class="table-responsive">
                        <table class="table table-hover border">
                            <thead class="table-light">
                                <tr>
                                    <th width="50" class="text-center">
                                        <input type="checkbox" id="checkAll" class="form-check-input">
                                    </th>
                                    <th>Pertanyaan</th>
                                    <th width="110">Tipe Soal</th>
                                    <th width="120">Peruntukan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($daftarSoal as $s)
                                    @php $pf = $s->peruntukan_fakultas ?? 'Umum'; @endphp
                                    <tr>
                                        <td class="text-center">
                                            <input type="checkbox" name="soal_pilihan[]" value="{{ $s->id }}"
                                                class="form-check-input soal-checkbox"
                                                {{ in_array($s->id, old('soal_pilihan', [])) ? 'checked' : '' }}>
                                        </td>
                                        <td>{{ $s->soal }}</td>
                                        <td>
                                            <span class="badge {{ $s->jenis_soal == 'essay' ? 'bg-info' : 'bg-success' }}">
                                                {{ ucfirst($s->jenis_soal) }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $badgeFakultas[$pf] ?? 'secondary' }} {{ $pf === 'FDIK' ? 'text-dark' : '' }}">
                                                {{ $pf }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4 pt-3 border-top d-flex justify-content-between align-items-center">
                        <a href="{{ route('survey') }}" class="btn btn-outline-secondary px-4">
                            <i class="bi bi-arrow-left me-1"></i> Kembali
                        </a>
                        <button type="submit" class="btn btn-primary px-5 py-2 fw-bold shadow-sm rounded-pill" id="btn_submit" disabled>
                            <i class="bi bi-send me-2"></i> Buat Survey untuk Semua Lulusan
                        </button>
                    </div>
                </div>
            </div>

        </form>
    </section>
</div>

<style>
    .card { border-radius: 12px; }
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
        border-bottom-color: #8B1A2A;
    }
    .modern-textarea {
        border: 1.5px solid #eee;
        border-radius: 12px;
        padding: 15px;
        background-color: #fcfcfc;
        transition: all 0.3s;
    }
    .modern-textarea:focus {
        background-color: #fff;
        border-color: #8B1A2A;
        box-shadow: 0 4px 12px rgba(67,94,190,0.08);
    }
    .form-check-input:checked { background-color: #8B1A2A; border-color: #8B1A2A; }
    .btn-primary { background-color: #8B1A2A; border: none; transition: transform 0.2s; }
    .btn-primary:hover { transform: translateY(-2px); background-color: #384ea1; }
    .btn-primary:disabled { background-color: #9aa7d4; transform: none; cursor: not-allowed; }
</style>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function () {
    $('#checkAll').on('change', function () {
        $('.soal-checkbox').prop('checked', this.checked);
    });

    $('#btn_preview').on('click', function () {
        const tahun = $('#tahun_lulus').val();
        if (!tahun) {
            alert('Pilih tahun lulus terlebih dahulu.');
            return;
        }

        $.ajax({
            url: '{{ route("survey.lulusan-by-tahun") }}',
            type: 'GET',
            data: { tahun: tahun },
            success: function (data) {
                $('#preview_lulusan').addClass('d-none');
                $('#no_lulusan').addClass('d-none');

                if (data.length === 0) {
                    $('#no_lulusan').removeClass('d-none');
                    $('#btn_submit').prop('disabled', true);
                    return;
                }

                $('#preview_count_text').text(
                    'Ditemukan ' + data.length + ' lulusan tahun ' + tahun + '. Survey akan dibuat untuk masing-masing lulusan.'
                );

                let rows = '';
                data.forEach(function (l, i) {
                    const perusahaan = l.pengguna ? l.pengguna.nama_perusahaan : '-';
                    rows += `<tr>
                        <td>${i + 1}</td>
                        <td>${l.nama}</td>
                        <td>${l.nim}</td>
                        <td>${l.program_studi}</td>
                        <td>${perusahaan}</td>
                    </tr>`;
                });

                $('#preview_tbody').html(rows);
                $('#preview_lulusan').removeClass('d-none');
                $('#btn_submit').prop('disabled', false);
            },
            error: function () {
                alert('Gagal memuat data lulusan.');
            }
        });
    });
});
</script>
@endsection
