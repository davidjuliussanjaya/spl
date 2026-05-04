<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Akses Survey & Login</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .bg-glass {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.5);
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center bg-gradient-to-br from-cyan-300 via-blue-400 to-indigo-500 relative overflow-hidden p-4">
    
    <!-- Decorative background elements -->
    <div class="absolute top-0 left-0 w-96 h-96 bg-white opacity-20 rounded-full mix-blend-overlay filter blur-3xl transform -translate-x-1/2 -translate-y-1/2"></div>
    <div class="absolute bottom-0 right-0 w-96 h-96 bg-pink-300 opacity-30 rounded-full mix-blend-overlay filter blur-3xl transform translate-x-1/3 translate-y-1/3"></div>

    <div class="w-full max-w-md z-10">
        <!-- Glassmorphism Card -->
        <div class="bg-glass shadow-2xl rounded-3xl p-8 overflow-hidden relative">
            
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-gradient-to-tr from-blue-600 to-cyan-400 text-white shadow-lg mb-4 transform rotate-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 -rotate-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-gray-800 tracking-tight">Sistem Evaluasi</h2>
                <p class="text-sm text-gray-500 mt-1 font-medium">Portal Layanan Lulusan & Perusahaan</p>
            </div>

            <div x-data="{ activeTab: 'survey' }" class="w-full">
                <!-- Tab Navigation (Pill style inside glass) -->
                <div class="flex p-1.5 mb-8 bg-gray-100/60 rounded-2xl shadow-inner">
                    <button @click="activeTab = 'survey'" 
                            :class="activeTab === 'survey' ? 'bg-white text-blue-600 shadow-md font-bold' : 'text-gray-500 hover:text-gray-700 font-medium'"
                            class="flex-1 py-2.5 text-sm rounded-xl transition-all duration-300">
                        Akses Survey
                    </button>
                    <button @click="activeTab = 'login'" 
                            :class="activeTab === 'login' ? 'bg-white text-blue-600 shadow-md font-bold' : 'text-gray-500 hover:text-gray-700 font-medium'"
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
                                   class="block w-full pl-12 pr-4 py-3.5 text-lg font-mono font-bold tracking-widest uppercase bg-white/70 border-0 focus:ring-2 focus:ring-blue-400 focus:bg-white rounded-2xl shadow-sm transition-all duration-200 placeholder-gray-400 text-gray-800" 
                                   type="text" 
                                   name="code" 
                                   placeholder="KODE-AKSES" 
                                   required />
                        </div>

                        <div class="mt-8">
                            <button type="submit" class="w-full flex justify-center py-3.5 px-4 rounded-2xl shadow-lg text-sm font-bold text-white bg-gradient-to-r from-blue-500 to-cyan-400 hover:from-blue-600 hover:to-cyan-500 focus:outline-none focus:ring-4 focus:ring-cyan-300/50 transition-all duration-300 transform hover:-translate-y-0.5 active:translate-y-0">
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
                            <input id="email" class="block w-full pl-12 pr-4 py-3.5 bg-white/70 border-0 focus:ring-2 focus:ring-blue-400 focus:bg-white rounded-2xl shadow-sm transition-all duration-200 text-gray-800 placeholder-gray-400 font-medium" type="email" name="email" :value="old('email')" placeholder="Email Staff" required autofocus />
                        </div>

                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                            </div>
                            <input id="password" class="block w-full pl-12 pr-4 py-3.5 bg-white/70 border-0 focus:ring-2 focus:ring-blue-400 focus:bg-white rounded-2xl shadow-sm transition-all duration-200 text-gray-800 placeholder-gray-400 font-medium" type="password" name="password" placeholder="Password" required />
                        </div>

                        <div class="flex items-center justify-between mt-2 px-1">
                            <label for="remember_me" class="inline-flex items-center cursor-pointer">
                                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-blue-500 shadow-sm focus:ring-blue-400" name="remember">
                                <span class="ms-2 text-xs font-semibold text-gray-500">Ingat Saya</span>
                            </label>
                        </div>

                        <div class="mt-8">
                            <button type="submit" class="w-full flex justify-center py-3.5 px-4 rounded-2xl shadow-lg text-sm font-bold text-white bg-gray-800 hover:bg-gray-900 focus:outline-none focus:ring-4 focus:ring-gray-300/50 transition-all duration-300 transform hover:-translate-y-0.5 active:translate-y-0">
                                Masuk ke Dashboard
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <p class="text-center text-white/80 text-xs mt-8 font-semibold tracking-wide">
            &copy; {{ date('Y') }} SISTEM EVALUASI LULUSAN
        </p>
    </div>
</body>
</html>