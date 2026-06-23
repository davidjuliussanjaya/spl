@extends('layouts.app')

@section('title', 'Detail Survey')

@section('content')

{{-- Variabel Penentu Status: Jika true, form akan dikunci --}}
@php
    $isLocked = $survey->is_completed;
    $disabledAttr = $isLocked ? 'disabled' : '';
    // Ambil ID soal yang sebelumnya sudah dipilih oleh admin
    $selectedSoalIds = $survey->soals->pluck('id')->toArray();
@endphp

<div class="page-heading">
    <div class="page-title mb-3 d-flex justify-content-between align-items-center">
        <div>
            <h4 class="mb-1">Detail Form Survey</h4>
            @if($isLocked)
                <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i> Telah Diisi oleh Responden</span>
            @else
                <span class="badge bg-warning text-dark"><i class="bi bi-clock me-1"></i> Menunggu Pengisian (Bisa Diedit)</span>
            @endif
        </div>
        <div>
            <a href="{{ route('survey') }}" class="btn btn-secondary btn-sm"><i class="bi bi-arrow-left"></i> Kembali</a>
        </div>
    </div>

    <section class="section">
        {{-- Action mengarah ke route UPDATE (Jangan lupa buat route PUT di web.php) --}}
        <form action="{{ route('survey.update', $survey->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-4">
                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                            <span class="fw-bold">A</span>
                        </div>
                        <h5 class="fw-bold m-0 text-dark">Judul & Deskripsi</h5>
                    </div>

                    <div class="row">
                        <div class="col-md-12 mb-4">
                            <label class="form-label small text-secondary mb-1 fw-bold text-uppercase">Judul Survey <span class="text-danger">*</span></label>
                            <input type="text" name="judul" class="form-control line-input fs-5" placeholder="Contoh: Survey Kepuasan..." required value="{{ old('judul', $survey->judul) }}" {{ $disabledAttr }}>
                        </div>

                        <div class="col-md-12 mb-2">
                            <label class="form-label small text-secondary mb-1 fw-bold text-uppercase">Deskripsi / Instruksi Tambahan (Opsional)</label>
                            <textarea name="deskripsi" class="form-control modern-textarea" rows="3" {{ $disabledAttr }}>{{ old('deskripsi', $survey->deskripsi) }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-4">
                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                            <span class="fw-bold">B</span>
                        </div>
                        <h5 class="fw-bold m-0 text-dark">Informasi Institusi & Responden</h5>
                    </div>

                    <div class="row g-4">
                        {{-- Sisi Kiri: Identitas Lulusan --}}
                        <div class="col-lg-6 border-end-lg">
                            <h6 class="text-muted text-uppercase small fw-bold mb-3"><i class="bi bi-person me-2"></i>1. Identitas Alumni / Lulusan</h6>
                            
                            <div class="mb-3">
                                <label class="form-label small text-secondary mb-1">Pilih Data Lulusan <span class="text-danger">*</span></label>
                                <select name="lulusan_id" class="form-select line-input" required {{ $disabledAttr }}>
                                    <option value="">-- Pilih Alumni/Lulusan --</option>
                                    @foreach($lulusan as $l)
                                        <option value="{{ $l->id }}" {{ $survey->lulusan_id == $l->id ? 'selected' : '' }}>{{ $l->nama }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label small text-secondary mb-1">Nama Lengkap Penyelia (Responden)</label>
                                <input type="text" name="nama" class="form-control line-input" value="{{ $survey->penggunalulusan->nama_penyelia ?? '' }}" {{ $disabledAttr }}>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label small text-secondary mb-1">Nomor HP</label>
                                    <input type="text" name="hp" class="form-control line-input" value="{{ $survey->penggunalulusan->kontak_penyelia ?? '' }}" {{ $disabledAttr }}>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label small text-secondary mb-1">Email</label>
                                    <input type="email" name="email" class="form-control line-input" value="{{ $survey->penggunalulusan->email_penyelia ?? '' }}" {{ $disabledAttr }}>
                                </div>
                            </div>
                        </div>

                        {{-- Sisi Kanan: Identitas Perusahaan --}}
                        <div class="col-lg-6">
                            <h6 class="text-muted text-uppercase small fw-bold mb-3"><i class="bi bi-building me-2"></i>2. Identitas Perusahaan</h6>
                            
                            <div class="mb-3">
                                <label class="form-label small text-secondary mb-1">Cari / Pilih Instansi <span class="text-danger">*</span></label>
                                <select name="penggunalulusan_id" id="select_perusahaan" class="form-select line-input" required {{ $disabledAttr }}>
                                    <option value="">-- Pilih Perusahaan --</option>
                                    @foreach($perusahaan as $p)
                                        <option value="{{ $p->id }}" {{ $survey->penggunalulusan_id == $p->id ? 'selected' : '' }}>{{ $p->nama_perusahaan }}</option>
                                    @endforeach
                                </select>
                                <input type="hidden" name="nama_perusahaan" id="nama_perusahaan_hidden" value="{{ $survey->penggunalulusan->nama_perusahaan ?? '' }}" {{ $disabledAttr }}>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label small text-secondary mb-1">Nomor Badan Hukum</label>
                                    <input type="text" name="badan_hukum" class="form-control line-input" value="{{ $survey->penggunalulusan->nomor_badan_hukum ?? '' }}" {{ $disabledAttr }}>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label small text-secondary mb-1">Telepon Kantor</label>
                                    <input type="text" name="telp_perusahaan" class="form-control line-input" value="{{ $survey->penggunalulusan->kontak_perusahaan ?? '' }}" {{ $disabledAttr }}>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label small text-secondary mb-1">Alamat Lengkap Perusahaan</label>
                                <input type="text" name="alamat_perusahaan" class="form-control line-input" value="{{ $survey->penggunalulusan->alamat_perusahaan ?? '' }}" {{ $disabledAttr }}>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white pt-4 px-4 border-0">
                    <div class="d-flex align-items-center">
                        <div class="bg-dark text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                            <span class="fw-bold">C</span>
                        </div>
                        <h5 class="fw-bold m-0">Pertanyaan yang Digunakan</h5>
                    </div>
                    <p class="text-muted small ms-5 ps-2">Daftar pertanyaan untuk sesi survey perusahaan ini.</p>
                </div>

                <div class="card-body p-4">
                    <div class="table-responsive">
                        <table class="table table-hover border">
                            <thead class="table-light">
                                <tr>
                                    <th width="50" class="text-center">
                                        <input type="checkbox" id="checkAll" class="form-check-input" {{ $disabledAttr }}>
                                    </th>
                                    <th>Pertanyaan</th>
                                    <th width="150">Tipe Soal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($daftarSoal as $s)
                                <tr>
                                    <td class="text-center">
                                        <input type="checkbox" name="soal_pilihan[]" value="{{ $s->id }}" class="form-check-input soal-checkbox" 
                                        {{ in_array($s->id, $selectedSoalIds) ? 'checked' : '' }} {{ $disabledAttr }}>
                                    </td>
                                    <td>{{ $s->soal }}</td>
                                    <td>
                                        @if($s->jenis_soal === 'rating')
                                            <span class="badge bg-success">Rating</span>
                                        @elseif($s->jenis_soal === 'multiple_choice')
                                            <span class="badge bg-primary">Multiple Choice</span>
                                        @else
                                            <span class="badge bg-info">Essay</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- TOMBOL SUBMIT HANYA MUNCUL JIKA SURVEY BELUM DIISI --}}
                    @if(!$isLocked)
                        <div class="mt-4 pt-3 border-top d-flex justify-content-end">
                            <button type="submit" class="btn btn-warning px-5 py-2 fw-bold shadow-sm rounded-pill text-dark">
                                Simpan Perubahan <i class="bi bi-pencil-square ms-2"></i>
                            </button>
                        </div>
                    @else
                        <div class="mt-4 pt-3 border-top text-center">
                            <p class="text-muted italic"><i class="bi bi-lock-fill"></i> Data ini tidak dapat diubah karena perusahaan telah mengisi jawaban survey.</p>
                        </div>
                    @endif
                </div>
            </div>

        </form>

        {{-- SECTION D: Hasil Jawaban Responden (hanya tampil jika survey sudah selesai) --}}
        @if($isLocked && isset($responGrouped) && $responGrouped->isNotEmpty())
        <div class="card border-0 shadow-sm mt-4">
            <div class="card-header bg-white pt-4 px-4 border-0">
                <div class="d-flex align-items-center">
                    <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center me-3"
                         style="width: 40px; height: 40px;">
                        <span class="fw-bold">D</span>
                    </div>
                    <div>
                        <h5 class="fw-bold m-0">Hasil Jawaban Responden</h5>
                        <p class="text-muted small mb-0 mt-1">Jawaban yang telah diisi oleh perusahaan / instansi.</p>
                    </div>
                </div>
            </div>
            <div class="card-body p-4">

                @php
                    $soalDipakai = $survey->soals->sortBy(fn($s) => $s->kode ?? $s->id);
                    $groupedByKategori = $soalDipakai->groupBy(fn($s) => $s->kategori->nama_kategori ?? 'Lainnya');
                @endphp

                @foreach($groupedByKategori as $namaKategori => $soalGroup)
                    <div class="mb-4">
                        <div class="fw-bold text-uppercase small text-muted mb-3 pb-1 border-bottom"
                             style="letter-spacing: 1px;">
                            {{ $namaKategori }}
                        </div>

                        @foreach($soalGroup as $s)
                            @php $responses = $responGrouped->get($s->id, collect()); @endphp
                            <div class="d-flex gap-3 mb-3 pb-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                                <span class="badge bg-secondary text-white flex-shrink-0 mt-1"
                                      style="min-width: 40px; height: fit-content;">
                                    {{ $s->kode ?? '#' }}
                                </span>
                                <div class="flex-grow-1">
                                    <p class="fw-semibold text-dark mb-2 small">{{ $s->soal }}</p>

                                    @if($responses->isEmpty())
                                        <span class="text-muted fst-italic small">Tidak dijawab</span>

                                    @elseif($s->jenis_soal === 'rating')
                                        @php $r = $responses->first(); @endphp
                                        @if($r->jawaban)
                                            @php
                                                $n = (int) $r->jawaban->nilai;
                                                $nilaiColor = $n === 4 ? 'bg-success'
                                                    : ($n === 3 ? 'bg-primary'
                                                    : ($n === 2 ? 'bg-warning text-dark'
                                                    : ($n === 1 ? 'bg-danger' : 'bg-secondary')));
                                            @endphp
                                            <span class="badge {{ $nilaiColor }}">
                                                {{ $r->jawaban->jawaban }} ({{ $r->jawaban->nilai }})
                                            </span>
                                        @else
                                            <span class="text-muted fst-italic small">-</span>
                                        @endif

                                    @elseif($s->jenis_soal === 'multiple_choice')
                                        <ul class="mb-0 ps-3 small">
                                            @foreach($responses as $r)
                                                @if($r->jawaban_id && $r->jawaban)
                                                    <li>
                                                        {{ $r->jawaban->jawaban }}
                                                        @if($r->jawaban->nilai)
                                                            <span class="text-muted">({{ $r->jawaban->nilai }})</span>
                                                        @endif
                                                    </li>
                                                @elseif($r->jawaban_text)
                                                    <li class="fst-italic text-secondary">
                                                        Lainnya: {{ $r->jawaban_text }}
                                                    </li>
                                                @endif
                                            @endforeach
                                        </ul>

                                    @elseif($s->jenis_soal === 'essay')
                                        @php $r = $responses->first(); @endphp
                                        <div class="bg-light border rounded p-2 small text-secondary"
                                             style="white-space: pre-line;">{{ $r->jawaban_text ?? '-' }}</div>

                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endforeach

            </div>
        </div>
        @endif

    </section>
</div>

<style>
    /* Card & General */
    .card { border-radius: 12px; }
    
    /* Input Garis Minimalis */
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
        border-bottom-color: #435ebe;
    }
    
    /* Styling khusus input disabled agar tidak terlihat mati total */
    .line-input:disabled, .modern-textarea:disabled, .form-select:disabled {
        background-color: transparent;
        color: #6c757d;
        border-bottom-style: dashed;
        opacity: 0.8;
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

    /* Responsive Utilities */
    @media (min-width: 992px) {
        .border-end-lg { border-right: 1px solid #dee2e6; }
    }
</style>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
$(document).ready(function() {
    // 1. Select2 Init (Disable jika isLocked true)
    $('#select_perusahaan').select2({
        theme: 'bootstrap-5',
        placeholder: '-- Pilih Perusahaan --',
        width: '100%',
        disabled: {{ $isLocked ? 'true' : 'false' }}
    });

    // 2. Logic Check All Pertanyaan
    $('#checkAll').on('change', function() {
        if(!{{ $isLocked ? 'true' : 'false' }}) {
            $('.soal-checkbox').prop('checked', this.checked);
        }
    });

    // 3. Auto-fill Data via AJAX saat Perusahaan dipilih
    $('#select_perusahaan').on('change', function() {
        let id = $(this).val();
        
        if (id && !{{ $isLocked ? 'true' : 'false' }}) {
            $.ajax({
                url: '/get-perusahaan/' + id, 
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    $('input[name="nama"]').val(data.nama_penyelia);
                    $('input[name="hp"]').val(data.kontak_penyelia);
                    $('input[name="email"]').val(data.email_penyelia);

                    $('#nama_perusahaan_hidden').val(data.nama_perusahaan);
                    $('input[name="badan_hukum"]').val(data.nomor_badan_hukum);
                    $('input[name="telp_perusahaan"]').val(data.kontak_perusahaan);
                    $('input[name="alamat_perusahaan"]').val(data.alamat_perusahaan);
                }
            });
        }
    });
});
</script>
@endsection