<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Akses Survey & Login</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --spl-brand: #2563EB;
            --spl-brand-dark: #1D4ED8;
            --spl-brand-soft: #EFF6FF;
            --spl-bg: #F6F8FC;
            --spl-border: #E2E8F0;
            --spl-text: #0F172A;
            --spl-muted: #64748B;
        }
        body { font-family: 'Inter', sans-serif; color: var(--spl-text); background: var(--spl-bg); }
        .bg-glass {
            background: #FFFFFF;
            border: 1px solid var(--spl-border);
            box-shadow: 0 1px 2px rgba(15,23,42,.04), 0 16px 32px rgba(15,23,42,.08);
        }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center relative overflow-hidden p-4" style="background:#F6F8FC;">

    <!-- Decorative background elements -->
    <div class="absolute top-0 left-0 w-96 h-96 opacity-20 rounded-full filter blur-3xl transform -translate-x-1/2 -translate-y-1/2" style="background:#BFDBFE;"></div>
    <div class="absolute bottom-0 right-0 w-96 h-96 opacity-20 rounded-full filter blur-3xl transform translate-x-1/3 translate-y-1/3" style="background:#99F6E4;"></div>
    <div class="absolute top-1/2 left-1/4 w-64 h-64 opacity-20 rounded-full filter blur-2xl" style="background:#DBEAFE;"></div>

    <div class="w-full max-w-md z-10">
        <!-- Glassmorphism Card -->
        <div class="bg-glass rounded-2xl p-8 overflow-hidden relative">
            
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl text-white shadow-lg mb-4" style="background: linear-gradient(135deg, #2563EB, #1D4ED8);">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-9 w-9" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422A12.083 12.083 0 0121 21H3a12.083 12.083 0 012.84-10.422L12 14z" />
                    </svg>
                </div>
                <h2 class="text-xl font-bold tracking-tight" style="color:#0F172A;">Universitas Dinamika</h2>
                <p class="text-xs font-bold mt-0.5 tracking-widest uppercase" style="color:#2563EB;">Surabaya</p>
                <p class="text-sm text-gray-500 mt-1 font-medium">Portal Tracer Study & Evaluasi Lulusan</p>
            </div>

            <div x-data="{ activeTab: 'survey', showPassword: false }"
                 @pointerup.window="showPassword = false"
                 @pointercancel.window="showPassword = false"
                 class="w-full">
                <!-- Tab Navigation (Pill style inside glass) -->
                <div class="flex p-1.5 mb-8 bg-gray-100/60 rounded-2xl shadow-inner">
                    <button @click="activeTab = 'survey'"
                            :class="activeTab === 'survey' ? 'bg-white shadow-md font-bold' : 'text-gray-500 hover:text-gray-700 font-medium'"
                            :style="activeTab === 'survey' ? 'color:#2563EB;' : ''"
                            class="flex-1 py-2.5 text-sm rounded-xl transition-all duration-300">
                        Akses Survey
                    </button>
                    <button @click="activeTab = 'login'"
                            :class="activeTab === 'login' ? 'bg-white shadow-md font-bold' : 'text-gray-500 hover:text-gray-700 font-medium'"
                            :style="activeTab === 'login' ? 'color:#2563EB;' : ''"
                            class="flex-1 py-2.5 text-sm rounded-xl transition-all duration-300">
                        Login Admin
                    </button>
                </div>

                <!-- Session Status & Errors -->
                @if (session('status'))
                    <div class="mb-4 p-3 rounded-xl bg-green-50 text-sm font-medium text-green-600 border border-green-200">
                        {{ session('status') }}
                    </div>
                @endif
                @if (session('error'))
                    <div class="mb-4 p-3 rounded-xl bg-red-50 text-sm font-medium text-red-600 border border-red-200">
                        {{ session('error') }}
                    </div>
                @endif
                @if ($errors->any())
                    <div class="mb-4 p-3 rounded-xl bg-red-50 text-sm font-medium text-red-600 border border-red-200">
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Tab Content: Survey -->
                <div x-show="activeTab === 'survey'" 
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     class="space-y-4">
                    
                    <p class="text-sm text-gray-500 text-center mb-6 px-2">Masukkan kode akses unik Anda untuk mulai mengisi kuesioner.</p>

                    <form method="POST" action="{{ route('survey.access') }}">
                        @csrf
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                                </svg>
                            </div>
                            <input id="code"
                                   class="block w-full pl-12 pr-4 py-3.5 text-lg font-mono font-bold tracking-widest uppercase bg-white/70 border-0 rounded-2xl shadow-sm transition-all duration-200 placeholder-gray-400 text-gray-800"
                                   style="outline:none;"
                                   onfocus="this.style.boxShadow='0 0 0 3px rgba(37,99,235,0.15)';this.style.background='white';"
                                   onblur="this.style.boxShadow='';this.style.background='rgba(255,255,255,0.7)';"
                                   type="text"
                                   name="code"
                                   placeholder="KODE-AKSES"
                                   required />
                        </div>

                        <div class="mt-8">
                            <button type="submit" class="w-full flex justify-center py-3.5 px-4 rounded-xl shadow-lg text-sm font-bold text-white transition-all duration-300 transform hover:-translate-y-0.5 active:translate-y-0" style="background:#2563EB;" onmouseover="this.style.background='#1D4ED8';" onmouseout="this.style.background='#2563EB';">
                                Mulai Survey Sekarang
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Tab Content: Login -->
                <div x-show="activeTab === 'login'" style="display: none;"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4"
                     x-transition:enter-end="opacity-100 translate-y-0">
                    
                    <form method="POST" action="{{ route('login') }}" class="space-y-4">
                        @csrf
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                                </svg>
                            </div>
                            <input id="email" class="block w-full pl-12 pr-4 py-3.5 bg-white/70 border-0 rounded-xl shadow-sm transition-all duration-200 text-gray-800 placeholder-gray-400 font-medium" style="outline:none;" onfocus="this.style.boxShadow='0 0 0 3px rgba(37,99,235,0.15)';this.style.background='white';" onblur="this.style.boxShadow='';this.style.background='rgba(255,255,255,0.7)';" type="email" name="email" :value="old('email')" placeholder="Email Staff" required autofocus />
                        </div>

                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                            </div>
                            <input id="password" class="block w-full pl-12 pr-12 py-3.5 bg-white/70 border-0 rounded-xl shadow-sm transition-all duration-200 text-gray-800 placeholder-gray-400 font-medium" style="outline:none;" onfocus="this.style.boxShadow='0 0 0 3px rgba(37,99,235,0.15)';this.style.background='white';" onblur="this.style.boxShadow='';this.style.background='rgba(255,255,255,0.7)';" :type="showPassword ? 'text' : 'password'" name="password" placeholder="Password" required />
                            <button type="button"
                                    class="absolute inset-y-0 right-0 flex items-center pr-4 text-gray-400 transition-colors hover:text-blue-600 focus:outline-none focus:text-blue-600"
                                    aria-label="Tahan untuk menampilkan password"
                                    title="Tahan untuk menampilkan password"
                                    @pointerdown.prevent="showPassword = true"
                                    @keydown.space.prevent="showPassword = true"
                                    @keyup.space="showPassword = false"
                                    @keydown.enter.prevent="showPassword = true"
                                    @keyup.enter="showPassword = false"
                                    @blur="showPassword = false">
                                <svg x-show="!showPassword" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <svg x-show="showPassword" x-cloak xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a10.05 10.05 0 012.442-4.568M6.223 6.223A9.956 9.956 0 0112 5c4.478 0 8.268 2.943 9.542 7a9.978 9.978 0 01-4.132 5.411M6.223 6.223L3 3m3.223 3.223l3.65 3.65m0 0A3 3 0 0014.12 14.12m-4.247-4.247L14.12 14.12M21 21l-3.588-3.589" />
                                </svg>
                            </button>
                        </div>

                        <div class="flex items-center justify-between mt-2 px-1">
                            <label for="remember_me" class="inline-flex items-center cursor-pointer">
                                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-blue-500 shadow-sm focus:ring-blue-400" name="remember">
                                <span class="ms-2 text-xs font-semibold text-gray-500">Ingat Saya</span>
                            </label>
                        </div>

                        <div class="mt-8">
                            <button type="submit" class="w-full flex justify-center py-3.5 px-4 rounded-xl shadow-lg text-sm font-bold text-white transition-all duration-300 transform hover:-translate-y-0.5 active:translate-y-0" style="background:#2563EB;" onmouseover="this.style.background='#1D4ED8';" onmouseout="this.style.background='#2563EB';">
                                Masuk ke Dashboard
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <p class="text-center text-slate-500 text-xs mt-8 font-semibold tracking-wide">
            &copy; {{ date('Y') }} UNIVERSITAS DINAMIKA SURABAYA &mdash; SISTEM TRACER STUDY
        </p>
    </div>
</body>
</html>
