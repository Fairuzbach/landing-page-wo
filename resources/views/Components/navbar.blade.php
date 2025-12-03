<nav x-data="{ mobileMenuOpen: false }"
    class="fixed w-full z-50 top-0 start-0 border-b border-slate-200/50 bg-white/80 backdrop-blur-md transition-all duration-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-wrap items-center justify-between mx-auto p-4">

            {{-- 1. LOGO --}}
            <a href="/" class="flex items-center space-x-3 rtl:space-x-reverse group">
                <div class="p-2 bg-indigo-600 rounded-lg shadow-lg group-hover:scale-105 transition-transform">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z">
                        </path>
                    </svg>
                </div>
                <span class="self-center text-xl font-bold whitespace-nowrap text-slate-800">
                    Work<span class="text-indigo-600">Order</span>
                </span>
            </a>

            {{-- 2. MOBILE MENU BUTTON --}}
            <button @click="mobileMenuOpen = !mobileMenuOpen" type="button"
                class="inline-flex items-center p-2 w-10 h-10 justify-center text-sm text-slate-500 rounded-lg md:hidden hover:bg-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-200">
                <span class="sr-only">Open main menu</span>
                <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                    viewBox="0 0 17 14">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M1 1h15M1 7h15M1 13h15" />
                </svg>
            </button>

            {{-- 3. DESKTOP MENU --}}
            <div class="hidden w-full md:block md:w-auto" id="navbar-default">
                <ul
                    class="font-medium flex flex-col p-4 md:p-0 mt-4 border border-slate-100 rounded-lg md:flex-row md:space-x-8 rtl:space-x-reverse md:mt-0 md:border-0 md:items-center">
                    <li><a href="#"
                            class="block py-2 px-3 text-white bg-indigo-700 rounded md:bg-transparent md:text-indigo-700 md:p-0">Home</a>
                    </li>
                    <li><a href="#features"
                            class="block py-2 px-3 text-slate-700 rounded hover:bg-slate-100 md:hover:bg-transparent md:border-0 md:hover:text-indigo-700 md:p-0 transition-colors">Fitur</a>
                    </li>
                    <li><a href="#contact"
                            class="block py-2 px-3 text-slate-700 rounded hover:bg-slate-100 md:hover:bg-transparent md:border-0 md:hover:text-indigo-700 md:p-0 transition-colors">Bantuan</a>
                    </li>

                    {{-- AUTH BUTTONS --}}
                    <li class="md:ml-4 flex items-center gap-2">
                        @if (Route::has('login'))
                            @auth
                                <a href="{{ url('/dashboard') }}"
                                    class="text-white bg-indigo-600 hover:bg-indigo-700 focus:ring-4 focus:ring-indigo-300 font-medium rounded-full text-sm px-5 py-2.5 shadow-md">Dashboard</a>
                            @else
                                <a href="{{ route('login') }}"
                                    class="text-slate-700 hover:text-indigo-600 font-medium text-sm px-4 py-2 transition-colors">Masuk</a>
                                @if (Route::has('register'))
                                    <a href="{{ route('register') }}"
                                        class="text-white bg-slate-900 hover:bg-slate-800 focus:ring-4 focus:ring-slate-300 font-medium rounded-full text-sm px-5 py-2.5 shadow-md hover:shadow-lg">Daftar</a>
                                @endif
                            @endauth
                        @endif
                    </li>
                </ul>
            </div>
        </div>

        {{-- 4. MOBILE DROPDOWN --}}
        <div x-show="mobileMenuOpen" x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 -translate-y-2"
            class="md:hidden border-t border-slate-200 bg-white/95 backdrop-blur-xl">
            <ul class="flex flex-col p-4 font-medium space-y-2">
                <li><a href="#" class="block py-2 px-3 text-white bg-indigo-600 rounded">Home</a></li>
                <li><a href="#" class="block py-2 px-3 text-slate-700 rounded hover:bg-slate-100">Fitur</a></li>
                <li><a href="#" class="block py-2 px-3 text-slate-700 rounded hover:bg-slate-100">Bantuan</a></li>
                <li class="pt-4 border-t border-slate-100 flex flex-col gap-2">
                    @auth
                        <a href="{{ url('/dashboard') }}"
                            class="text-center w-full text-white bg-indigo-600 hover:bg-indigo-700 font-medium rounded-lg text-sm px-5 py-2.5">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}"
                            class="text-center w-full text-slate-700 bg-slate-100 hover:bg-slate-200 font-medium rounded-lg text-sm px-5 py-2.5">Masuk</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}"
                                class="text-center w-full text-white bg-slate-900 hover:bg-slate-800 font-medium rounded-lg text-sm px-5 py-2.5">Daftar
                                Akun</a>
                        @endif
                    @endauth
                </li>
            </ul>
        </div>
    </div>
</nav>
