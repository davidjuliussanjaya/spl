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
            --spl-brand: #1f5ed8;
            --spl-brand-dark: #163f98;
            --spl-brand-deep: #102d68;
            --spl-brand-soft: #edf4ff;
            --spl-accent: #ed1c24;
            --spl-border: #d9e2ef;
            --spl-text: #172033;
            --spl-muted: #5f6c80;
            --spl-surface: #ffffff;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: radial-gradient(circle at top right, #e8f0ff 0, transparent 29rem), #f6f8fc;
            color: var(--spl-text);
            font-size: 0.9375rem;
            line-height: 1.55;
        }

        /* Container & Cards */
        .survey-container {
            max-width: 920px;
            margin: 0 auto;
        }
        .card-custom {
            background: var(--spl-surface);
            border-radius: 18px;
            border: 1px solid var(--spl-border);
            box-shadow: 0 12px 30px rgba(16, 45, 104, .07);
            margin-bottom: 1.5rem;
            overflow: hidden;
        }

        /* Header Survey */
        .survey-header {
            background: linear-gradient(135deg, var(--spl-brand-deep) 0%, var(--spl-brand-dark) 48%, var(--spl-brand) 100%);
            color: #fff;
            padding: 2.75rem 2rem 2.5rem;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        .survey-header::before {
            content: '';
            position: absolute;
            inset: 0 0 auto;
            height: 4px;
            background: var(--spl-accent);
            z-index: 0;
        }
        .survey-header::after {
            content: '';
            position: absolute;
            right: -5rem;
            bottom: -8rem;
            width: 23rem;
            height: 23rem;
            border: 2.5rem solid rgba(255, 255, 255, .055);
            border-radius: 50%;
            z-index: 0;
        }
        .survey-header-content {
            position: relative;
            z-index: 1;
        }
        .survey-header h1 {
            max-width: 760px;
            margin: 0 auto .85rem;
            font-size: clamp(1.65rem, 3.7vw, 2.4rem);
            font-weight: 800;
            letter-spacing: -.035em;
            line-height: 1.18;
        }
        .survey-header p.text-white-50 {
            color: #dce9ff !important;
            font-size: .84rem;
            font-weight: 500;
            line-height: 1.5;
        }
        .survey-header p.text-white-50 + p.text-white-50 { margin-top: .3rem; }
        .survey-header p.text-white-50 .text-white { color: #fff !important; letter-spacing: .05em; }
        .univ-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: min(280px, 76vw);
            min-height: 86px;
            background: #fff;
            padding: .85rem 1.15rem;
            border-radius: 15px;
            margin-bottom: 1.35rem;
            border: 1px solid rgba(255,255,255,.75);
            box-shadow: 0 10px 22px rgba(5, 19, 49, .24);
        }
        .univ-logo {
            display: block;
            width: 100%;
            max-width: 230px;
            height: auto;
        }
        .survey-eyebrow {
            display: inline-flex;
            align-items: center;
            gap: .45rem;
            margin-bottom: .7rem;
            color: #dce9ff;
            font-size: .72rem;
            font-weight: 700;
            letter-spacing: .11em;
            text-transform: uppercase;
        }
        .survey-eyebrow::before { width: 22px; height: 2px; background: var(--spl-accent); content: ''; }
        .survey-header-meta { display: flex; justify-content: center; gap: .5rem; flex-wrap: wrap; }
        .survey-header-meta span {
            display: inline-flex;
            align-items: center;
            gap: .35rem;
            padding: .35rem .65rem;
            border: 1px solid rgba(255,255,255,.17);
            border-radius: 999px;
            background: rgba(255,255,255,.08);
            color: #e4eeff;
            font-size: .78rem;
            font-weight: 500;
        }
        .survey-header-meta strong { color: #fff; font-size: .8rem; letter-spacing: .04em; }

        /* Info Box */
        .info-box {
            background-color: #f7faff;
            border-radius: 14px;
            padding: 1.5rem;
            border: 1px solid #dce7f7;
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
        .text-dinamika { color: var(--spl-brand) !important; }
        .bg-dinamika-subtle { background-color: var(--spl-brand-soft) !important; color: var(--spl-brand) !important;}

        /* Lulusan Card */
        .lulusan-card {
            background: linear-gradient(135deg, #f2f7ff 0%, #fff 68%);
            border: 1px solid #cdddf5;
            border-radius: 16px;
            padding: 1.55rem 1.5rem;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        .lulusan-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--spl-accent), var(--spl-brand));
        }
        .lulusan-avatar {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, var(--spl-brand), #4a83ec);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            box-shadow: 0 4px 14px rgba(37, 99, 235, .25);
        }
        .lulusan-avatar i {
            font-size: 1.75rem;
            color: #fff;
        }
        .lulusan-name {
            font-size: 1.1rem;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 0.25rem;
        }
        .lulusan-nim {
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--spl-brand);
            letter-spacing: 0.5px;
            margin-bottom: 1rem;
        }
        .lulusan-meta {
            display: inline-flex;
            align-items: center;
            gap: 0.75rem;
            background-color: #fff;
            border: 1px solid #bfdbfe;
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
            color: var(--spl-brand);
            font-size: 0.85rem;
        }
        .lulusan-meta-divider {
            width: 1px;
            height: 14px;
            background-color: #bfdbfe;
        }

        /* Kategori Title */
        .kategori-title {
            font-weight: 700;
            color: var(--spl-brand-deep);
            background-color: var(--spl-brand-soft);
            padding: .9rem 1.1rem;
            border-radius: 11px;
            border-left: 4px solid var(--spl-accent);
        }

        /* Form Elements */
        .form-control {
            border: 1px solid #bdcadd;
            border-radius: 10px;
            padding: .68rem .85rem;
            color: var(--spl-text);
            font-size: .9rem;
            transition: all 0.2s;
        }
        .form-control:focus {
            border-color: var(--spl-brand);
            box-shadow: 0 0 0 3px rgba(31, 94, 216, .14);
        }

        /* Survey Table */
        .survey-table {
            border-collapse: collapse;
            font-size: .86rem;
        }
        .survey-table thead th {
            background-color: #edf4ff;
            color: var(--spl-brand-deep);
            text-align: center;
            vertical-align: middle;
            font-weight: 700;
            border-color: #d5e2f6;
            padding: .68rem .75rem;
        }
        .survey-table thead tr:first-child th {
            background-color: var(--spl-brand-deep);
            color: #fff;
            font-size: .92rem;
            text-align: left;
            letter-spacing: 0.3px;
            border-color: var(--spl-brand-deep);
        }
        .survey-table tbody tr td {
            vertical-align: middle;
            border-color: #d5e2f6;
            padding: .7rem .75rem;
        }
        .survey-table tbody tr:hover td {
            background-color: var(--spl-brand-soft);
        }
        .survey-table .td-no {
            text-align: center;
            font-weight: 600;
            color: var(--spl-brand);
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
            accent-color: var(--spl-brand);
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

        /* Multiple Choice */
        .mc-option .form-check-input {
            width: 18px;
            height: 18px;
            cursor: pointer;
            accent-color: var(--spl-brand);
        }
        .mc-option .form-check-label {
            font-size: 0.9rem;
            cursor: pointer;
            padding-top: 1px;
        }
        .mc-other-input {
            border: 1.5px solid #cbd5e1;
            border-radius: 8px;
            padding: 0.45rem 0.85rem;
            font-size: 0.875rem;
            transition: all 0.2s;
        }
        .mc-other-input:focus {
            border-color: var(--spl-brand);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, .12);
            outline: none;
        }
        .mc-other-input:disabled {
            background-color: #f8fafc;
            cursor: not-allowed;
        }

        /* Button */
        .btn-submit {
            background: linear-gradient(135deg, var(--spl-brand-dark), var(--spl-brand));
            color: white;
            border: none;
            border-radius: 12px;
            padding: .95rem 2rem;
            font-weight: 700;
            font-size: .95rem;
            width: 100%;
            transition: all 0.3s;
            text-transform: uppercase;
            letter-spacing: .045em;
        }
        .btn-submit:hover {
            background: var(--spl-brand-deep);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(37, 99, 235, .22);
            color: white;
        }

        .border-bottom-dashed { border-bottom: 2px dashed #e2e8f0; }
        .section-heading {
            display: flex;
            align-items: center;
            gap: .7rem;
            margin: 0 0 1.25rem;
            color: var(--spl-brand-deep);
            font-size: 1.16rem;
            font-weight: 800;
            letter-spacing: -.015em;
        }
        .section-heading::before {
            width: 8px;
            height: 28px;
            border-radius: 99px;
            background: linear-gradient(var(--spl-accent), var(--spl-brand));
            content: '';
        }
        .section-description { color: var(--spl-muted); font-size: .84rem; line-height: 1.55; margin: -.8rem 0 1.25rem 1rem; }
        .company-summary {
            display: flex;
            align-items: center;
            gap: .9rem;
            margin-bottom: 1rem;
            padding: 1rem;
            border: 1px solid #d9e5f7;
            border-radius: 13px;
            background: #f7faff;
        }
        .company-summary-icon {
            display: grid;
            flex: 0 0 42px;
            width: 42px;
            height: 42px;
            place-items: center;
            border-radius: 11px;
            background: var(--spl-brand-soft);
            color: var(--spl-brand);
            font-size: 1.15rem;
        }
        .company-summary-name { color: var(--spl-text); font-size: .94rem; font-weight: 700; }
        .company-summary-address { color: var(--spl-muted); font-size: .79rem; line-height: 1.45; margin-top: .15rem; }
        .company-stat { margin-left: auto; min-width: 92px; padding-left: .9rem; border-left: 1px solid #d6e1f2; text-align: center; }
        .company-stat strong { display: block; color: var(--spl-brand-deep); font-size: 1.38rem; font-weight: 800; line-height: 1; }
        .company-stat span { display: block; color: var(--spl-muted); font-size: .68rem; line-height: 1.35; margin-top: .3rem; }
        .employee-count-box { padding: 1rem; border: 1px solid var(--spl-border); border-radius: 12px; background: #fff; }

        /* Layar kecil: pertahankan ruang baca dan cegah isi survei terpotong. */
        @media (max-width: 575.98px) {
            .survey-container { padding-left: .875rem; padding-right: .875rem; }
            .card-custom { margin-bottom: 1rem; }
            .survey-header { padding: 2rem 1.25rem; }
            .survey-header h1 { font-size: 1.45rem; line-height: 1.26; overflow-wrap: anywhere; }
            .univ-badge { min-height: 70px; margin-bottom: 1rem; padding: .6rem .8rem; }
            .survey-header-meta { gap: .35rem; }
            .survey-header-meta span { font-size: .7rem; }
            .lulusan-card { padding: 1.35rem 1rem; }
            .lulusan-name, .info-value { overflow-wrap: anywhere; }
            .lulusan-meta { align-items: flex-start; border-radius: 12px; flex-direction: column; padding: .6rem .8rem; text-align: left; width: 100%; }
            .lulusan-meta-divider { display: none; }
            .kategori-title { font-size: .95rem; line-height: 1.45; padding: .85rem 1rem; }
            .survey-table { font-size: .78rem; min-width: 570px; }
            .survey-table thead th, .survey-table tbody tr td { padding: .55rem .6rem; }
            .survey-table .th-option { min-width: 54px; }
            .btn-submit { font-size: .92rem; letter-spacing: .04em; padding: .9rem 1rem; white-space: normal; }
            .d-flex.justify-content-between.align-items-center { align-items: flex-start !important; gap: .75rem; }
            .d-flex.align-items-center.gap-2 { align-items: flex-start !important; flex-direction: column; }
            .d-flex.align-items-center.gap-2 .form-control { max-width: 100% !important; width: 100%; }
            .company-summary { align-items: flex-start; }
            .company-stat { min-width: 72px; padding-left: .65rem; }
            .section-heading { font-size: 1.03rem; }
        }
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
                    <div class="univ-badge">
                        <img src="{{ asset('assets/images/logo/undika.png') }}" class="univ-logo" alt="Universitas Dinamika">
                    </div>
                    <div class="survey-eyebrow">Survey pengguna lulusan</div>
                    <h1>{{ $survey->judul }}</h1>
                    <p class="text-white-50 mb-1">{{ $survey->periode?->nama_periode }} · {{ $survey->periode?->tanggal_mulai?->translatedFormat('d M Y') }}–{{ $survey->periode?->tanggal_berakhir?->translatedFormat('d M Y') }}</p>
                    <p class="text-white-50 mb-0">Kode Akses Sesi: <span class="fw-bold text-white tracking-widest">{{ $survey->access_code }}</span></p>
                </div>
            </div>

            <div class="p-4 p-md-5">
                @if($survey->deskripsi)
                    <div class="alert alert-light border mb-4 text-secondary" style="line-height: 1.6;">
                        <i class="bi bi-info-circle-fill text-dinamika me-2"></i> {{ $survey->deskripsi }}
                    </div>
                @endif

                <h2 class="section-heading">Informasi tentang lulusan terkait</h2>

                <div class="lulusan-card">
                    <div class="lulusan-avatar">
                        <i class="bi bi-person-badge-fill"></i>
                    </div>
                    <div class="lulusan-name">{{ $survey->lulusan->nama ?? '-' }}</div>
                    <div class="lulusan-nim">{{ $survey->lulusan->nim ?? '-' }}</div>
                    <div class="lulusan-meta">
                        <div class="lulusan-meta-item">
                            <i class="bi bi-building-fill-check"></i>
                            <span>{{ $survey->lulusan->programStudi?->nama ?? '-' }}</span>
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

        <form action="{{ route('survey.submit', $survey->access_code) }}" method="POST" data-draft-key="spl:draft:survey-fill-{{ $survey->access_code }}">
            @csrf

            @if($errors->any())
                <div class="alert alert-danger border-0 shadow-sm mb-4" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    Lengkapi seluruh isian wajib sebelum mengirim survei.
                </div>
            @endif
            
            <div class="card-custom p-4 p-md-5">
                <div class="mb-2">
                    <div>
                        <h2 class="section-heading mb-2">Identitas perusahaan <span class="text-danger">*</span></h2>
                        <p class="section-description">Data institusi/perusahaan Anda sudah terisi dari sistem.</p>
                    </div>
                </div>

                @php
                    $jumlahSistem = $survey->penggunalulusan->lulusans->count();
                    $storedJumlah = $survey->penggunalulusan->jumlah_lulusan;
                @endphp

                <div class="company-summary">
                    <div class="company-summary-icon"><i class="bi bi-building"></i></div>
                        <div class="flex-grow-1">
                            <div class="company-summary-name">{{ $survey->penggunalulusan->nama_perusahaan ?? 'Nama Perusahaan Belum Tersedia' }}</div>
                            <div class="company-summary-address">{{ $survey->penggunalulusan->alamat_perusahaan ?? 'Alamat belum tersedia' }}</div>
                        </div>
                        <div class="company-stat">
                            <strong>{{ $jumlahSistem }}</strong>
                            <span>lulusan tercatat<br>di sistem</span>
                        </div>
                </div>

                <div class="employee-count-box mb-3">
                    <label class="form-label small text-secondary fw-bold mb-1">
                        <i class="bi bi-people-fill me-1 text-primary"></i>
                        Jumlah Lulusan yang Saat Ini Bekerja di Instansi Ini <span class="text-danger">*</span>
                    </label>
                    <div class="d-flex align-items-center gap-2">
                        <input type="number" name="jumlah_lulusan_bekerja" class="form-control"
                               style="max-width: 160px"
                               min="1" step="1" required value="{{ old('jumlah_lulusan_bekerja', $storedJumlah) }}"
                               placeholder="Minimal 1 orang">
                        @if($jumlahSistem > 0)
                        <span class="text-muted small">
                            (sistem mencatat <strong>{{ $jumlahSistem }}</strong> lulusan)
                        </span>
                        @endif
                    </div>
                    <div class="form-text">Diisi oleh perusahaan sesuai jumlah lulusan yang bekerja saat ini, minimal 1 orang.</div>
                    @error('jumlah_lulusan_bekerja')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
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
                            @php
                                $jenisOptions = [
                                    'Teknologi Informasi / Software / Digital',
                                    'Keuangan / Perbankan / Bisnis',
                                    'Manufaktur / Industri',
                                    'Perdagangan / Retail / E-Commerce',
                                    'Media / Kreatif / Desain',
                                    'Jasa (konsultan, pendidikan, dll)',
                                    'Pemerintahan',
                                    'BUMN / BUMD',
                                ];
                                $currentJenis = $survey->penggunalulusan->jenis_perusahaan ?? '';
                                $isLainnya = $currentJenis !== '' && !in_array($currentJenis, $jenisOptions);
                            @endphp
                            <select name="jenis_perusahaan" id="fill_jenis_select" class="form-control"
                                    onchange="document.getElementById('fill_jenis_lainnya_wrap').classList.toggle('d-none',this.value!=='_lainnya_')">
                                <option value="" disabled {{ !$currentJenis ? 'selected' : '' }}>Pilih Jenis Perusahaan</option>
                                <option value="Teknologi Informasi / Software / Digital" {{ $currentJenis === 'Teknologi Informasi / Software / Digital' ? 'selected' : '' }}>Teknologi Informasi / Software / Digital</option>
                                <option value="Keuangan / Perbankan / Bisnis" {{ $currentJenis === 'Keuangan / Perbankan / Bisnis' ? 'selected' : '' }}>Keuangan / Perbankan / Bisnis</option>
                                <option value="Manufaktur / Industri" {{ $currentJenis === 'Manufaktur / Industri' ? 'selected' : '' }}>Manufaktur / Industri</option>
                                <option value="Perdagangan / Retail / E-Commerce" {{ $currentJenis === 'Perdagangan / Retail / E-Commerce' ? 'selected' : '' }}>Perdagangan / Retail / E-Commerce</option>
                                <option value="Media / Kreatif / Desain" {{ $currentJenis === 'Media / Kreatif / Desain' ? 'selected' : '' }}>Media / Kreatif / Desain</option>
                                <option value="Jasa (konsultan, pendidikan, dll)" {{ $currentJenis === 'Jasa (konsultan, pendidikan, dll)' ? 'selected' : '' }}>Jasa (konsultan, pendidikan, dll)</option>
                                <option value="Pemerintahan" {{ $currentJenis === 'Pemerintahan' ? 'selected' : '' }}>Pemerintahan</option>
                                <option value="BUMN / BUMD" {{ $currentJenis === 'BUMN / BUMD' ? 'selected' : '' }}>BUMN / BUMD</option>
                                <option value="_lainnya_" {{ $isLainnya ? 'selected' : '' }}>Lainnya...</option>
                            </select>
                            <div id="fill_jenis_lainnya_wrap" class="mt-1 {{ $isLainnya ? '' : 'd-none' }}">
                                <input type="text" id="fill_jenis_lainnya_text" class="form-control form-control-sm"
                                       placeholder="Sebutkan jenis perusahaan..."
                                       value="{{ $isLainnya ? $currentJenis : '' }}"
                                       oninput="document.getElementById('fill_jenis_select').value='_lainnya_'">
                            </div>
                        </div>
                        <script>
                        document.querySelector('form').addEventListener('submit', function(e) {
                            var sel = document.getElementById('fill_jenis_select');
                            var text = document.getElementById('fill_jenis_lainnya_text');
                            if (sel && sel.value === '_lainnya_') {
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
                        <div class="col-md-12">
                            <label class="form-label small text-secondary fw-bold mb-1">Alamat Perusahaan</label>
                            <textarea name="alamat_perusahaan" class="form-control" rows="2" placeholder="Alamat lengkap...">{{ $survey->penggunalulusan->alamat_perusahaan ?? '' }}</textarea>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small text-secondary fw-bold mb-1">Kontak Perusahaan</label>
                            <input type="text" name="kontak_perusahaan" class="form-control" placeholder="Telp/WA Perusahaan..." value="{{ $survey->penggunalulusan->kontak_perusahaan ?? '' }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small text-secondary fw-bold mb-1">Jumlah Cabang Nasional</label>
                            <input type="number" name="cabang_kota" class="form-control" min="0" placeholder="Jumlah cabang..." value="{{ $survey->penggunalulusan->cabang_kota ?? 0 }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small text-secondary fw-bold mb-1">Jumlah Cabang Luar Negeri</label>
                            <input type="number" name="cabang_negara" class="form-control" min="0" placeholder="Jumlah negara..." value="{{ $survey->penggunalulusan->cabang_negara ?? 0 }}">
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-custom p-4 p-md-5">
                <div class="mb-2">
                    <h2 class="section-heading mb-2">Konfirmasi identitas responden <span class="text-danger">*</span></h2>
                    <p class="section-description">Mohon konfirmasi atau lengkapi data Anda sebagai perwakilan instansi yang mengisi kuesioner ini.</p>
                </div>
                
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label small text-secondary fw-bold mb-1">Nama Lengkap Penyelia <span class="text-danger">*</span></label>
                        <input type="text" name="nama_pengisi" class="form-control" required placeholder="Nama Anda..." value="{{ $survey->penggunalulusan->nama_penyelia ?? '' }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small text-secondary fw-bold mb-1">Jabatan / Posisi</label>
                        <input type="text" name="jabatan_pengisi" class="form-control" placeholder="Contoh: HRD Manager, Direktur..." value="{{ $survey->penggunalulusan->jabatan_penyelia ?? '' }}">
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
                    $ratingSoal      = $soalGroup->filter(fn($s) => $s->jenis_soal === 'rating')->values();
                    $multiChoiceSoal = $soalGroup->filter(fn($s) => $s->jenis_soal === 'multiple_choice')->values();
                    $essaySoal       = $soalGroup->filter(fn($s) => $s->jenis_soal === 'essay')->values();
                    $firstRating     = $ratingSoal->first();
                @endphp

                <div class="card-custom p-4 p-md-5">

                    {{-- RATING: tabel radio button, pilih salah satu --}}
                    @if($ratingSoal->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered survey-table mb-0">
                                <thead>
                                    <tr>
                                        <th colspan="{{ 2 + ($firstRating ? $firstRating->jawaban->count() : 0) }}">
                                            {{ $namaKategori }}
                                        </th>
                                    </tr>
                                    <tr>
                                        <th class="td-no">No</th>
                                        <th>Pertanyaan</th>
                                        @if($firstRating)
                                            @foreach($firstRating->jawaban as $j)
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
                                    @foreach($ratingSoal as $s)
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
                                                           @checked(old('jawaban.' . $s->id) == $j->id)
                                                           {{ $s->is_required ? 'required' : '' }}>
                                                </td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @foreach($ratingSoal->filter(fn($s) => $s->is_required) as $s)
                            @error('jawaban.' . $s->id)<div class="text-danger small mt-2">{{ $message }}</div>@enderror
                        @endforeach
                    @endif

                    {{-- MULTIPLE CHOICE: checkbox beberapa pilihan + kolom "Lainnya" teks bebas --}}
                    @if($multiChoiceSoal->count() > 0)
                        <div class="{{ $ratingSoal->count() > 0 ? 'mt-4 pt-4 border-top' : '' }}">
                            @if($ratingSoal->count() == 0)
                                <div class="kategori-title mb-4">
                                    <h5 class="mb-0">{{ $namaKategori }}</h5>
                                </div>
                            @endif

                            @foreach($multiChoiceSoal as $s)
                                <div class="mb-4 {{ !$loop->last ? 'pb-4 border-bottom-dashed' : '' }}" data-required-multiple="{{ $s->is_required ? 'true' : 'false' }}">
                                    <p class="fw-bold text-dark mb-3">
                                        <span class="text-dinamika me-1">{{ $loop->iteration }}.</span>
                                        {{ $s->soal }}
                                        @if($s->is_required) <span class="text-danger">*</span> @endif
                                    </p>
                                    <div class="ps-2">
                                        @foreach($s->jawaban as $j)
                                            <div class="form-check mc-option mb-2">
                                                <input class="form-check-input" type="checkbox"
                                                       name="mc[{{ $s->id }}][]"
                                                       value="{{ $j->id }}"
                                                       @checked(in_array($j->id, old('mc.' . $s->id, [])))
                                                       id="mc_{{ $s->id }}_{{ $j->id }}">
                                                <label class="form-check-label" for="mc_{{ $s->id }}_{{ $j->id }}">
                                                    {{ $j->jawaban }}
                                                    @if($j->nilai)
                                                        <span class="text-muted small">({{ $j->nilai }})</span>
                                                    @endif
                                                </label>
                                            </div>
                                        @endforeach

                                        <div class="form-check mc-option mb-2">
                                            <input class="form-check-input" type="checkbox"
                                                   id="mc_other_check_{{ $s->id }}"
                                                   @checked(old('mc_custom.' . $s->id))
                                                   onchange="
                                                       var txt = document.getElementById('mc_other_text_{{ $s->id }}');
                                                       txt.disabled = !this.checked;
                                                       if (this.checked) txt.focus();
                                                       else txt.value = '';
                                                   ">
                                            <label class="form-check-label" for="mc_other_check_{{ $s->id }}">
                                                Lainnya (tuliskan):
                                            </label>
                                        </div>
                                        <input type="text"
                                               name="mc_custom[{{ $s->id }}]"
                                               id="mc_other_text_{{ $s->id }}"
                                               class="mc-other-input ms-4 d-block"
                                               style="max-width: 420px"
                                               placeholder="Tuliskan jawaban Anda..."
                                               value="{{ old('mc_custom.' . $s->id) }}"
                                               {{ old('mc_custom.' . $s->id) ? '' : 'disabled' }}>
                                        <div class="text-danger small mt-2" data-multiple-error hidden>Pilih minimal satu jawaban untuk pertanyaan wajib ini.</div>
                                        @error('mc.' . $s->id)<div class="text-danger small mt-2">{{ $message }}</div>@enderror
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    {{-- ESSAY: textarea teks bebas --}}
                    @if($essaySoal->count() > 0)
                        @php $hasSectionAbove = $ratingSoal->count() > 0 || $multiChoiceSoal->count() > 0; @endphp
                        <div class="{{ $hasSectionAbove ? 'mt-4 pt-4 border-top' : '' }}">
                            @if(!$hasSectionAbove)
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
                                              {{ $s->is_required ? 'required' : '' }}>{{ old('jawaban.' . $s->id) }}</textarea>
                                    @error('jawaban.' . $s->id)<div class="text-danger small mt-1">{{ $message }}</div>@enderror
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
    <script>
        document.querySelector('form').addEventListener('submit', function (event) {
            let firstInvalidGroup = null;

            document.querySelectorAll('[data-required-multiple="true"]').forEach(function (group) {
                const hasCheckedAnswer = Array.from(group.querySelectorAll('input[name^="mc["]')).some(function (input) {
                    return input.checked;
                });
                const otherAnswer = group.querySelector('.mc-other-input')?.value.trim() ?? '';
                const error = group.querySelector('[data-multiple-error]');
                const isValid = hasCheckedAnswer || otherAnswer !== '';

                error.hidden = isValid;
                group.classList.toggle('border', !isValid);
                group.classList.toggle('border-danger', !isValid);

                if (!isValid && !firstInvalidGroup) {
                    firstInvalidGroup = group;
                }
            });

            if (firstInvalidGroup) {
                event.preventDefault();
                firstInvalidGroup.scrollIntoView({ behavior: 'smooth', block: 'center' });
                firstInvalidGroup.querySelector('input[type="checkbox"]')?.focus();
            }
        });
    </script>
    <x-form-draft-cache />
</body>
</html>
