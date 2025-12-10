<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>JEMBO Work Management</title>

    {{-- 1. Load Font Google (Inter) agar terlihat modern --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">


    @vite(['resources/css/app.css', 'resources/js/app.js'])
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

        @keyframes float {

            0%,
            100% {
                transform: translate(0, 0) scale(1);
            }

            33% {
                transform: translate(30px, -50px) scale(1.1);
            }

            66% {
                transform: translate(-20px, 20px) scale(0.9);
            }
        }

        .animate-blob {
            animation: float 10s infinite ease-in-out;
        }

        .animation-delay-2000 {
            animation-delay: 2s;
        }

        .animation-delay-4000 {
            animation-delay: 4s;
        }

        /* 2. Animasi Teks Berkilau (Shimmer) */
        @keyframes shine {
            to {
                background-position: 200% center;
            }
        }

        .animate-text-shimmer {
            background-size: 200% auto;
            animation: shine 3s linear infinite;
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
            class="absolute top-[-10%] left-[-10%] w-96 h-96 bg-blue-400/20 rounded-full blur-[100px] pointer-events-none animate-blob">
        </div>
        <div
            class="absolute bottom-[-10%] right-[-10%] w-96 h-96 bg-purple-400/20 rounded-full blur-[100px] pointer-events-none animate-blob animation-delay-2000">
        </div>
        <div
            class="absolute top-[20%] right-[10%] w-72 h-72 bg-emerald-400/20 rounded-full blur-[80px] pointer-events-none animate-blob animation-delay-4000">
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 w-full">

            {{-- 4. Header Section --}}
            <div class="text-center max-w-3xl mx-auto mb-16 animate-fade-in-up opacity-0-start">
                {{-- Judul Besar --}}
                <h1 class="text-4xl md:text-6xl font-extrabold tracking-tight text-slate-900 mb-6 leading-tight">
                    JEMBO Work <span
                        class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 via-indigo-500 to-blue-600 animate-text-shimmer">Management</span>
                </h1>
            </div>

            {{-- 5. Cards Grid (Data Divisi) --}}
            <div class="p-6">
                {{-- 1. Definisikan variable dengan nama $divisions --}}
                @php
                    // GANTI NAMA DARI $menuItems KE $divisions
                    $divisions = [
                        [
                            'id' => 'maintenance',
                            'name' => 'Work Order Maintenance',
                            'desc' => 'Servis • Mesin • Pemeliharaan',
                            'icon' =>
                                '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />',
                            'color' => 'from-blue-500 to-cyan-400',
                            'shadow' => 'shadow-blue-500/20',
                            'bg_hover' => 'group-hover:text-blue-600',
                            'btn_link' => '#',
                        ],
                        [
                            'id' => 'engineering',
                            'name' => 'Engineering Improvement Order',
                            'desc' => 'Upgrade • Efisiensi • Optimasi',
                            'icon' =>
                                '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />',
                            'color' => 'from-orange-500 to-red-400',
                            'shadow' => 'shadow-orange-500/20',
                            'bg_hover' => 'group-hover:text-orange-600',
                            'btn_link' => route('engineering.wo.index'),
                        ],
                        [
                            'id' => 'generalAffair',
                            'name' => 'Work Order General Affair',
                            'desc' => 'Layanan • Fasilitas • Operasional',
                            'icon' =>
                                '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />',
                            'color' => 'from-purple-500 to-pink-400',
                            'shadow' => 'shadow-purple-500/20',
                            'bg_hover' => 'group-hover:text-purple-600',
                            'btn_link' => route('ga.index'),
                        ],
                        [
                            'id' => 'facility',
                            'name' => 'Work Order Facility',
                            'desc' => 'Description',
                            'icon' =>
                                '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />',
                            'color' => 'from-emerald-500 to-green-400',
                            'shadow' => 'shadow-emerald-500/20',
                            'bg_hover' => 'group-hover:text-emerald-600',
                            'btn_link' => '#',
                        ],
                    ];
                @endphp

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8 items-stretch">
                    @foreach ($divisions as $item)
                        {{-- Wrapper Alpine.js untuk Efek Tilt 3D --}}
                        <div x-data="{
                            transform: 'perspective(1000px) rotateX(0deg) rotateY(0deg) scale(1)',
                            handleMove(e) {
                                const el = this.$refs.card;
                                const { left, top, width, height } = el.getBoundingClientRect();
                                const x = e.clientX - left;
                                const y = e.clientY - top;
                        
                                // Hitung rotasi (maksimal 10 derajat)
                                const xPct = x / width - 0.5;
                                const yPct = y / height - 0.5;
                                const rotateY = xPct * 20;
                                const rotateX = yPct * -20;
                        
                                this.transform = `perspective(1000px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) scale(1.05)`;
                            },
                            handleLeave() {
                                this.transform = 'perspective(1000px) rotateX(0deg) rotateY(0deg) scale(1)';
                            }
                        }" class="h-full">
                            <a href="{{ $item['btn_link'] }}" class="group block h-full">
                                <div x-ref="card" @mousemove="handleMove($event)" @mouseleave="handleLeave()"
                                    :style="`transform: ${transform}; transition: transform 0.1s ease-out;`"
                                    class="bg-white/80 backdrop-blur-sm rounded-xl shadow-sm border border-gray-200/60 p-5 h-full flex flex-col justify-between relative overflow-hidden z-10 hover:shadow-2xl hover:border-gray-300">

                                    {{-- Efek Kilau Putih (Glare) saat hover --}}
                                    <div
                                        class="absolute inset-0 bg-gradient-to-tr from-white/40 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500 pointer-events-none">
                                    </div>

                                    {{-- Bagian Atas: Icon & Title --}}
                                    <div class="flex flex-col items-start gap-4 relative z-20">
                                        {{-- Icon Wrapper --}}
                                        <div
                                            class="w-12 h-12 rounded-lg bg-gradient-to-br {{ $item['color'] }} {{ $item['shadow'] }} text-white flex items-center justify-center flex-shrink-0 shadow-md group-hover:scale-110 transition-transform duration-300">
                                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor">
                                                {!! $item['icon'] !!}
                                            </svg>
                                        </div>

                                        {{-- Text Wrapper --}}
                                        <div class="w-full">
                                            <h3
                                                class="font-bold text-gray-800 text-base leading-tight mb-2 {{ $item['bg_hover'] }} transition-colors min-h-[3rem] flex items-center">
                                                {{ $item['name'] }}
                                            </h3>
                                            <p class="text-xs text-gray-500 leading-relaxed">
                                                {{ $item['desc'] }}
                                            </p>
                                        </div>
                                    </div>

                                    {{-- Arrow icon --}}
                                    <div
                                        class="mt-4 flex justify-end opacity-0 group-hover:opacity-100 transition-all duration-300 transform translate-x-[-20px] group-hover:translate-x-0 relative z-20">
                                        <svg class="w-5 h-5 text-gray-400 group-hover:text-{{ explode('-', $item['color'])[1] }}-500"
                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                                        </svg>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>



        {{-- 6. Footer Note --}}
        <div class="text-center mt-20 text-slate-400 text-sm animate-fade-in-up opacity-0-start delay-300">
            <p>&copy; {{ date('Y') }} Fairuz Bachri. All rights reserved. </p>
        </div>

    </div>
    </div>
</body>

</html>
