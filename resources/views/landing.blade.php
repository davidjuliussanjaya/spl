<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tracer Study — Universitas Dinamika</title>
    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.svg') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/vendors/bootstrap-icons/bootstrap-icons.css') }}">
    <style>
        :root { --blue: #2563eb; --blue-dark: #1d4ed8; --navy: #0f172a; --muted: #64748b; }
        * { box-sizing: border-box; }
        body {
            min-height: 100vh; margin: 0; display: grid; place-items: center; padding: 1.5rem;
            font-family: 'Inter', sans-serif; color: var(--navy);
            background: linear-gradient(135deg, #eff6ff 0%, #f8fafc 48%, #dbeafe 100%);
        }
        .landing-main { width: min(100%, 680px); text-align: center; }
        .brand-logo { width: min(250px, 72vw); height: auto; margin-bottom: 2rem; }
        .landing-title { margin: 0 0 .8rem; font-size: clamp(1.8rem, 5vw, 2.65rem); font-weight: 800; letter-spacing: -.04em; }
        .landing-description { max-width: 560px; margin: 0 auto 2rem; color: var(--muted); font-size: .98rem; line-height: 1.7; }
        .login-link {
            display: inline-flex; align-items: center; gap: .45rem; margin-bottom: 1.4rem;
            color: var(--blue); font-size: .86rem; font-weight: 700; text-decoration: none;
        }
        .login-link:hover { color: var(--blue-dark); text-decoration: underline; }
        .access-card {
            padding: 1.5rem; text-align: left; background: #fff; border: 1px solid #dbe5f1;
            border-radius: 16px; box-shadow: 0 16px 45px rgba(15, 23, 42, .1);
        }
        .access-card h2 { margin: 0 0 .35rem; font-size: 1rem; font-weight: 700; }
        .access-card p { margin: 0 0 1rem; color: var(--muted); font-size: .82rem; }
        .access-form { display: flex; gap: .65rem; }
        .access-form input {
            min-width: 0; flex: 1; padding: .75rem .9rem; border: 1px solid #cbd5e1; border-radius: 9px;
            color: var(--navy); font: inherit; font-size: .88rem; outline: none;
        }
        .access-form input:focus { border-color: var(--blue); box-shadow: 0 0 0 3px rgba(37, 99, 235, .13); }
        .access-form button {
            padding: .75rem 1rem; border: 0; border-radius: 9px; background: var(--blue); color: #fff;
            cursor: pointer; font: inherit; font-size: .85rem; font-weight: 700;
        }
        .access-form button:hover { background: var(--blue-dark); }
        .form-error { margin: .85rem 0 0; color: #b91c1c; font-size: .8rem; }
        .survey-success-notification {
            position: fixed; z-index: 10; top: 1.25rem; right: 1.25rem;
            display: flex; align-items: flex-start; gap: .75rem; width: min(390px, calc(100vw - 2rem));
            padding: 1rem 1.1rem; border: 1px solid #86efac; border-radius: 12px;
            background: #f0fdf4; color: #166534; box-shadow: 0 12px 30px rgba(15, 23, 42, .16); text-align: left;
        }
        .survey-success-notification i { font-size: 1.35rem; line-height: 1.2; }
        .survey-success-notification strong { display: block; font-size: .9rem; margin-bottom: .15rem; }
        .survey-success-notification span { display: block; font-size: .78rem; line-height: 1.45; }
        @media (max-width: 575.98px) {
            body { padding: 1rem; }
            .brand-logo { margin-bottom: 1.5rem; }
            .access-card { padding: 1.2rem; }
            .access-form { flex-direction: column; }
            .access-form button { width: 100%; }
            .survey-success-notification { top: .75rem; right: 1rem; }
        }
    </style>
</head>
<body>
    @if(session('success'))
        <div class="survey-success-notification" role="alert" aria-live="polite">
            <i class="bi bi-check-circle-fill" aria-hidden="true"></i>
            <div>
                <strong>Kuesioner berhasil tersimpan</strong>
                <span>{{ session('success') }} Terima kasih atas waktu dan partisipasi Anda.</span>
            </div>
        </div>
    @endif

    <main class="landing-main">
        <img src="{{ asset('assets/images/logo/undika.png') }}" class="brand-logo" alt="Universitas Dinamika">
        <h1 class="landing-title">Tracer Study Evaluasi Pengguna Lulusan</h1>
        <p class="landing-description">Masukkan kode akses yang Anda terima untuk mengisi kuesioner evaluasi lulusan.</p>
        <a href="{{ route('login') }}" class="login-link"><i class="bi bi-box-arrow-in-right"></i> Login Admin / User</a>

        <section class="access-card" aria-labelledby="survey-access-title">
            <h2 id="survey-access-title"><i class="bi bi-key-fill text-primary me-1"></i> Akses kuesioner</h2>
            <p>Masukkan kode akses survei Anda untuk melanjutkan.</p>
            <form action="{{ route('survey.access') }}" method="POST" class="access-form">
                @csrf
                <input type="text" name="code" placeholder="Masukkan kode survei..." autocomplete="off" required>
                <button type="submit"><i class="bi bi-arrow-right-circle-fill me-1"></i> Lanjut</button>
            </form>
            @if(session('error'))
                <p class="form-error"><i class="bi bi-exclamation-circle me-1"></i>{{ session('error') }}</p>
            @endif
        </section>
    </main>

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
