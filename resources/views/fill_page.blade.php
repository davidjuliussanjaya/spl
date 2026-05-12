<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $survey->judul }} - Survey Kepuasan</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --dinamika-maroon: #9A031E; /* Warna khas Universitas Dinamika */
            --dinamika-maroon-dark: #6C0215;
            --dinamika-gold: #F4A261;
            --dinamika-light: #FFF5F5;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #f4f7f6;
            color: #333;
        }

        /* Container & Cards */
        .survey-container {
            max-width: 850px;
            margin: 0 auto;
        }
        .card-custom {
            background: #ffffff;
            border-radius: 16px;
            border: none;
            box-shadow: 0 4px 20px rgba(0,0,0,0.03);
            margin-bottom: 2rem;
            overflow: hidden;
        }

        /* Header Survey */
        .survey-header {
            background: linear-gradient(135deg, var(--dinamika-maroon) 0%, var(--dinamika-maroon-dark) 100%);
            color: #fff;
            padding: 3rem 2rem;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        /* Aksen kotak-kotak dekoratif ala logo Dinamika */
        .survey-header::before {
            content: '';
            position: absolute;
            top: -20%;
            right: -10%;
            width: 200px;
            height: 200px;
            background: rgba(255, 255, 255, 0.05);
            transform: rotate(45deg);
            z-index: 0;
        }
        .survey-header::after {
            content: '';
            position: absolute;
            bottom: -30%;
            left: -10%;
            width: 150px;
            height: 150px;
            background: rgba(255, 255, 255, 0.05);
            transform: rotate(45deg);
            z-index: 0;
        }
        .survey-header-content {
            position: relative;
            z-index: 1;
        }
        .survey-header h2 {
            font-weight: 800;
            margin-bottom: 0.5rem;
            letter-spacing: -0.5px;
        }
        .univ-badge {
            display: inline-block;
            background-color: rgba(255,255,255,0.15);
            padding: 0.4rem 1rem;
            border-radius: 50px;
            font-size: 0.85rem;
            font-weight: 600;
            letter-spacing: 1px;
            margin-bottom: 1.5rem;
            border: 1px solid rgba(255,255,255,0.2);
            text-transform: uppercase;
        }

        /* Info Box */
        .info-box {
            background-color: #f8fafc;
            border-radius: 12px;
            padding: 1.5rem;
            border: 1px solid #e2e8f0;
        }
        .info-label {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #64748b;
            font-weight: 600;
            margin-bottom: 0.25rem;
        }
        .info-value {
            font-weight: 600;
            color: #0f172a;
            margin-bottom: 1rem;
        }

        /* Utilities */
        .text-dinamika { color: var(--dinamika-maroon) !important; }
        .bg-dinamika-subtle { background-color: var(--dinamika-light) !important; color: var(--dinamika-maroon) !important;}

        /* Lulusan Card */
        .lulusan-card {
            background: linear-gradient(135deg, #fff5f6 0%, #fff 60%);
            border: 1.5px solid #f5c0c8;
            border-radius: 14px;
            padding: 1.75rem 1.5rem;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        .lulusan-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--dinamika-maroon), var(--dinamika-gold));
        }
        .lulusan-avatar {
            width: 64px;
            height: 64px;
            background: linear-gradient(135deg, var(--dinamika-maroon), #c0392b);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            box-shadow: 0 4px 14px rgba(154,3,30,0.25);
        }
        .lulusan-avatar i {
            font-size: 1.75rem;
            color: #fff;
        }
        .lulusan-name {
            font-size: 1.15rem;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 0.25rem;
        }
        .lulusan-nim {
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--dinamika-maroon);
            letter-spacing: 0.5px;
            margin-bottom: 1rem;
        }
        .lulusan-meta {
            display: inline-flex;
            align-items: center;
            gap: 0.75rem;
            background-color: #fff;
            border: 1px solid #f5c0c8;
            border-radius: 50px;
            padding: 0.4rem 1.25rem;
            font-size: 0.82rem;
            color: #475569;
            flex-wrap: wrap;
            justify-content: center;
        }
        .lulusan-meta-item {
            display: flex;
            align-items: center;
            gap: 0.35rem;
            font-weight: 500;
        }
        .lulusan-meta-item i {
            color: var(--dinamika-maroon);
            font-size: 0.85rem;
        }
        .lulusan-meta-divider {
            width: 1px;
            height: 14px;
            background-color: #f5c0c8;
        }

        /* Kategori Title */
        .kategori-title {
            font-weight: 700;
            color: #1e293b;
            background-color: #f1f5f9;
            padding: 1rem 1.5rem;
            border-radius: 10px;
            border-left: 5px solid var(--dinamika-maroon);
        }

        /* Form Elements */
        .form-control {
            border: 1.5px solid #cbd5e1;
            border-radius: 10px;
            padding: 0.75rem 1rem;
            transition: all 0.2s;
        }
        .form-control:focus {
            border-color: var(--dinamika-maroon);
            box-shadow: 0 0 0 4px rgba(154, 3, 30, 0.1);
        }

        /* Survey Table */
        .survey-table {
            border-collapse: collapse;
            font-size: 0.875rem;
        }
        .survey-table thead th {
            background-color: #fce8eb;
            color: #7a0117;
            text-align: center;
            vertical-align: middle;
            font-weight: 700;
            border-color: #f5c0c8;
            padding: 0.6rem 0.75rem;
        }
        .survey-table thead tr:first-child th {
            background-color: var(--dinamika-maroon);
            color: #fff;
            font-size: 0.95rem;
            text-align: left;
            letter-spacing: 0.3px;
            border-color: var(--dinamika-maroon-dark);
        }
        .survey-table tbody tr td {
            vertical-align: middle;
            border-color: #f5c0c8;
            padding: 0.6rem 0.75rem;
        }
        .survey-table tbody tr:hover td {
            background-color: #fff5f6;
        }
        .survey-table .td-no {
            text-align: center;
            font-weight: 600;
            color: var(--dinamika-maroon);
            width: 45px;
        }
        .survey-table .td-option {
            text-align: center;
            width: 65px;
        }
        .survey-table .td-option input[type="radio"] {
            width: 18px;
            height: 18px;
            cursor: pointer;
            accent-color: var(--dinamika-maroon);
        }
        .survey-table .th-option {
            min-width: 60px;
            line-height: 1.3;
        }
        .survey-table .th-option .score-badge {
            display: block;
            font-size: 0.7rem;
            font-weight: 400;
            opacity: 0.75;
        }

        /* Button */
        .btn-submit {
            background-color: var(--dinamika-maroon);
            color: white;
            border: none;
            border-radius: 10px;
            padding: 1rem 2rem;
            font-weight: 600;
            font-size: 1.1rem;
            width: 100%;
            transition: all 0.3s;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .btn-submit:hover {
            background-color: var(--dinamika-maroon-dark);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(154, 3, 30, 0.2);
            color: white;
        }

        .border-bottom-dashed { border-bottom: 2px dashed #e2e8f0; }
    </style>
</head>
<body>

    <div class="container py-5 survey-container">
        
        @if(session('error'))
            <div class="alert alert-danger shadow-sm rounded-3 border-0 mb-4">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
            </div>
        @endif

        <div class="card-custom">
            <div class="survey-header">
                <div class="survey-header-content">
                    <div class="univ-badge"><i class="bi bi-mortarboard-fill me-2 text-warning"></i> Universitas Dinamika</div>
                    <h2>{{ $survey->judul }}</h2>
                    <p class="text-white-50 mb-0">Kode Akses Sesi: <span class="fw-bold text-white tracking-widest">{{ $survey->access_code }}</span></p>
                </div>
            </div>

            <div class="p-4 p-md-5">
                @if($survey->deskripsi)
                    <div class="alert alert-light border mb-4 text-secondary" style="line-height: 1.6;">
                        <i class="bi bi-info-circle-fill text-dinamika me-2"></i> {{ $survey->deskripsi }}
                    </div>
                @endif

                <h5 class="fw-bold mb-4 text-dark">A. Informasi tentang Lulusan Terkait</h5>

                <div class="lulusan-card">
                    <div class="lulusan-avatar">
                        <i class="bi bi-person-badge-fill"></i>
                    </div>
                    <div class="lulusan-name">{{ $survey->lulusan->nama ?? '-' }}</div>
                    <div class="lulusan-nim">{{ $survey->lulusan->nim ?? '-' }}</div>
                    <div class="lulusan-meta">
                        <div class="lulusan-meta-item">
                            <i class="bi bi-building-fill-check"></i>
                            <span>{{ $survey->lulusan->program_studi ?? '-' }}</span>
                        </div>
                        <div class="lulusan-meta-divider"></div>
                        <div class="lulusan-meta-item">
                            <i class="bi bi-mortarboard-fill"></i>
                            <span>Lulus {{ $survey->lulusan->tahun_lulus ? \Carbon\Carbon::parse($survey->lulusan->tahun_lulus)->format('Y') : '-' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <form action="{{ route('survey.submit', $survey->access_code) }}" method="POST">
            @csrf
            
            <div class="card-custom p-4 p-md-5">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div>
                        <label class="form-label fs-5 fw-bold text-dark mb-0">Identitas Perusahaan <span class="text-danger">*</span></label>
                        <p class="text-muted small mb-0 mt-1">Data institusi/perusahaan Anda sudah terisi dari sistem.</p>
                    </div>
                </div>

                <div class="p-3 bg-light rounded border mb-3">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-building fs-3 text-secondary me-3"></i>
                        <div>
                            <div class="fw-bold">{{ $survey->penggunalulusan->nama_perusahaan ?? 'Nama Perusahaan Belum Tersedia' }}</div>
                            <div class="small text-muted">{{ $survey->penggunalulusan->alamat_perusahaan ?? 'Alamat belum tersedia' }}</div>
                        </div>
                    </div>
                </div>

                <button class="btn btn-outline-secondary btn-sm" type="button" data-bs-toggle="collapse" data-bs-target="#collapsePerusahaan" aria-expanded="false" aria-controls="collapsePerusahaan">
                    <i class="bi bi-pencil-square me-1"></i> Ubah Data Perusahaan Jika Tidak Sesuai
                </button>

                <div class="collapse mt-3" id="collapsePerusahaan">
                    <div class="row g-3 border-top pt-3 mt-1">
                        <div class="col-md-12">
                            <label class="form-label small text-secondary fw-bold mb-1">Nama Perusahaan <span class="text-danger">*</span></label>
                            <input type="text" name="nama_perusahaan" class="form-control" required placeholder="Nama Perusahaan..." value="{{ $survey->penggunalulusan->nama_perusahaan ?? '' }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small text-secondary fw-bold mb-1">Nomor Badan Hukum</label>
                            <input type="text" name="nomor_badan_hukum" class="form-control" placeholder="No. Badan Hukum..." value="{{ $survey->penggunalulusan->nomor_badan_hukum ?? '' }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small text-secondary fw-bold mb-1">Jenis Perusahaan</label>
                            <input type="text" name="jenis_perusahaan" class="form-control" placeholder="BUMN / Swasta / dll..." value="{{ $survey->penggunalulusan->jenis_perusahaan ?? '' }}">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label small text-secondary fw-bold mb-1">Alamat Perusahaan</label>
                            <textarea name="alamat_perusahaan" class="form-control" rows="2" placeholder="Alamat lengkap...">{{ $survey->penggunalulusan->alamat_perusahaan ?? '' }}</textarea>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small text-secondary fw-bold mb-1">Kontak Perusahaan</label>
                            <input type="text" name="kontak_perusahaan" class="form-control" placeholder="Telp/WA Perusahaan..." value="{{ $survey->penggunalulusan->kontak_perusahaan ?? '' }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small text-secondary fw-bold mb-1">Cabang Kota</label>
                            <input type="text" name="cabang_kota" class="form-control" placeholder="Kota..." value="{{ $survey->penggunalulusan->cabang_kota ?? '' }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small text-secondary fw-bold mb-1">Cabang Negara</label>
                            <input type="text" name="cabang_negara" class="form-control" placeholder="Negara..." value="{{ $survey->penggunalulusan->cabang_negara ?? '' }}">
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-custom p-4 p-md-5">
                <div class="mb-2">
                    <label class="form-label fs-5 fw-bold text-dark">Konfirmasi Identitas Responden <span class="text-danger">*</span></label>
                    <p class="text-muted small mb-3">Mohon konfirmasi atau lengkapi data Anda sebagai perwakilan instansi yang mengisi kuesioner ini.</p>
                </div>
                
                <div class="row g-3">
                    <div class="col-md-12">
                        <label class="form-label small text-secondary fw-bold mb-1">Nama Lengkap Penyelia <span class="text-danger">*</span></label>
                        <input type="text" name="nama_pengisi" class="form-control" required placeholder="Nama Anda..." value="{{ $survey->penggunalulusan->nama_penyelia ?? '' }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small text-secondary fw-bold mb-1">Nomor HP</label>
                        <input type="text" name="hp_pengisi" class="form-control" placeholder="08..." value="{{ $survey->penggunalulusan->kontak_penyelia ?? '' }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small text-secondary fw-bold mb-1">Alamat Email</label>
                        <input type="email" name="email_pengisi" class="form-control" placeholder="email@instansi.com" value="{{ $survey->penggunalulusan->email_penyelia ?? '' }}">
                    </div>
                </div>
            </div>

            @php
                $groupedSoal = $soal->groupBy(fn($s) => $s->kategori->nama_kategori ?? 'Lainnya');
            @endphp

            @foreach($groupedSoal as $namaKategori => $soalGroup)
                @php
                    $mcSoal    = $soalGroup->filter(fn($s) => $s->jenis_soal != 'essay')->values();
                    $essaySoal = $soalGroup->filter(fn($s) => $s->jenis_soal == 'essay')->values();
                    $firstMc   = $mcSoal->first();
                @endphp

                <div class="card-custom p-4 p-md-5">

                    @if($mcSoal->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered survey-table mb-0">
                                <thead>
                                    <tr>
                                        <th colspan="{{ 2 + ($firstMc ? $firstMc->jawaban->count() : 0) }}">
                                            {{ $namaKategori }}
                                        </th>
                                    </tr>
                                    <tr>
                                        <th class="td-no">No</th>
                                        <th>Pertanyaan</th>
                                        @if($firstMc)
                                            @foreach($firstMc->jawaban as $j)
                                                <th class="th-option">
                                                    {{ $j->jawaban }}
                                                    @if($j->nilai)
                                                        <span class="score-badge">{{ $j->nilai }}</span>
                                                    @endif
                                                </th>
                                            @endforeach
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($mcSoal as $s)
                                        <tr>
                                            <td class="td-no">{{ $loop->iteration }}</td>
                                            <td>
                                                {{ $s->soal }}
                                                @if($s->is_required) <span class="text-danger">*</span> @endif
                                            </td>
                                            @foreach($s->jawaban as $j)
                                                <td class="td-option">
                                                    <input type="radio"
                                                           name="jawaban[{{ $s->id }}]"
                                                           value="{{ $j->id }}"
                                                           {{ $s->is_required ? 'required' : '' }}>
                                                </td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif

                    @if($essaySoal->count() > 0)
                        <div class="{{ $mcSoal->count() > 0 ? 'mt-4 pt-4 border-top' : '' }}">
                            @if($mcSoal->count() == 0)
                                <div class="kategori-title mb-4">
                                    <h5 class="mb-0">{{ $namaKategori }}</h5>
                                </div>
                            @endif
                            @foreach($essaySoal as $s)
                                <div class="mb-4 {{ !$loop->last ? 'pb-4 border-bottom-dashed' : '' }}">
                                    <label class="form-label fs-6 mb-2 text-dark fw-bold">
                                        <span class="text-dinamika me-1">{{ $loop->iteration }}.</span> {{ $s->soal }}
                                        @if($s->is_required) <span class="text-danger">*</span> @endif
                                    </label>
                                    <textarea name="jawaban[{{ $s->id }}]" class="form-control" rows="3"
                                              placeholder="Tuliskan umpan balik Anda di sini..."
                                              {{ $s->is_required ? 'required' : '' }}></textarea>
                                </div>
                            @endforeach
                        </div>
                    @endif

                </div>
            @endforeach

            <div class="mt-4 mb-5">
                <button type="submit" class="btn-submit">
                    Kirim Kuesioner Evaluasi <i class="bi bi-send-check ms-2"></i>
                </button>
                <p class="text-center text-muted small mt-3">
                    <i class="bi bi-shield-lock-fill text-success"></i> Data umpan balik ini disimpan dengan aman dan rahasia.
                </p>
            </div>

        </form>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>