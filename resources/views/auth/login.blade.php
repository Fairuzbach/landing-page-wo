<x-guest-layout>
    {{-- Container Utama dengan Animasi Masuk --}}
    <div x-data="{ loading: false }" x-init="$el.classList.remove('opacity-0', 'translate-y-4')"
        class="opacity-0 translate-y-4 transition-all duration-700 ease-out">

        <div class="mb-8 text-center">
            <div
                class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-indigo-100 text-indigo-600 mb-4 animate-bounce-slow">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1">
                    </path>
                </svg>
            </div>
            <h2 class="text-3xl font-extrabold text-gray-900 tracking-tight">Selamat Datang!</h2>
            <p class="text-sm text-gray-500 mt-2">Silakan masuk untuk melanjutkan akses.</p>
        </div>

        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form method="POST" action="{{ route('login') }}" @submit="loading = true">
            @csrf

            {{-- Input Email dengan Ikon --}}
            <div class="group">
                <x-input-label for="email" :value="__('Email')"
                    class="group-focus-within:text-indigo-600 transition-colors" />
                <div class="relative mt-1">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400 group-focus-within:text-indigo-500 transition-colors"
                            xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
                            <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
                        </svg>
                    </div>
                    <x-text-input id="email"
                        class="block w-full pl-10 rounded-xl border-gray-300 focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500 transition-all duration-300 hover:border-indigo-400"
                        type="email" name="email" :value="old('email')" required autofocus autocomplete="username"
                        placeholder="nama@email.com" />
                </div>
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            {{-- Input Password dengan Toggle --}}
            <div class="mt-5 group" x-data="{ show: false }">
                <x-input-label for="password" :value="__('Password')"
                    class="group-focus-within:text-indigo-600 transition-colors" />
                <div class="relative mt-1">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400 group-focus-within:text-indigo-500 transition-colors"
                            xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z"
                                clip-rule="evenodd" />
                        </svg>
                    </div>
                    <x-text-input id="password"
                        class="block w-full pl-10 pr-10 rounded-xl border-gray-300 focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500 transition-all duration-300 hover:border-indigo-400"
                        ::type="show ? 'text' : 'password'" name="password" required autocomplete="current-password"
                        placeholder="••••••••" />

                    <button type="button" @click="show = !show"
                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-indigo-600 focus:outline-none transition-colors cursor-pointer">
                        <svg x-show="!show" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        <svg x-show="show" style="display: none;" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                        </svg>
                    </button>
                </div>
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <div class="flex items-center justify-between mt-6">
                <label for="remember_me" class="inline-flex items-center group cursor-pointer">
                    <input id="remember_me" type="checkbox"
                        class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500 cursor-pointer transition-transform duration-200 ease-in-out group-hover:scale-110"
                        name="remember">
                    <span
                        class="ms-2 text-sm text-gray-600 group-hover:text-gray-900 transition-colors">{{ __('Ingat saya') }}</span>
                </label>



                @if (Route::has('password.request'))
                    <a class="text-sm text-indigo-600 hover:text-indigo-800 font-medium hover:underline transition-all"
                        href="{{ route('password.request') }}">
                        {{ __('Lupa password?') }}
                    </a>
                @endif
                <a class="text-sm text-indigo-600 hover:text-indigo-800 font-medium hover:underline transition-all"
                    href="{{ route('register') }}">
                    {{ __('Belum punya akun?') }}
                </a>
            </div>

            <div class="mt-8">
                <button type="submit"
                    class="w-full flex justify-center py-3 px-4 border border-transparent rounded-xl shadow-sm text-sm font-bold text-white bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-700 hover:to-indigo-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transform transition hover:-translate-y-0.5 hover:shadow-lg active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed"
                    :disabled="loading">

                    {{-- Spinner Loading --}}
                    <svg x-show="loading" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white"
                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                            stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                        </path>
                    </svg>

                    <span x-text="loading ? 'Memproses...' : 'Masuk'"></span>
                </button>
            </div>
        </form>
    </div>
</x-guest-layout>
