@extends('layouts.app')

@section('title', 'Tambah Survey')

@section('content')
    <div class="page-heading">
        <div class="page-title mb-3">
            <h4>Form Survey</h4>
        </div>

        <section class="section">
            <form action="{{ route('survey.store') }}" method="POST">
                @csrf

                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-4">
                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3"
                                style="width: 40px; height: 40px;">
                                <span class="fw-bold">A</span>
                            </div>
                            <h5 class="fw-bold m-0 text-dark">Informasi Utama Survey</h5>
                        </div>

                        <div class="row">
                            <div class="col-md-12 mb-4">
                                <label class="form-label small text-secondary mb-1 fw-bold text-uppercase">Judul Survey
                                    <span class="text-danger">*</span></label>
                                <input type="text" name="judul" class="form-control line-input fs-5"
                                    placeholder="Contoh: Survey Kepuasan Pengguna Lulusan 2026" required
                                    value="{{ old('judul') }}">
                            </div>

                            <div class="col-md-12 mb-2">
                                <label class="form-label small text-secondary mb-1 fw-bold text-uppercase">Deskripsi /
                                    Instruksi Tambahan (Opsional)</label>
                                <textarea name="deskripsi" class="form-control modern-textarea" rows="3"
                                    placeholder="Tambahkan deskripsi atau instruksi pengisian survey untuk perusahaan...">{{ old('deskripsi') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-4">
                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3"
                                style="width: 40px; height: 40px;">
                                <span class="fw-bold">B</span>
                            </div>
                            <h5 class="fw-bold m-0 text-dark">Informasi Institusi & Responden</h5>
                        </div>

                        <div class="row g-4">
                            {{-- Sisi Kiri: Identitas Lulusan --}}
                            <div class="col-lg-6 border-end-lg">
                                <h6 class="text-muted text-uppercase small fw-bold mb-3"><i class="bi bi-person me-2"></i>1.
                                    Identitas Alumni / Lulusan</h6>

                                <div class="mb-3">
                                    <label class="form-label small text-secondary mb-1">Pilih Data Lulusan <span
                                            class="text-danger">*</span></label>
                                    <select name="lulusan_id" class="form-select line-input" required>
                                        <option value="">-- Pilih Alumni/Lulusan --</option>
                                        @foreach($lulusan as $l)
                                            <option value="{{ $l->id }}">{{ $l->nama }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label small text-secondary mb-1">Nama Lengkap Penyelia
                                        (Responden)</label>
                                    {{-- Hapus readonly --}}
                                    <input type="text" name="nama" class="form-control line-input"
                                        placeholder="Otomatis terisi atau ketik manual...">
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label small text-secondary mb-1">Nomor HP</label>
                                        {{-- Hapus readonly --}}
                                        <input type="text" name="hp" class="form-control line-input"
                                            placeholder="Otomatis terisi atau ketik manual...">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label small text-secondary mb-1">Email</label>
                                        {{-- Hapus readonly --}}
                                        <input type="email" name="email" class="form-control line-input"
                                            placeholder="Otomatis terisi atau ketik manual...">
                                    </div>
                                </div>
                            </div>

                            {{-- Sisi Kanan: Identitas Perusahaan --}}
                            <div class="col-lg-6">
                                <h6 class="text-muted text-uppercase small fw-bold mb-3"><i
                                        class="bi bi-building me-2"></i>2. Identitas Perusahaan</h6>

                                <div class="mb-3">
                                    <label class="form-label small text-secondary mb-1">Cari / Pilih Instansi <span
                                            class="text-danger">*</span></label>
                                    <select name="pengguna_lulusan_id" id="select_perusahaan" class="form-select line-input"
                                        required>
                                        <option value="">-- Pilih Perusahaan --</option>
                                        @foreach($perusahaan as $p)
                                            <option value="{{ $p->id }}">{{ $p->nama_perusahaan }}</option>
                                        @endforeach
                                    </select>
                                    <input type="hidden" name="nama_perusahaan" id="nama_perusahaan_hidden">
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label small text-secondary mb-1">Nomor Badan Hukum</label>
                                        {{-- Hapus readonly --}}
                                        <input type="text" name="badan_hukum" class="form-control line-input"
                                            placeholder="Otomatis terisi atau ketik manual...">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label small text-secondary mb-1">Telepon Kantor</label>
                                        {{-- Hapus readonly --}}
                                        <input type="text" name="telp_perusahaan" class="form-control line-input"
                                            placeholder="Otomatis terisi atau ketik manual...">
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label small text-secondary mb-1">Alamat Lengkap Perusahaan</label>
                                    {{-- Hapus readonly --}}
                                    <input type="text" name="alamat_perusahaan" class="form-control line-input"
                                        placeholder="Otomatis terisi atau ketik manual...">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white pt-4 px-4 border-0">
                        <div class="d-flex align-items-center">
                            <div class="bg-dark text-white rounded-circle d-flex align-items-center justify-content-center me-3"
                                style="width: 40px; height: 40px;">
                                <span class="fw-bold">C</span>
                            </div>
                            <h5 class="fw-bold m-0">Pilih Pertanyaan yang Digunakan</h5>
                        </div>
                        <p class="text-muted small ms-5 ps-2">Centang pertanyaan yang akan muncul di halaman survey
                            perusahaan.</p>
                    </div>

                    <div class="card-body p-4">
                        <div class="table-responsive">
                            @php
                                $badgeFakultas = ['Umum'=>'secondary','FTI'=>'primary','FDIK'=>'warning','FEB'=>'success'];
                            @endphp
                            <table class="table table-hover border">
                                <thead class="table-light">
                                    <tr>
                                        <th width="50" class="text-center">
                                            <input type="checkbox" id="checkAll" class="form-check-input">
                                        </th>
                                        <th>Pertanyaan</th>
                                        <th width="110">Tipe Soal</th>
                                        <th width="100">Peruntukan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($daftarSoal as $s)
                                        <tr>
                                            <td class="text-center">
                                                <input type="checkbox" name="soal_pilihan[]" value="{{ $s->id }}"
                                                    class="form-check-input soal-checkbox">
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
                                            <td>
                                                @php $pf = $s->peruntukan_fakultas ?? 'Umum'; @endphp
                                                <span class="badge bg-{{ $badgeFakultas[$pf] ?? 'secondary' }}">
                                                    {{ $pf }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-4 pt-3 border-top d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary px-5 py-2 fw-bold shadow-sm rounded-pill">
                                Simpan & Generate Kode <i class="bi bi-key ms-2"></i>
                            </button>
                        </div>
                    </div>
                </div>

            </form>
        </section>
    </div>

    <style>
        /* Card & General */
        .card {
            border-radius: 12px;
        }

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
            border-bottom-color: #8B1A2A;
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
            border-color: #8B1A2A;
            box-shadow: 0 4px 12px rgba(67, 94, 190, 0.08);
        }

        /* Checkbox & Button */
        .form-check-input:checked {
            background-color: #8B1A2A;
            border-color: #8B1A2A;
        }

        .btn-primary {
            background-color: #8B1A2A;
            border: none;
            transition: transform 0.2s;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            background-color: #384ea1;
        }

        /* Responsive Utilities */
        @media (min-width: 992px) {
            .border-end-lg {
                border-right: 1px solid #dee2e6;
            }
        }
    </style>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function () {
            // 1. Select2 Init
            $('#select_perusahaan').select2({
                theme: 'bootstrap-5',
                placeholder: '-- Pilih Perusahaan --',
                width: '100%'
            });

            // 2. Logic Check All Pertanyaan
            $('#checkAll').on('change', function () {
                $('.soal-checkbox').prop('checked', this.checked);
            });

            // 3. Auto-fill Data via AJAX saat Perusahaan dipilih
            $('#select_perusahaan').on('change', function () {
                let id = $(this).val();

                if (id) {
                    $.ajax({
                        url: '/get-perusahaan/' + id,
                        type: 'GET',
                        dataType: 'json',
                        success: function (data) {
                            // Isi Field Penyelia (Kiri)
                            $('input[name="nama"]').val(data.nama_penyelia);
                            $('input[name="hp"]').val(data.kontak_penyelia);
                            $('input[name="email"]').val(data.email_penyelia);

                            // Isi Field Perusahaan (Kanan)
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