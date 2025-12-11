<x-guest-layout>
    <div x-data="{ loading: false }" x-init="$el.classList.remove('opacity-0', 'scale-95')" class="opacity-0 scale-95 transition-all duration-500 ease-out">

        <div class="mb-6 text-center">
            <h2 class="text-2xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-indigo-600 to-purple-600">
                Buat Akun Baru</h2>
            <p class="text-sm text-gray-500 mt-1">Bergabung bersama kami untuk mengelola laporan</p>
        </div>

        <form method="POST" action="{{ route('register') }}" @submit="loading = true">
            @csrf

            {{-- Grid untuk Nama & Username --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                {{-- Nama Lengkap --}}
                <div class="group">
                    <x-input-label for="name" :value="__('Nama Lengkap')" class="group-focus-within:text-indigo-600" />
                    <x-text-input id="name"
                        class="block mt-1 w-full rounded-xl border-gray-300 focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500 transition-all hover:bg-gray-50"
                        type="text" name="name" :value="old('name')" required autofocus autocomplete="name"
                        placeholder="John Doe" />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>

                {{-- Username --}}
                <div class="group">
                    <x-input-label for="username" :value="__('Username')" class="group-focus-within:text-indigo-600" />
                    <div class="relative mt-1">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-gray-400">@</span>
                        </div>
                        <x-text-input id="username"
                            class="block w-full pl-8 rounded-xl border-gray-300 focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500 transition-all hover:bg-gray-50"
                            type="text" name="username" :value="old('username')" required autocomplete="username"
                            placeholder="johndoe" />
                    </div>
                    <x-input-error :messages="$errors->get('username')" class="mt-2" />
                </div>
            </div>

            {{-- Email --}}
            <div class="mt-4 group">
                <x-input-label for="email" :value="__('Email')" class="group-focus-within:text-indigo-600" />
                <x-text-input id="email"
                    class="block mt-1 w-full rounded-xl border-gray-300 focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500 transition-all hover:bg-gray-50"
                    type="email" name="email" :value="old('email')" required autocomplete="email"
                    placeholder="nama@perusahaan.com" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            {{-- Divisi (Styled Select) --}}
            <div class="mt-4 relative group">
                <x-input-label for="divisi" :value="__('Divisi')" class="group-focus-within:text-indigo-600" />
                <div class="relative">
                    <select id="divisi" name="divisi"
                        class="block mt-1 w-full rounded-xl border-gray-300 focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500 appearance-none bg-white transition-all hover:border-indigo-400 cursor-pointer"
                        required>
                        <option value="" disabled selected>Pilih Divisi Anda</option>
                        <option value="Facility" {{ old('divisi') == 'Facility' ? 'selected' : '' }}>🏭 Facility
                        </option>
                        <option value="Maintenance" {{ old('divisi') == 'Maintenance' ? 'selected' : '' }}>🔧
                            Maintenance</option>
                        <option value="Engineering" {{ old('divisi') == 'Engineering' ? 'selected' : '' }}>⚙️
                            Engineering</option>
                        <option value="General Affair" {{ old('divisi') == 'General Affair' ? 'selected' : '' }}>🏢
                            General Affair</option>
                    </select>
                    {{-- Custom Arrow Icon --}}
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                        <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                            <path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z" />
                        </svg>
                    </div>
                </div>
                <x-input-error :messages="$errors->get('divisi')" class="mt-2" />
            </div>

            {{-- Password Fields Grid --}}
            <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4" x-data="{ show: false }">
                {{-- Password --}}
                <div class="group">
                    <x-input-label for="password" :value="__('Password')" class="group-focus-within:text-indigo-600" />
                    <div class="relative mt-1">
                        <x-text-input id="password"
                            class="block w-full rounded-xl border-gray-300 focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500 pr-10 transition-all hover:bg-gray-50"
                            ::type="show ? 'text' : 'password'" name="password" required autocomplete="new-password"
                            placeholder="••••••••" />
                        <button type="button" @click="show = !show"
                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-indigo-600 cursor-pointer">
                            <svg x-show="!show" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            <svg x-show="show" style="display: none;" class="h-5 w-5" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                            </svg>
                        </button>
                    </div>
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                {{-- Konfirmasi Password --}}
                <div class="group">
                    <x-input-label for="password_confirmation" :value="__('Konfirmasi')"
                        class="group-focus-within:text-indigo-600" />
                    <x-text-input id="password_confirmation"
                        class="block w-full mt-1 rounded-xl border-gray-300 focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500 transition-all hover:bg-gray-50"
                        ::type="show ? 'text' : 'password'" name="password_confirmation" required autocomplete="new-password"
                        placeholder="••••••••" />
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                </div>
            </div>

            <div class="mt-8">
                <button type="submit"
                    class="w-full flex justify-center py-3 px-4 border border-transparent rounded-xl shadow-md text-base font-bold text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transform transition hover:-translate-y-1 hover:shadow-lg disabled:opacity-50 disabled:cursor-not-allowed"
                    :disabled="loading">
                    <svg x-show="loading" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white"
                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                            stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                        </path>
                    </svg>
                    <span x-text="loading ? 'Mendaftarkan...' : 'Daftar Sekarang'"></span>
                </button>
            </div>

            <div class="mt-6 text-center">
                <span class="text-sm text-gray-500">Sudah punya akun? </span>
                <a class="text-sm font-semibold text-indigo-600 hover:text-indigo-800 hover:underline transition-all"
                    href="{{ route('login') }}">
                    Masuk di sini
                </a>
            </div>
        </form>
    </div>
</x-guest-layout>
