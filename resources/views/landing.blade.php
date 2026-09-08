<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tracer Study — Universitas Dinamika</title>
    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.svg') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,wght@0,400;0,500;0,600;0,700;0,800;1,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/vendors/bootstrap-icons/bootstrap-icons.css') }}">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --navy:   #8B1A2A;
            --blue:   #8B1A2A;
            --blue-l: #B91C3A;
            --cyan:   #C9A227;
            --green:  #22C55E;
            --slate:  #64748B;
            --slate-l:#94A3B8;
            --bg:     #FFF5F7;
            --white:  #FFFFFF;
            --radius: 16px;
        }

        html { scroll-behavior: smooth; }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg);
            color: #1E293B;
            line-height: 1.6;
            overflow-x: hidden;
        }

        /* ── Navbar ── */
        .navbar {
            position: fixed; top: 0; left: 0; right: 0; z-index: 100;
            display: flex; align-items: center; justify-content: space-between;
            padding: .9rem 2rem;
            background: rgba(255,255,255,.85);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(0,0,0,.06);
            transition: box-shadow .2s;
        }
        .navbar.scrolled { box-shadow: 0 2px 20px rgba(0,0,0,.08); }
        .nav-brand { display: flex; align-items: center; gap: .6rem; text-decoration: none; }
        .nav-logo {
            width: 36px; height: 36px; border-radius: 10px;
            background: linear-gradient(135deg, #8B1A2A, #B91C3A);
            display: flex; align-items: center; justify-content: center;
            color: #fff; font-weight: 800; font-size: .95rem; letter-spacing: -.5px;
        }
        .nav-name { font-weight: 700; font-size: .95rem; color: #8B1A2A; }
        .nav-name span { color: #B91C3A; }
        .nav-actions { display: flex; align-items: center; gap: .75rem; }
        .btn-nav-outline {
            padding: .42rem 1.1rem; border-radius: 8px; font-size: .82rem; font-weight: 600;
            border: 1.5px solid #8B1A2A; color: #8B1A2A;
            background: transparent; cursor: pointer; text-decoration: none;
            transition: all .15s;
        }
        .btn-nav-outline:hover { background: #8B1A2A; color: #fff; }
        .btn-nav-fill {
            padding: .42rem 1.1rem; border-radius: 8px; font-size: .82rem; font-weight: 600;
            border: none; color: #fff; background: #8B1A2A;
            cursor: pointer; text-decoration: none; transition: background .15s;
        }
        .btn-nav-fill:hover { background: #6C0215; }

        /* ── Hero ── */
        .hero {
            min-height: 100vh;
            display: flex; align-items: center;
            position: relative; overflow: hidden;
            padding: 7rem 2rem 4rem;
            background: linear-gradient(135deg, #4A000D 0%, #8B1A2A 45%, #B91C3A 100%);
        }
        .hero-blob {
            position: absolute; border-radius: 50%; filter: blur(80px); opacity: .18; pointer-events: none;
        }
        .hero-blob-1 { width: 600px; height: 600px; background: var(--blue-l); top: -150px; right: -100px; }
        .hero-blob-2 { width: 400px; height: 400px; background: var(--cyan); bottom: -80px; left: -80px; }
        .hero-blob-3 { width: 300px; height: 300px; background: var(--green); top: 40%; left: 35%; }

        .hero-inner {
            max-width: 1120px; margin: 0 auto; width: 100%;
            display: grid; grid-template-columns: 1fr 1fr; gap: 4rem; align-items: center;
        }
        @media(max-width: 900px) { .hero-inner { grid-template-columns: 1fr; gap: 2.5rem; } }

        .hero-eyebrow {
            display: inline-flex; align-items: center; gap: .4rem;
            font-size: .75rem; font-weight: 600; letter-spacing: .8px; text-transform: uppercase;
            color: var(--cyan); background: rgba(6,182,212,.12);
            border: 1px solid rgba(6,182,212,.3); border-radius: 50px;
            padding: .3rem .8rem; margin-bottom: 1.25rem;
        }
        .hero-title {
            font-size: clamp(2rem, 4vw, 3rem); font-weight: 800; line-height: 1.15;
            color: #fff; margin-bottom: 1.25rem;
        }
        .hero-title .accent { color: var(--cyan); }
        .hero-desc {
            font-size: 1.05rem; color: rgba(255,255,255,.72); line-height: 1.75;
            margin-bottom: 2rem; max-width: 480px;
        }
        .hero-cta { display: flex; gap: .85rem; flex-wrap: wrap; }
        .btn-hero-primary {
            display: inline-flex; align-items: center; gap: .45rem;
            padding: .75rem 1.75rem; border-radius: 10px; font-size: .92rem; font-weight: 700;
            background: #C9A227; color: #4A000D; text-decoration: none;
            border: none; cursor: pointer; transition: all .2s;
            box-shadow: 0 4px 20px rgba(201,162,39,.4);
        }
        .btn-hero-primary:hover { background: #E8C547; transform: translateY(-1px); box-shadow: 0 6px 24px rgba(201,162,39,.5); color: #4A000D; }
        .btn-hero-secondary {
            display: inline-flex; align-items: center; gap: .45rem;
            padding: .75rem 1.75rem; border-radius: 10px; font-size: .92rem; font-weight: 600;
            background: rgba(255,255,255,.1); color: #fff; text-decoration: none;
            border: 1.5px solid rgba(255,255,255,.25); cursor: pointer; transition: all .2s;
            backdrop-filter: blur(4px);
        }
        .btn-hero-secondary:hover { background: rgba(255,255,255,.18); transform: translateY(-1px); color: #fff; }

        /* Hero ilustrasi / card mockup */
        .hero-visual { display: flex; justify-content: flex-end; }
        @media(max-width: 900px) { .hero-visual { justify-content: center; } }

        .mockup-card {
            background: rgba(255,255,255,.07);
            border: 1px solid rgba(255,255,255,.14);
            border-radius: 20px; padding: 1.5rem;
            width: 100%; max-width: 400px;
            backdrop-filter: blur(8px);
        }
        .mockup-header {
            display: flex; align-items: center; gap: .7rem; margin-bottom: 1.25rem;
        }
        .mockup-dot { width: 10px; height: 10px; border-radius: 50%; }
        .mockup-title { font-size: .78rem; font-weight: 600; color: rgba(255,255,255,.5); }
        .mockup-stat-row { display: grid; grid-template-columns: 1fr 1fr; gap: .75rem; margin-bottom: .75rem; }
        .mockup-stat {
            background: rgba(255,255,255,.07); border-radius: 12px; padding: 1rem;
            border: 1px solid rgba(255,255,255,.1);
        }
        .mockup-stat-val { font-size: 1.4rem; font-weight: 800; color: #fff; line-height: 1; margin-bottom: .2rem; }
        .mockup-stat-lbl { font-size: .68rem; color: rgba(255,255,255,.5); font-weight: 500; }
        .mockup-bar-section { margin-top: .5rem; }
        .mockup-bar-lbl { display: flex; justify-content: space-between; font-size: .7rem; color: rgba(255,255,255,.5); margin-bottom: .4rem; }
        .mockup-bar-track { height: 7px; background: rgba(255,255,255,.1); border-radius: 99px; overflow: hidden; margin-bottom: .55rem; }
        .mockup-bar-fill { height: 100%; border-radius: 99px; }

        /* ── Stats strip ── */
        .stats-strip {
            background: var(--white); border-top: 1px solid #E2E8F0; border-bottom: 1px solid #E2E8F0;
            padding: 2.5rem 2rem;
        }
        .stats-inner { max-width: 1120px; margin: 0 auto; display: flex; justify-content: center; flex-wrap: wrap; gap: 3rem; }
        .stat-item { text-align: center; }
        .stat-num { font-size: 2rem; font-weight: 800; color: #4A000D; line-height: 1; }
        .stat-num span { color: #8B1A2A; }
        .stat-lbl { font-size: .78rem; color: var(--slate); font-weight: 500; margin-top: .25rem; }

        /* ── Features ── */
        .section { padding: 5rem 2rem; }
        .section-inner { max-width: 1120px; margin: 0 auto; }
        .section-tag {
            display: inline-block; font-size: .72rem; font-weight: 700; text-transform: uppercase;
            letter-spacing: .8px; color: var(--blue); background: #FDE8EC;
            border-radius: 50px; padding: .3rem .9rem; margin-bottom: 1rem;
        }
        .section-title { font-size: clamp(1.5rem, 3vw, 2.1rem); font-weight: 800; color: var(--navy); margin-bottom: .75rem; }
        .section-sub { font-size: 1rem; color: var(--slate); max-width: 540px; }

        .feat-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 1.5rem; margin-top: 3rem; }
        @media(max-width: 900px) { .feat-grid { grid-template-columns: 1fr 1fr; } }
        @media(max-width: 580px) { .feat-grid { grid-template-columns: 1fr; } }

        .feat-card {
            background: var(--white); border: 1px solid #E2E8F0; border-radius: var(--radius);
            padding: 1.75rem; transition: all .2s;
        }
        .feat-card:hover { transform: translateY(-4px); box-shadow: 0 8px 30px rgba(0,0,0,.08); border-color: var(--blue-l); }
        .feat-icon {
            width: 48px; height: 48px; border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.25rem; margin-bottom: 1.1rem;
        }
        .feat-title { font-size: .95rem; font-weight: 700; color: var(--navy); margin-bottom: .5rem; }
        .feat-desc { font-size: .83rem; color: var(--slate); line-height: 1.65; }

        /* ── How it works ── */
        .how-bg { background: linear-gradient(135deg, #FFF0F3, #FDE8EC); }
        .steps { display: flex; flex-direction: column; gap: 1.5rem; margin-top: 2.5rem; max-width: 640px; }
        .step { display: flex; gap: 1.25rem; align-items: flex-start; }
        .step-num {
            width: 40px; height: 40px; border-radius: 10px; flex-shrink: 0;
            background: var(--navy); color: #fff;
            display: flex; align-items: center; justify-content: center;
            font-weight: 800; font-size: .9rem;
        }
        .step-title { font-size: .92rem; font-weight: 700; color: var(--navy); margin-bottom: .2rem; }
        .step-desc  { font-size: .82rem; color: var(--slate); line-height: 1.6; }

        /* ── CTA Banner ── */
        .cta-banner {
            background: linear-gradient(135deg, #4A000D, #8B1A2A);
            border-radius: 20px; padding: 3rem 2.5rem;
            display: flex; align-items: center; justify-content: space-between;
            gap: 2rem; flex-wrap: wrap;
        }
        .cta-title { font-size: 1.5rem; font-weight: 800; color: #fff; margin-bottom: .4rem; }
        .cta-sub   { font-size: .9rem; color: rgba(255,255,255,.65); }
        .cta-btns  { display: flex; gap: .75rem; flex-shrink: 0; flex-wrap: wrap; }

        /* ── Footer ── */
        footer {
            background: #4A000D; color: rgba(255,255,255,.5);
            padding: 2rem; text-align: center; font-size: .8rem;
        }
        footer strong { color: rgba(255,255,255,.85); }
        .footer-links {
            display: flex; justify-content: center; gap: .75rem; flex-wrap: wrap;
            margin-bottom: .9rem;
        }
        .footer-link {
            display: inline-flex; align-items: center; gap: .35rem;
            padding: .55rem .9rem; border-radius: 8px;
            color: #4A000D; background: #C9A227; text-decoration: none;
            font-weight: 700; font-size: .78rem;
        }
        .footer-link:hover { background: #E8C547; color: #4A000D; }

        /* ── Survey access card ── */
        .survey-card {
            background: rgba(255,255,255,.06);
            border: 1px solid rgba(255,255,255,.15);
            border-radius: 14px; padding: 1.5rem; margin-top: 2rem;
            backdrop-filter: blur(4px);
        }
        .survey-card-title { font-size: .78rem; font-weight: 600; color: rgba(255,255,255,.6); text-transform: uppercase; letter-spacing: .6px; margin-bottom: .85rem; }
        .survey-input-row { display: flex; gap: .5rem; }
        .survey-input {
            flex: 1; padding: .55rem .9rem; border-radius: 8px; border: 1.5px solid rgba(255,255,255,.2);
            background: rgba(255,255,255,.08); color: #fff; font-size: .88rem;
            outline: none; transition: border-color .15s; font-family: inherit;
        }
        .survey-input::placeholder { color: rgba(255,255,255,.35); }
        .survey-input:focus { border-color: var(--cyan); }
        .survey-btn {
            padding: .55rem 1.1rem; border-radius: 8px; border: none;
            background: #C9A227; color: #4A000D; font-weight: 700; font-size: .85rem;
            cursor: pointer; transition: background .15s; white-space: nowrap; font-family: inherit;
        }
        .survey-btn:hover { background: #E8C547; }
    </style>
</head>
<body>

    {{-- ── Navbar ── --}}
    <nav class="navbar" id="navbar">
        <a href="{{ url('/') }}" class="nav-brand">
            <div class="nav-logo"><i class="bi bi-mortarboard-fill" style="font-size:.9rem;"></i></div>
            <div class="nav-name">Undika<span> Tracer Study</span></div>
        </a>
        <div class="nav-actions">
            <a href="#fitur" class="btn-nav-outline" style="border:none;color:var(--slate);background:transparent;font-weight:500;">Fitur</a>
            <a href="#cara-kerja" class="btn-nav-outline" style="border:none;color:var(--slate);background:transparent;font-weight:500;">Cara Kerja</a>
            <a href="{{ route('login') }}" class="btn-nav-fill">
                <i class="bi bi-box-arrow-in-right"></i> Masuk
            </a>
        </div>
    </nav>

    {{-- ── Hero ── --}}
    <section class="hero">
        <div class="hero-blob hero-blob-1"></div>
        <div class="hero-blob hero-blob-2"></div>
        <div class="hero-blob hero-blob-3"></div>

        <div class="hero-inner">
            <div>
                <div class="hero-eyebrow">
                    <i class="bi bi-mortarboard-fill"></i>
                    Universitas Dinamika
                </div>
                <h1 class="hero-title">
                    Sistem Tracer Study<br>
                    <span class="accent">Evaluasi Pengguna</span><br>
                    Lulusan
                </h1>
                <p class="hero-desc">
                    Platform digital untuk memantau dan mengevaluasi kualitas lulusan Universitas Dinamika
                    di dunia kerja melalui penilaian langsung dari pengguna lulusan.
                </p>
                <div class="hero-cta">
                    <a href="{{ route('login') }}" class="btn-hero-primary">
                        <i class="bi bi-box-arrow-in-right"></i> Masuk sebagai Admin
                    </a>
                    <a href="#isi-survey" class="btn-hero-secondary">
                        <i class="bi bi-pencil-square"></i> Isi Survey
                    </a>
                </div>

                {{-- Survey access inline --}}
                <div class="survey-card" id="isi-survey">
                    <div class="survey-card-title"><i class="bi bi-key me-1"></i> Akses Survey dengan Kode</div>
                    <form action="{{ route('survey.access') }}" method="POST">
                        @csrf
                        <div class="survey-input-row">
                            <input type="text" name="code" class="survey-input"
                                placeholder="Masukkan kode survey Anda..." required>
                            <button type="submit" class="survey-btn">
                                <i class="bi bi-arrow-right-circle-fill"></i> Lanjut
                            </button>
                        </div>
                        @if(session('error'))
                            <p style="font-size:.78rem;color:#FCA5A5;margin-top:.5rem;">
                                <i class="bi bi-exclamation-circle me-1"></i>{{ session('error') }}
                            </p>
                        @endif
                    </form>
                </div>
            </div>

            {{-- Mockup visual --}}
            <div class="hero-visual">
                <div class="mockup-card">
                    <div class="mockup-header">
                        <div class="mockup-dot" style="background:#EF4444;"></div>
                        <div class="mockup-dot" style="background:#F59E0B;"></div>
                        <div class="mockup-dot" style="background:#22C55E;"></div>
                        <div class="mockup-title" style="margin-left:.25rem;">Dashboard Evaluasi</div>
                    </div>
                    <div class="mockup-stat-row">
                        <div class="mockup-stat">
                            <div class="mockup-stat-val">128</div>
                            <div class="mockup-stat-lbl">Total Responden</div>
                        </div>
                        <div class="mockup-stat">
                            <div class="mockup-stat-val" style="color:#22C55E;">3.82</div>
                            <div class="mockup-stat-lbl">Indeks Kepuasan</div>
                        </div>
                    </div>
                    <div class="mockup-bar-section">
                        @foreach([['Kerjasama Tim','#22C55E','88%'],['Komunikasi','#3B82F6','75%'],['Kepemimpinan','#F59E0B','62%'],['Etos Kerja','#06B6D4','91%']] as [$label,$color,$width])
                        <div class="mockup-bar-lbl"><span>{{ $label }}</span><span style="color:{{ $color }};">{{ $width }}</span></div>
                        <div class="mockup-bar-track">
                            <div class="mockup-bar-fill" style="width:{{ $width }};background:{{ $color }};"></div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ── Stats Strip ── --}}
    <div class="stats-strip">
        <div class="stats-inner">
            <div class="stat-item">
                <div class="stat-num">3<span>+</span></div>
                <div class="stat-lbl">Fakultas</div>
            </div>
            <div class="stat-item">
                <div class="stat-num">10<span>+</span></div>
                <div class="stat-lbl">Kategori Kompetensi</div>
            </div>
            <div class="stat-item">
                <div class="stat-num">4</div>
                <div class="stat-lbl">Skala Penilaian</div>
            </div>
            <div class="stat-item">
                <div class="stat-num">100<span>%</span></div>
                <div class="stat-lbl">Berbasis Digital</div>
            </div>
        </div>
    </div>

    {{-- ── Fitur ── --}}
    <section class="section" id="fitur">
        <div class="section-inner">
            <div>
                <span class="section-tag">Fitur Unggulan</span>
                <h2 class="section-title">Semua yang dibutuhkan dalam satu platform</h2>
                <p class="section-sub">Dirancang khusus untuk kebutuhan tracer study perguruan tinggi yang efisien dan akurat.</p>
            </div>
            <div class="feat-grid">
                <div class="feat-card">
                    <div class="feat-icon" style="background:#FDE8EC;color:#8B1A2A;">
                        <i class="bi bi-send-check-fill"></i>
                    </div>
                    <div class="feat-title">Survey Digital</div>
                    <div class="feat-desc">Pengguna lulusan mengisi penilaian secara online menggunakan kode unik yang dikirimkan, kapan saja dan di mana saja.</div>
                </div>
                <div class="feat-card">
                    <div class="feat-icon" style="background:#F0FDF4;color:#16A34A;">
                        <i class="bi bi-bar-chart-line-fill"></i>
                    </div>
                    <div class="feat-title">Dashboard Analitik</div>
                    <div class="feat-desc">Visualisasi distribusi penilaian per kategori kompetensi secara real-time dengan grafik yang informatif.</div>
                </div>
                <div class="feat-card">
                    <div class="feat-icon" style="background:#FFF7ED;color:#EA580C;">
                        <i class="bi bi-percent"></i>
                    </div>
                    <div class="feat-title">Distribusi Kepuasan</div>
                    <div class="feat-desc">Melihat persentase Sangat Baik, Baik, Kurang, dan Sangat Kurang per kategori untuk analisis mendalam.</div>
                </div>
                <div class="feat-card">
                    <div class="feat-icon" style="background:#FDF4FF;color:#9333EA;">
                        <i class="bi bi-funnel-fill"></i>
                    </div>
                    <div class="feat-title">Filter Fleksibel</div>
                    <div class="feat-desc">Saring data berdasarkan tahun lulus, fakultas, program studi, dan jenis perusahaan untuk analisis yang lebih terarah.</div>
                </div>
                <div class="feat-card">
                    <div class="feat-icon" style="background:#ECFDF5;color:#059669;">
                        <i class="bi bi-file-earmark-excel-fill"></i>
                    </div>
                    <div class="feat-title">Ekspor Laporan</div>
                    <div class="feat-desc">Unduh laporan lengkap dalam format Excel dengan distribusi penilaian per soal dan ringkasan per kategori.</div>
                </div>
                <div class="feat-card">
                    <div class="feat-icon" style="background:#FDE8EC;color:#8B1A2A;">
                        <i class="bi bi-building-check"></i>
                    </div>
                    <div class="feat-title">Multi Fakultas</div>
                    <div class="feat-desc">Mendukung FTI, FDIK, dan FEB dengan pertanyaan yang dapat disesuaikan per peruntukan fakultas.</div>
                </div>
            </div>
        </div>
    </section>

    {{-- ── Cara Kerja ── --}}
    <section class="section how-bg" id="cara-kerja">
        <div class="section-inner" style="display:grid;grid-template-columns:1fr 1fr;gap:4rem;align-items:center;">
            <div>
                <span class="section-tag">Cara Kerja</span>
                <h2 class="section-title">Proses yang sederhana dan terstruktur</h2>
                <p class="section-sub">Dari pengiriman kode hingga laporan tersedia dalam beberapa langkah mudah.</p>
                <div class="steps">
                    <div class="step">
                        <div class="step-num">1</div>
                        <div>
                            <div class="step-title">Admin membuat survey</div>
                            <div class="step-desc">Admin mendaftarkan data lulusan dan pengguna lulusan, lalu sistem menghasilkan kode unik untuk setiap survey.</div>
                        </div>
                    </div>
                    <div class="step">
                        <div class="step-num">2</div>
                        <div>
                            <div class="step-title">Responden mengisi penilaian</div>
                            <div class="step-desc">Pengguna lulusan mengakses survey menggunakan kode yang diterima dan mengisi penilaian secara digital.</div>
                        </div>
                    </div>
                    <div class="step">
                        <div class="step-num">3</div>
                        <div>
                            <div class="step-title">Data terekap otomatis</div>
                            <div class="step-desc">Setiap jawaban tersimpan dan langsung terproses menjadi statistik distribusi kepuasan per kategori kompetensi.</div>
                        </div>
                    </div>
                    <div class="step">
                        <div class="step-num">4</div>
                        <div>
                            <div class="step-title">Laporan siap diunduh</div>
                            <div class="step-desc">Admin dapat melihat dashboard analitik dan mengunduh laporan Excel kapan saja sesuai kebutuhan.</div>
                        </div>
                    </div>
                </div>
            </div>
            <div style="display:flex;justify-content:center;align-items:center;">
                <div style="background:var(--white);border-radius:20px;padding:2rem;box-shadow:0 8px 40px rgba(0,0,0,.08);width:100%;max-width:380px;">
                    <div style="font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.6px;color:var(--slate-l);margin-bottom:1.25rem;">
                        Tingkat Kepuasan Pengguna
                    </div>
                    @foreach([['Kerjasama Tim','#22C55E','75%','25%'],['Etos Kerja','#3B82F6','50%','50%'],['Kepemimpinan','#F59E0B','33%','67%'],['Komunikasi','#06B6D4','62%','38%']] as [$kat,$c1,$pct1,$pct2])
                    <div style="margin-bottom:1rem;">
                        <div style="display:flex;justify-content:space-between;font-size:.78rem;font-weight:600;color:#334155;margin-bottom:.35rem;">
                            <span>{{ $kat }}</span>
                            <span style="color:{{ $c1 }};">{{ $pct1 }}</span>
                        </div>
                        <div style="display:flex;height:8px;border-radius:99px;overflow:hidden;gap:2px;">
                            <div style="width:{{ $pct1 }};background:{{ $c1 }};border-radius:99px 0 0 99px;"></div>
                            <div style="flex:1;background:#E2E8F0;border-radius:0 99px 99px 0;"></div>
                        </div>
                    </div>
                    @endforeach
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:.75rem;margin-top:1.5rem;">
                        <div style="background:#F0FDF4;border-radius:10px;padding:.85rem;text-align:center;">
                            <div style="font-size:1.2rem;font-weight:800;color:#16A34A;">SB</div>
                            <div style="font-size:.68rem;color:#15803D;font-weight:500;">Sangat Baik</div>
                        </div>
                        <div style="background:#FDE8EC;border-radius:10px;padding:.85rem;text-align:center;">
                            <div style="font-size:1.2rem;font-weight:800;color:#8B1A2A;">B</div>
                            <div style="font-size:.68rem;color:#6C0215;font-weight:500;">Baik</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <style>@media(max-width:900px){.section-inner{grid-template-columns:1fr !important;gap:2.5rem !important;}}</style>
    </section>

    {{-- ── CTA Banner ── --}}
    <section class="section">
        <div class="section-inner">
            <div class="cta-banner">
                <div>
                    <div class="cta-title">Siap mulai mengevaluasi lulusan?</div>
                    <div class="cta-sub">Masuk ke dashboard admin atau bagikan kode survey ke pengguna lulusan Anda.</div>
                </div>
                <div class="cta-btns">
                    <a href="{{ route('login') }}" class="btn-hero-primary" style="box-shadow:none;">
                        <i class="bi bi-box-arrow-in-right"></i> Masuk sebagai Admin
                    </a>
                    <a href="#isi-survey" class="btn-hero-secondary">
                        <i class="bi bi-pencil-square"></i> Isi Survey
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- ── Footer ── --}}
    <footer>
        <div class="footer-links">
            <a href="{{ route('user-manual') }}" class="footer-link">
                <i class="bi bi-file-earmark-text-fill"></i> User Manual
            </a>
        </div>
        <p>&copy; {{ date('Y') }} <strong>Universitas Dinamika</strong> · Sistem Tracer Study Evaluasi Pengguna Lulusan</p>
    </footer>

    <script>
        // Navbar shadow on scroll
        const navbar = document.getElementById('navbar');
        window.addEventListener('scroll', () => {
            navbar.classList.toggle('scrolled', window.scrollY > 20);
        });

        // Smooth highlight active nav link
        const sections = document.querySelectorAll('section[id], div[id="isi-survey"]');
        const navLinks = document.querySelectorAll('.navbar a[href^="#"]');
        const observer = new IntersectionObserver(entries => {
            entries.forEach(e => {
                if (e.isIntersecting) {
                    navLinks.forEach(l => l.style.color = '');
                    const active = document.querySelector(`.navbar a[href="#${e.target.id}"]`);
                    if (active) active.style.color = 'var(--blue)';
                }
            });
        }, { threshold: .4 });
        sections.forEach(s => observer.observe(s));
    </script>
</body>
</html>
