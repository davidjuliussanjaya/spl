<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tracer Study Pengguna Lulusan — Universitas Dinamika</title>
    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.svg') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/vendors/bootstrap-icons/bootstrap-icons.css') }}">
    <style>
        :root {
            --brand: #1d4ed8;
            --brand-dark: #173ea8;
            --brand-soft: #eff6ff;
            --ink: #10203d;
            --text: #334155;
            --muted: #64748b;
            --line: #dbe5f1;
            --surface: #ffffff;
            --canvas: #f8fbff;
        }

        * { box-sizing: border-box; }
        html { scroll-behavior: smooth; }
        body { margin: 0; overflow-x: hidden; background: var(--canvas); color: var(--text); font-family: 'Inter', sans-serif; }
        a { color: inherit; }
        button, input { font: inherit; }
        .container { width: min(1120px, calc(100% - 3rem)); margin: 0 auto; }
        .skip-link { position: fixed; z-index: 100; top: .75rem; left: .75rem; padding: .7rem 1rem; border-radius: .5rem; background: var(--ink); color: #fff; text-decoration: none; transform: translateY(-180%); }
        .skip-link:focus { transform: translateY(0); }

        .site-header { position: sticky; z-index: 20; top: 0; border-bottom: 1px solid rgba(219, 229, 241, .72); background: rgba(255, 255, 255, .91); backdrop-filter: blur(12px); }
        .nav { display: flex; align-items: center; justify-content: space-between; min-height: 76px; gap: 1.25rem; }
        .brand { display: inline-flex; align-items: center; text-decoration: none; }
        .brand img { display: block; width: 148px; height: auto; }
        .nav-links { display: flex; align-items: center; gap: 1.55rem; }
        .nav-links a { color: #475569; font-size: .82rem; font-weight: 650; text-decoration: none; }
        .nav-links a:not(.nav-login):hover { color: var(--brand); }
        .nav-login { display: inline-flex; align-items: center; gap: .4rem; padding: .62rem .85rem; border: 1px solid var(--line); border-radius: 8px; color: var(--brand) !important; }
        .nav-login:hover { border-color: #93c5fd; background: var(--brand-soft); }

        .hero { position: relative; overflow: hidden; padding: clamp(4.25rem, 8vw, 7rem) 0 clamp(4.5rem, 8vw, 7.5rem); background: linear-gradient(115deg, #f8fbff 0%, #eff6ff 54%, #f7fbff 100%); }
        .hero::before { position: absolute; inset: 0; z-index: 0; opacity: .62; background: url('{{ asset('assets/images/bg/4853433.jpg') }}') center / cover; content: ''; mix-blend-mode: multiply; }
        .hero::after { position: absolute; z-index: 0; right: -13rem; bottom: -15rem; width: 42rem; height: 42rem; border: 42px solid rgba(37, 99, 235, .08); border-radius: 50%; content: ''; }
        .hero-grid { position: relative; z-index: 1; display: grid; grid-template-columns: minmax(0, 1.18fr) minmax(340px, .82fr); align-items: center; gap: clamp(2.5rem, 7vw, 7.5rem); }
        .eyebrow { display: inline-flex; align-items: center; gap: .45rem; margin: 0 0 1.15rem; color: var(--brand); font-size: .72rem; font-weight: 800; letter-spacing: .1em; text-transform: uppercase; }
        .eyebrow::before { width: 26px; height: 2px; border-radius: 99px; background: currentColor; content: ''; }
        h1 { max-width: 660px; margin: 0; color: var(--ink); font-size: clamp(2.35rem, 5vw, 4.15rem); font-weight: 800; letter-spacing: -.06em; line-height: 1.08; }
        .hero-copy > p:not(.eyebrow) { max-width: 590px; margin: 1.35rem 0 0; color: var(--muted); font-size: clamp(.95rem, 1.6vw, 1.06rem); line-height: 1.8; }
        .hero-actions { display: flex; flex-wrap: wrap; gap: .75rem; margin-top: 2rem; }
        .button { display: inline-flex; align-items: center; justify-content: center; gap: .5rem; min-height: 46px; padding: .75rem 1rem; border: 1px solid transparent; border-radius: 9px; font-size: .83rem; font-weight: 750; text-decoration: none; transition: transform .16s ease, box-shadow .16s ease, background-color .16s ease; }
        .button:hover { transform: translateY(-1px); }
        .button-primary { background: var(--brand); color: #fff; box-shadow: 0 9px 16px rgba(29, 78, 216, .2); }
        .button-primary:hover { background: var(--brand-dark); color: #fff; }
        .button-secondary { border-color: #bfdbfe; background: rgba(255, 255, 255, .75); color: var(--brand); }
        .button-secondary:hover { background: #fff; color: var(--brand-dark); }

        .access-card { position: relative; overflow: hidden; padding: clamp(1.35rem, 3.2vw, 2rem); border: 1px solid rgba(191, 219, 254, .85); border-radius: 18px; background: rgba(255, 255, 255, .96); box-shadow: 0 22px 55px rgba(15, 23, 42, .14); }
        #akses-survei { scroll-margin-top: 96px; }
        .access-card::before { position: absolute; top: 0; right: 0; left: 0; height: 4px; background: linear-gradient(90deg, var(--brand), #60a5fa); content: ''; }
        .access-symbol { display: inline-flex; align-items: center; justify-content: center; width: 46px; height: 46px; margin-bottom: 1.1rem; border-radius: 12px; background: var(--brand-soft); color: var(--brand); font-size: 1.2rem; }
        .access-card h2 { margin: 0; color: var(--ink); font-size: 1.25rem; letter-spacing: -.025em; }
        .access-card > p { margin: .55rem 0 1.35rem; color: var(--muted); font-size: .82rem; line-height: 1.6; }
        .access-form label { display: block; margin-bottom: .45rem; color: #475569; font-size: .7rem; font-weight: 750; letter-spacing: .06em; text-transform: uppercase; }
        .input-wrap { position: relative; }
        .input-wrap > i { position: absolute; top: 50%; left: .9rem; color: #94a3b8; transform: translateY(-50%); }
        .access-form input { width: 100%; min-height: 48px; padding: .72rem .85rem .72rem 2.55rem; border: 1px solid #cbd5e1; border-radius: 9px; background: #fff; color: var(--ink); font-size: .88rem; outline: none; transition: border-color .16s ease, box-shadow .16s ease; }
        .access-form input:focus { border-color: var(--brand); box-shadow: 0 0 0 4px rgba(37, 99, 235, .12); }
        .access-form input::placeholder { color: #94a3b8; }
        .access-form button { display: inline-flex; align-items: center; justify-content: center; width: 100%; min-height: 47px; gap: .45rem; margin-top: .75rem; border: 0; border-radius: 9px; background: var(--brand); color: #fff; cursor: pointer; font-size: .84rem; font-weight: 750; transition: background-color .16s ease, transform .16s ease; }
        .access-form button:hover { background: var(--brand-dark); transform: translateY(-1px); }
        .access-form button:focus-visible, .button:focus-visible, .nav-login:focus-visible { outline: 3px solid rgba(37, 99, 235, .35); outline-offset: 2px; }
        .access-note { display: flex; align-items: flex-start; gap: .45rem; margin: 1.1rem 0 0 !important; color: var(--muted) !important; font-size: .71rem !important; line-height: 1.5 !important; }
        .access-note i { margin-top: .08rem; color: #0f766e; }
        .form-error { display: flex; gap: .45rem; margin: .9rem 0 0; padding: .7rem .75rem; border: 1px solid #fecaca; border-radius: 8px; background: #fef2f2; color: #b91c1c; font-size: .76rem; line-height: 1.45; }

        .trust-bar { position: relative; z-index: 2; margin-top: -1px; border-bottom: 1px solid var(--line); background: #fff; }
        .trust-items { display: grid; grid-template-columns: repeat(3, 1fr); }
        .trust-item { display: flex; align-items: center; gap: .75rem; min-height: 92px; padding: 1rem clamp(1rem, 3vw, 2rem); border-right: 1px solid var(--line); }
        .trust-item:first-child { border-left: 1px solid var(--line); }
        .trust-item i { color: var(--brand); font-size: 1.25rem; }
        .trust-item strong { display: block; color: var(--ink); font-size: .78rem; }
        .trust-item span { display: block; margin-top: .15rem; color: var(--muted); font-size: .7rem; line-height: 1.45; }

        .section { padding: clamp(4rem, 7vw, 6.5rem) 0; }
        .section-heading { max-width: 650px; margin: 0 auto; text-align: center; }
        .section-heading .eyebrow { justify-content: center; }
        .section-heading h2 { margin: 0; color: var(--ink); font-size: clamp(1.65rem, 3vw, 2.45rem); letter-spacing: -.045em; line-height: 1.2; }
        .section-heading > p:last-child { margin: .9rem auto 0; color: var(--muted); font-size: .9rem; line-height: 1.75; }
        .impact-section { background: #fff; }
        .impact-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; margin-top: 2.75rem; }
        .impact-card { padding: 1.5rem; border: 1px solid var(--line); border-radius: 13px; background: #fff; transition: border-color .16s ease, box-shadow .16s ease, transform .16s ease; }
        .impact-card:hover { border-color: #93c5fd; box-shadow: 0 12px 25px rgba(15, 23, 42, .07); transform: translateY(-3px); }
        .impact-icon { display: inline-flex; align-items: center; justify-content: center; width: 42px; height: 42px; margin-bottom: 1.1rem; border-radius: 10px; background: var(--brand-soft); color: var(--brand); font-size: 1.05rem; }
        .impact-card h3 { margin: 0; color: var(--ink); font-size: .98rem; }
        .impact-card p { margin: .6rem 0 0; color: var(--muted); font-size: .78rem; line-height: 1.65; }

        .steps-section { background: linear-gradient(130deg, #102a60, #1d4ed8); color: #fff; }
        .steps-section .section-heading h2, .steps-section .section-heading .eyebrow { color: #fff; }
        .steps-section .section-heading > p:last-child { color: #dbeafe; }
        .steps-section .eyebrow::before { background: #93c5fd; }
        .steps { display: grid; grid-template-columns: repeat(3, 1fr); gap: 2rem; margin-top: 3rem; }
        .step { position: relative; padding: 0 1.2rem 0 0; }
        .step:not(:last-child)::after { position: absolute; top: 24px; right: -1rem; width: calc(100% - 52px); border-top: 1px dashed rgba(191, 219, 254, .55); content: ''; }
        .step-number { display: inline-flex; position: relative; z-index: 1; align-items: center; justify-content: center; width: 48px; height: 48px; border: 1px solid rgba(191, 219, 254, .72); border-radius: 50%; background: #1d4ed8; color: #fff; font-size: .88rem; font-weight: 800; }
        .step h3 { margin: 1.2rem 0 .5rem; font-size: .98rem; }
        .step p { margin: 0; color: #dbeafe; font-size: .77rem; line-height: 1.65; }

        .cta-section { padding: clamp(3.5rem, 6vw, 5.5rem) 0; background: var(--canvas); }
        .cta-panel { display: flex; align-items: center; justify-content: space-between; gap: 2rem; padding: clamp(1.5rem, 4vw, 2.75rem); border: 1px solid #bfdbfe; border-radius: 18px; background: linear-gradient(120deg, #eff6ff, #fff); }
        .cta-panel h2 { max-width: 610px; margin: 0; color: var(--ink); font-size: clamp(1.4rem, 2.7vw, 2.05rem); letter-spacing: -.04em; line-height: 1.25; }
        .cta-panel p { margin: .65rem 0 0; color: var(--muted); font-size: .82rem; line-height: 1.6; }
        .cta-panel .button { flex: 0 0 auto; }

        footer { padding: 2rem 0; border-top: 1px solid var(--line); background: #fff; }
        .footer-content { display: flex; align-items: center; justify-content: space-between; gap: 1rem; color: var(--muted); font-size: .72rem; }
        .footer-content strong { color: var(--ink); }
        .footer-content a { color: var(--brand); font-weight: 700; text-decoration: none; }
        .footer-content a:hover { text-decoration: underline; }

        .survey-success-notification { position: fixed; z-index: 50; top: 1rem; right: 1rem; display: flex; align-items: flex-start; gap: .7rem; width: min(390px, calc(100vw - 2rem)); padding: 1rem; border: 1px solid #86efac; border-radius: 11px; background: #f0fdf4; color: #166534; box-shadow: 0 12px 30px rgba(15, 23, 42, .16); }
        .survey-success-notification i { font-size: 1.25rem; }
        .survey-success-notification strong { display: block; margin-bottom: .15rem; font-size: .84rem; }
        .survey-success-notification span { font-size: .75rem; line-height: 1.45; }

        @media (max-width: 840px) {
            .hero-grid { grid-template-columns: 1fr; gap: 2.5rem; }
            .hero-copy { text-align: center; }
            .hero-copy .eyebrow, .hero-actions { justify-content: center; }
            .hero-copy > p:not(.eyebrow) { margin-right: auto; margin-left: auto; }
            .access-card { width: min(100%, 480px); margin: 0 auto; }
            .trust-items { grid-template-columns: 1fr; }
            .trust-item, .trust-item:first-child { min-height: 70px; border-right: 0; border-left: 0; border-bottom: 1px solid var(--line); }
            .trust-item:last-child { border-bottom: 0; }
            .impact-grid, .steps { gap: 1rem; }
            .cta-panel { align-items: flex-start; flex-direction: column; }
        }
        @media (max-width: 620px) {
            .container { width: min(100% - 2rem, 1120px); }
            .nav { min-height: 67px; }
            .brand img { width: 130px; }
            .nav-links { gap: .75rem; }
            .nav-links a:not(.nav-login) { display: none; }
            .hero { padding-top: 3.75rem; }
            .hero-actions { flex-direction: column; }
            .hero-actions .button { width: 100%; }
            .impact-grid, .steps { grid-template-columns: 1fr; }
            .step { padding: 0; }
            .step:not(:last-child)::after { top: 60px; left: 24px; width: 1px; height: 35px; border-top: 0; border-left: 1px dashed rgba(191, 219, 254, .55); }
            .step + .step { margin-top: 1.35rem; }
            .footer-content { align-items: flex-start; flex-direction: column; }
        }
    </style>
</head>
<body>
    <a class="skip-link" href="#akses-survei">Langsung ke form kode survei</a>

    @if(session('success'))
        <div class="survey-success-notification" role="alert" aria-live="polite">
            <i class="bi bi-check-circle-fill" aria-hidden="true"></i>
            <div>
                <strong>Kuesioner berhasil tersimpan</strong>
                <span>{{ session('success') }} Terima kasih atas waktu dan partisipasi Anda.</span>
            </div>
        </div>
    @endif

    <header class="site-header">
        <nav class="container nav" aria-label="Navigasi utama">
            <a class="brand" href="{{ route('landing') }}" aria-label="Beranda Tracer Study Universitas Dinamika">
                <img src="{{ asset('assets/images/logo/undika.png') }}" alt="Universitas Dinamika">
            </a>
            <div class="nav-links">
                <a href="#tentang">Tentang survei</a>
                <a href="#cara-mengisi">Cara mengisi</a>
                <a href="{{ route('login') }}" class="nav-login"><i class="bi bi-box-arrow-in-right" aria-hidden="true"></i> Login</a>
            </div>
        </nav>
    </header>

    <main>
        <section class="hero" aria-labelledby="hero-title">
            <div class="container hero-grid">
                <div class="hero-copy">
                    <p class="eyebrow">Tracer Study Universitas Dinamika</p>
                    <h1 id="hero-title">Suara Anda membentuk lulusan yang lebih siap.</h1>
                    <p>Bagikan pengalaman Anda sebagai pengguna lulusan Universitas Dinamika. Setiap masukan membantu kami menyelaraskan kompetensi lulusan dengan kebutuhan dunia kerja.</p>
                    <div class="hero-actions">
                        <a href="#akses-survei" class="button button-primary"><i class="bi bi-pencil-square" aria-hidden="true"></i> Isi survei sekarang</a>
                        <a href="#tentang" class="button button-secondary">Pelajari lebih lanjut <i class="bi bi-arrow-down" aria-hidden="true"></i></a>
                    </div>
                </div>

                <section id="akses-survei" class="access-card" aria-labelledby="survey-access-title">
                    <span class="access-symbol" aria-hidden="true"><i class="bi bi-key-fill"></i></span>
                    <h2 id="survey-access-title">Masukkan kode survei</h2>
                    <p>Gunakan kode akses yang telah dikirimkan kepada Anda untuk mulai mengisi kuesioner.</p>
                    <form action="{{ route('survey.access') }}" method="POST" class="access-form">
                        @csrf
                        <label for="survey-code">Kode akses survei</label>
                        <div class="input-wrap">
                            <i class="bi bi-ticket-perforated" aria-hidden="true"></i>
                            <input id="survey-code" type="text" name="code" value="{{ old('code') }}" placeholder="Contoh: SPL-AB12CD" autocomplete="off" autocapitalize="characters" spellcheck="false" required autofocus>
                        </div>
                        <button type="submit">Lanjutkan pengisian <i class="bi bi-arrow-right" aria-hidden="true"></i></button>
                    </form>
                    @if(session('error'))
                        <p class="form-error" role="alert"><i class="bi bi-exclamation-circle-fill" aria-hidden="true"></i><span>{{ session('error') }}</span></p>
                    @endif
                    <p class="access-note"><i class="bi bi-shield-check" aria-hidden="true"></i><span>Kode hanya dapat digunakan untuk survei aktif pada periode pengisian yang sedang berlangsung.</span></p>
                </section>
            </div>
        </section>

        <section class="trust-bar" aria-label="Komitmen survei">
            <div class="container trust-items">
                <div class="trust-item"><i class="bi bi-person-check" aria-hidden="true"></i><div><strong>Untuk pengguna lulusan</strong><span>Dirancang agar cepat dan mudah diisi.</span></div></div>
                <div class="trust-item"><i class="bi bi-shield-lock" aria-hidden="true"></i><div><strong>Data dijaga dengan aman</strong><span>Masukan digunakan untuk evaluasi institusi.</span></div></div>
                <div class="trust-item"><i class="bi bi-bar-chart-line" aria-hidden="true"></i><div><strong>Masukan yang berdampak</strong><span>Mendukung peningkatan mutu lulusan.</span></div></div>
            </div>
        </section>

        <section id="tentang" class="section impact-section" aria-labelledby="impact-title">
            <div class="container">
                <div class="section-heading">
                    <p class="eyebrow">Tentang survei</p>
                    <h2 id="impact-title">Mengapa partisipasi Anda penting?</h2>
                    <p>Evaluasi dari dunia kerja adalah bagian penting untuk memastikan proses pembelajaran tetap relevan dan menghasilkan lulusan berkualitas.</p>
                </div>
                <div class="impact-grid">
                    <article class="impact-card">
                        <span class="impact-icon"><i class="bi bi-bullseye" aria-hidden="true"></i></span>
                        <h3>Kurikulum yang relevan</h3>
                        <p>Masukan Anda membantu kami menyesuaikan pembelajaran dengan kompetensi yang dibutuhkan industri.</p>
                    </article>
                    <article class="impact-card">
                        <span class="impact-icon"><i class="bi bi-people" aria-hidden="true"></i></span>
                        <h3>Kemitraan yang lebih kuat</h3>
                        <p>Hasil survei menjadi dasar penguatan kolaborasi antara kampus dan dunia kerja.</p>
                    </article>
                    <article class="impact-card">
                        <span class="impact-icon"><i class="bi bi-graph-up-arrow" aria-hidden="true"></i></span>
                        <h3>Perbaikan berkelanjutan</h3>
                        <p>Setiap respons memberikan arah nyata bagi peningkatan kualitas lulusan berikutnya.</p>
                    </article>
                </div>
            </div>
        </section>

        <section id="cara-mengisi" class="section steps-section" aria-labelledby="steps-title">
            <div class="container">
                <div class="section-heading">
                    <p class="eyebrow">Cara mengisi</p>
                    <h2 id="steps-title">Tiga langkah sederhana</h2>
                    <p>Proses pengisian dirancang singkat agar Anda dapat memberikan evaluasi dengan nyaman.</p>
                </div>
                <div class="steps">
                    <article class="step"><span class="step-number">01</span><h3>Siapkan kode survei</h3><p>Temukan kode akses yang dikirimkan oleh Universitas Dinamika kepada Anda.</p></article>
                    <article class="step"><span class="step-number">02</span><h3>Masukkan kode akses</h3><p>Ketik kode pada form di atas untuk membuka kuesioner yang sesuai.</p></article>
                    <article class="step"><span class="step-number">03</span><h3>Kirimkan evaluasi</h3><p>Lengkapi seluruh pertanyaan lalu kirimkan jawaban Anda dengan aman.</p></article>
                </div>
            </div>
        </section>

        <section class="cta-section" aria-label="Ajakan mengisi survei">
            <div class="container cta-panel">
                <div>
                    <h2>Sudah menerima kode survei?</h2>
                    <p>Masukkan kode akses Anda dan mulai isi evaluasi pengguna lulusan sekarang.</p>
                </div>
                <a href="#akses-survei" class="button button-primary">Masukkan kode <i class="bi bi-arrow-up-right" aria-hidden="true"></i></a>
            </div>
        </section>
    </main>

    <footer>
        <div class="container footer-content">
            <div><strong>Universitas Dinamika</strong> &copy; {{ date('Y') }} — Sistem Tracer Study</div>
            <a href="{{ route('login') }}">Login admin / pengguna</a>
        </div>
    </footer>

    <script>
        @if(session('clear_survey_draft'))
            try {
                localStorage.removeItem('spl:draft:survey-fill-{{ session('clear_survey_draft') }}');
                sessionStorage.removeItem('spl:draft:pending');
            } catch (error) {}
        @endif
    </script>
</body>
</html>
