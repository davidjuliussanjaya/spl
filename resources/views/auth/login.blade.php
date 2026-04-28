<x-guest-layout>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        
        <div class="p-4 border-r-0 md:border-r border-gray-200">
            <h3 class="mb-4 text-lg font-bold text-gray-700 dark:text-gray-300">Login Staff</h3>
            <x-auth-session-status class="mb-4" :status="session('status')" />

            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div>
                    <x-input-label for="email" :value="__('Email')" />
                    <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus />
                </div>

                <div class="mt-4">
                    <x-input-label for="password" :value="__('Password')" />
                    <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required />
                </div>

                <div class="flex items-center justify-end mt-4">
                    <x-primary-button class="w-full justify-center">
                        {{ __('Log in') }}
                    </x-primary-button>
                </div>
            </form>
        </div>

        <div class="p-4 bg-gray-50 dark:bg-gray-800 rounded-lg shadow-inner">
            <h3 class="mb-2 text-lg font-bold text-indigo-600 dark:text-indigo-400">Akses Survey</h3>
            <p class="mb-4 text-sm text-gray-600 dark:text-gray-400">Masukkan kode akses unik yang diberikan untuk mulai mengisi survey perusahaan.</p>

            @if (session('error'))
                <div class="mb-4 text-sm text-red-600">
                    {{ session('error') }}
                </div>
            @endif

            <form method="POST" action="{{ route('survey.access') }}">
                @csrf
                <div>
                    <x-input-label for="code" :value="__('Kode Akses')" />
                    <x-text-input id="code" class="block mt-1 w-full text-center font-bold tracking-widest" 
                                 type="text" name="code" placeholder="KODE-AKSES" 
                                 required style="text-transform: uppercase" />
                </div>

                <div class="mt-6">
                    <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        Mulai Mengisi Survey
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>