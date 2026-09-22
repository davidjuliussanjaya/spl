<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login Sistem — Tracer Study</title>
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
            background: linear-gradient(135deg, #eff6ff 0%, #f8fafc 50%, #e0f2fe 100%);
        }
        .login-wrap { width: min(100%, 420px); }
        .login-card { padding: 2rem; background: #fff; border: 1px solid #dbe5f1; border-radius: 18px; box-shadow: 0 16px 45px rgba(15, 23, 42, .1); }
        .brand { margin-bottom: 2rem; text-align: center; }
        .brand-logo { width: min(230px, 70vw); height: auto; }
        .brand h1 { margin: 1.1rem 0 .4rem; font-size: 1.3rem; }
        .brand p { margin: 0; color: var(--muted); font-size: .84rem; }
        .alert { margin-bottom: 1rem; padding: .8rem .9rem; border-radius: 10px; font-size: .82rem; line-height: 1.45; }
        .alert-error { background: #fef2f2; border: 1px solid #fecaca; color: #b91c1c; }
        .alert-status { background: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; }
        .field { margin-bottom: 1rem; }
        .field label { display: block; margin-bottom: .4rem; color: #334155; font-size: .8rem; font-weight: 700; }
        .input-wrap { position: relative; }
        .input-wrap > i {
            position: absolute; top: 50%; left: .9rem; transform: translateY(-50%);
            color: #94a3b8; font-size: .95rem; line-height: 1; pointer-events: none;
        }
        .input-wrap input {
            width: 100%; padding: .78rem 2.9rem; border: 1px solid #cbd5e1; border-radius: 10px;
            color: var(--navy); font: inherit; font-size: .9rem; outline: none;
        }
        .input-wrap input:focus { border-color: var(--blue); box-shadow: 0 0 0 3px rgba(37, 99, 235, .13); }
        .password-toggle {
            position: absolute; top: 50%; right: .45rem; transform: translateY(-50%);
            width: 2.25rem; height: 2.25rem; padding: 0; border: 0; border-radius: 8px;
            display: inline-flex; align-items: center; justify-content: center;
            background: transparent; color: #94a3b8; cursor: pointer; line-height: 1;
        }
        .password-toggle:hover, .password-toggle:focus-visible { background: #eff6ff; color: var(--blue); outline: none; }
        .password-toggle i { position: static; transform: none; font-size: 1rem; line-height: 1; }
        .remember { display: flex; align-items: center; gap: .45rem; margin: 1.1rem 0 1.4rem; color: var(--muted); font-size: .8rem; }
        .submit-button { width: 100%; padding: .82rem 1rem; border: 0; border-radius: 10px; background: var(--blue); color: #fff; cursor: pointer; font: inherit; font-size: .9rem; font-weight: 700; }
        .submit-button:hover { background: var(--blue-dark); }
        .back-link { display: block; margin-top: 1.25rem; color: var(--muted); font-size: .8rem; text-align: center; text-decoration: none; }
        .back-link:hover { color: var(--blue); }
        .copyright { margin: 1.3rem 0 0; color: var(--muted); font-size: .7rem; font-weight: 600; text-align: center; }
        @media (max-width: 480px) { body { padding: 1rem; } .login-card { padding: 1.5rem 1.25rem; } }
    </style>
</head>
<body>
    <main class="login-wrap">
        <section class="login-card" aria-labelledby="login-title">
            <div class="brand">
                <img src="{{ asset('assets/images/logo/undika.png') }}" class="brand-logo" alt="Universitas Dinamika">
                <h1 id="login-title">Login Sistem</h1>
                <p>Portal Tracer Study &amp; Evaluasi Lulusan</p>
            </div>

            @if(session('status'))
                <div class="alert alert-status" role="status">{{ session('status') }}</div>
            @endif
            @if($errors->any())
                <div class="alert alert-error" role="alert">
                    @foreach($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="field">
                    <label for="email">Email</label>
                    <div class="input-wrap">
                        <i class="bi bi-envelope"></i>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" autocomplete="email" required autofocus>
                    </div>
                </div>
                <div class="field">
                    <label for="password">Password</label>
                    <div class="input-wrap">
                        <i class="bi bi-lock"></i>
                        <input id="password" type="password" name="password" autocomplete="current-password" required>
                        <button type="button" class="password-toggle" id="togglePassword" aria-label="Tampilkan password"><i class="bi bi-eye"></i></button>
                    </div>
                </div>
                <label class="remember" for="remember">
                    <input id="remember" type="checkbox" name="remember"> Ingat saya
                </label>
                <button type="submit" class="submit-button"><i class="bi bi-box-arrow-in-right me-1"></i> Masuk ke Dashboard</button>
            </form>
            <a href="{{ url('/') }}" class="back-link"><i class="bi bi-arrow-left me-1"></i> Kembali ke akses kuesioner</a>
        </section>
        <p class="copyright">&copy; {{ date('Y') }} Universitas Dinamika Surabaya — Sistem Tracer Study</p>
    </main>

    <script>
        document.getElementById('togglePassword').addEventListener('click', function () {
            const password = document.getElementById('password');
            const isHidden = password.type === 'password';
            password.type = isHidden ? 'text' : 'password';
            this.innerHTML = `<i class="bi bi-eye${isHidden ? '-slash' : ''}"></i>`;
            this.setAttribute('aria-label', isHidden ? 'Sembunyikan password' : 'Tampilkan password');
        });
    </script>
</body>
</html>
