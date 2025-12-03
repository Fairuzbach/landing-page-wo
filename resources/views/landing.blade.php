<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Work Order Management</title>

    {{-- 1. Load Font Google (Inter) agar terlihat modern --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">

    @viteReactRefresh
    @vite(['resources/css/app.css', 'resources/js/app.jsx'])
    @inertiaHead
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        /* Animasi Custom untuk Entrance (Fade In Up) */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-in-up {
            animation: fadeInUp 0.8s ease-out forwards;
        }

        /* Delay utility classes */
        .delay-100 {
            animation-delay: 0.1s;
        }

        .delay-200 {
            animation-delay: 0.2s;
        }

        .delay-300 {
            animation-delay: 0.3s;
        }

        /* Initial state hidden */
        .opacity-0-start {
            opacity: 0;
        }
    </style>
</head>

<body class="antialiased bg-slate-50 text-slate-900 font-sans selection:bg-indigo-500 selection:text-white">
    <x-navbar />
    <div class="min-h-screen relative overflow-hidden flex flex-col justify-center py-12 sm:py-24 pt-24">

        {{-- 3. Background Decoration (Blobs Cahaya) --}}
        <div
            class="absolute top-[-10%] left-[-10%] w-96 h-96 bg-blue-400/20 rounded-full blur-[100px] pointer-events-none">
        </div>
        <div
            class="absolute bottom-[-10%] right-[-10%] w-96 h-96 bg-purple-400/20 rounded-full blur-[100px] pointer-events-none">
        </div>
        <div
            class="absolute top-[20%] right-[10%] w-72 h-72 bg-emerald-400/20 rounded-full blur-[80px] pointer-events-none">
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 w-full">

            {{-- 4. Header Section --}}
            <div class="text-center max-w-3xl mx-auto mb-16 animate-fade-in-up opacity-0-start">
                {{-- Badge Kecil --}}
                <div
                    class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white border border-slate-200 text-slate-600 text-sm font-medium mb-6 shadow-sm hover:shadow-md transition-shadow cursor-default">
                    <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 3.214L13 21l-2.286-6.857L5 12l5.714-3.214L13 3z">
                        </path>
                    </svg>
                    <span>Internal Portal v2.0</span>
                </div>

                {{-- Judul Besar --}}
                <h1 class="text-4xl md:text-6xl font-extrabold tracking-tight text-slate-900 mb-6 leading-tight">
                    Work Order <span
                        class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-indigo-600">Management</span>
                </h1>

                <p class="text-lg md:text-xl text-slate-600 leading-relaxed">
                    Pusat kendali terpadu untuk semua permintaan layanan divisi. <br class="hidden md:block">
                    Pilih departemen tujuan Anda untuk memulai pengajuan tiket.
                </p>
            </div>

            {{-- 5. Cards Grid (Data Divisi) --}}
            @php
                // Anda bisa mengubah link '#' menjadi route('nama.route') nanti
                $divisions = [
                    [
                        'id' => 'it',
                        'name' => 'IT Support',
                        'desc' => 'Hardware, Software, Network & Access',
                        'icon' =>
                            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />',
                        'color' => 'from-blue-500 to-cyan-400',
                        'shadow' => 'shadow-blue-500/20',
                        'bg_hover' => 'group-hover:text-blue-600',
                        'btn_link' => '#',
                    ],
                    [
                        'id' => 'maintenance',
                        'name' => 'Maintenance',
                        'desc' => 'Facility Repairs, AC & Electricity',
                        'icon' =>
                            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />',
                        'color' => 'from-orange-500 to-red-400',
                        'shadow' => 'shadow-orange-500/20',
                        'bg_hover' => 'group-hover:text-orange-600',
                        'btn_link' => '#',
                    ],
                    [
                        'id' => 'hr',
                        'name' => 'Human Resources',
                        'desc' => 'Recruitment, Leave & Administration',
                        'icon' =>
                            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />',
                        'color' => 'from-purple-500 to-pink-400',
                        'shadow' => 'shadow-purple-500/20',
                        'bg_hover' => 'group-hover:text-purple-600',
                        'btn_link' => '#',
                    ],
                    [
                        'id' => 'finance',
                        'name' => 'Finance',
                        'desc' => 'Budgeting, Payroll & Reimbursement',
                        'icon' =>
                            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />',
                        'color' => 'from-emerald-500 to-green-400',
                        'shadow' => 'shadow-emerald-500/20',
                        'bg_hover' => 'group-hover:text-emerald-600',
                        'btn_link' => '#',
                    ],
                    [
                        'id' => 'marketing',
                        'name' => 'Marketing',
                        'desc' => 'Campaigns, Social Media & Branding',
                        'icon' =>
                            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" />',
                        'color' => 'from-rose-500 to-pink-500',
                        'shadow' => 'shadow-rose-500/20',
                        'bg_hover' => 'group-hover:text-rose-600',
                        'btn_link' => '#',
                    ],
                    [
                        'id' => 'operations',
                        'name' => 'Operations',
                        'desc' => 'Logistics, Supply Chain & Inventory',
                        'icon' =>
                            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />',
                        'color' => 'from-indigo-500 to-violet-500',
                        'shadow' => 'shadow-indigo-500/20',
                        'bg_hover' => 'group-hover:text-indigo-600',
                        'btn_link' => '#',
                    ],
                    [
                        'id' => 'engineering',
                        'name' => 'Engineering',
                        'desc' => 'Product Design, R&D & Innovation',
                        'icon' =>
                            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />',
                        'color' => 'from-cyan-600 to-teal-500',
                        'shadow' => 'shadow-cyan-500/20',
                        'bg_hover' => 'group-hover:text-cyan-600',
                        'btn_link' => '#',
                    ],
                ];
            @endphp

            <div
                class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 animate-fade-in-up opacity-0-start delay-200">
                @foreach ($divisions as $division)
                    {{-- Card Link --}}
                    <a href="{{ $division['btn_link'] }}" class="group relative block h-full">
                        {{-- Card Body --}}
                        <div
                            class="h-full bg-white/60 backdrop-blur-xl border border-white/50 rounded-3xl p-6 shadow-lg hover:shadow-2xl transition-all duration-300 transform group-hover:-translate-y-2 {{ $division['shadow'] }}">

                            {{-- Decorative Gradient Overlay (Muncul saat Hover) --}}
                            <div
                                class="absolute inset-0 rounded-3xl bg-gradient-to-br {{ $division['color'] }} opacity-0 group-hover:opacity-5 transition-opacity duration-300 pointer-events-none">
                            </div>

                            {{-- Icon Box --}}
                            <div
                                class="w-14 h-14 rounded-2xl bg-gradient-to-br {{ $division['color'] }} flex items-center justify-center mb-6 shadow-md group-hover:scale-110 transition-transform duration-300">
                                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    {!! $division['icon'] !!}
                                </svg>
                            </div>

                            {{-- Content Text --}}
                            <h3
                                class="text-xl font-bold text-slate-800 mb-2 {{ $division['bg_hover'] }} transition-colors">
                                {{ $division['name'] }}
                            </h3>
                            <p class="text-slate-500 text-sm leading-relaxed mb-6">
                                {{ $division['desc'] }}
                            </p>

                            {{-- Action Footer (Panah) --}}
                            <div
                                class="mt-auto flex items-center text-sm font-semibold text-slate-400 group-hover:text-slate-900 transition-colors">
                                Buat Tiket
                                <svg class="w-4 h-4 ml-2 opacity-0 -translate-x-2 group-hover:opacity-100 group-hover:translate-x-0 transition-all duration-300"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                                </svg>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>

            {{-- 6. Footer Note --}}
            <div class="text-center mt-20 text-slate-400 text-sm animate-fade-in-up opacity-0-start delay-300">
                <p>&copy; {{ date('Y') }} Company Internal System. Butuh bantuan? <a href="#"
                        class="text-blue-500 hover:underline">Hubungi Admin</a></p>
            </div>

        </div>
    </div>
</body>

</html>
