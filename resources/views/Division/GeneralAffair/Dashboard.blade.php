@section('browser_title', 'GA Dashboard')
<x-app-layout>
    {{-- Header dengan Tema Caterpillar (Hitam/Kuning) --}}
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-extrabold text-xl text-slate-900 leading-tight uppercase flex items-center gap-3">
                <span class="w-3 h-8 bg-yellow-400 rounded-sm inline-block"></span>
                {{ __('Dashboard Statistik') }}
            </h2>
            <div class="flex gap-2">
                {{-- TOMBOL DOWNLOAD PDF BARU --}}
                <button onclick="exportToPDF()"
                    class="bg-red-600 text-white hover:bg-red-700 font-bold py-2 px-4 rounded text-sm uppercase tracking-wide transition flex items-center gap-2 shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                        </path>
                    </svg>
                    Export PDF
                </button>

                <a href="{{ route('ga.index') }}"
                    class="bg-slate-900 text-white hover:bg-slate-800 font-bold py-2 px-4 rounded text-sm uppercase tracking-wide transition">
                    &larr; Kembali ke Data
                </a>
            </div>
        </div>
    </x-slot>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-annotation@2.1.0"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns/dist/chartjs-adapter-date-fns.bundle.min.js">
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

    <div class="py-12 bg-slate-50">
        {{-- Tambahkan ID disini --}}
        <div id="dashboard-content" class="max-w-8xl mx-auto sm:px-6 lg:px-8 p-4 bg-slate-50">

            {{-- Counter Cards --}}
            {{-- B. STATISTIK CARDS (Interactive Industrial Style) --}}
            {{-- B. STATISTIK CARDS (High Interactivity) --}}
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8" x-show="show" x-transition>

                {{-- 1. Card Total --}}
                <div
                    class="bg-white rounded-sm shadow-md p-6 relative overflow-hidden group hover:-translate-y-2 transition-all duration-300 border-l-4 border-slate-900 hover:shadow-xl">
                    <div class="relative z-10">
                        <p
                            class="text-xs font-black text-slate-500 uppercase tracking-widest mb-1 group-hover:text-slate-800 transition-colors">
                            Total Tiket</p>
                        <p class="text-5xl font-black text-slate-900">{{ $countTotal }}</p>
                    </div>
                    {{-- Animated Icon --}}
                    <div
                        class="absolute -right-6 -bottom-6 text-slate-900 opacity-5 group-hover:opacity-10 group-hover:scale-110 group-hover:-rotate-12 transition-all duration-500 ease-out">
                        <svg class="w-40 h-40" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z" />
                        </svg>
                    </div>
                    {{-- Bottom Accent --}}
                    <div
                        class="absolute bottom-0 left-0 w-0 h-1 bg-slate-900 group-hover:w-full transition-all duration-500">
                    </div>
                </div>

                {{-- 2. Card Pending (Amber Glow) --}}
                <div
                    class="bg-white rounded-sm shadow-md p-6 relative overflow-hidden group hover:-translate-y-2 transition-all duration-300 border-l-4 border-amber-500 hover:shadow-amber-500/20 hover:shadow-xl">
                    <div class="relative z-10">
                        <p
                            class="text-xs font-black text-amber-600 uppercase tracking-widest mb-1 group-hover:text-amber-700 transition-colors">
                            Pending</p>
                        <p class="text-5xl font-black text-slate-900">{{ $countPending }}</p>
                    </div>
                    <div
                        class="absolute -right-6 -bottom-6 text-amber-500 opacity-10 group-hover:opacity-20 group-hover:scale-110 group-hover:rotate-12 transition-all duration-500 ease-out">
                        <svg class="w-40 h-40" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67z" />
                        </svg>
                    </div>
                    <div
                        class="absolute bottom-0 left-0 w-0 h-1 bg-amber-500 group-hover:w-full transition-all duration-500">
                    </div>
                </div>

                {{-- 3. Card In Progress (Blue Glow & Gear Spin) --}}
                <div
                    class="bg-white rounded-sm shadow-md p-6 relative overflow-hidden group hover:-translate-y-2 transition-all duration-300 border-l-4 border-blue-600 hover:shadow-blue-600/20 hover:shadow-xl">
                    <div class="relative z-10">
                        <p
                            class="text-xs font-black text-blue-600 uppercase tracking-widest mb-1 group-hover:text-blue-700 transition-colors">
                            In Progress</p>
                        <p class="text-5xl font-black text-slate-900">{{ $countInProgress }}</p>
                    </div>
                    <div
                        class="absolute -right-6 -bottom-6 text-blue-600 opacity-10 group-hover:opacity-20 group-hover:scale-110 group-hover:rotate-90 transition-all duration-700 ease-out">
                        <svg class="w-40 h-40" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M19.14 12.94c.04-.3.06-.61.06-.94 0-.32-.02-.64-.07-.94l2.03-1.58a.49.49 0 0 0 .12-.61l-1.92-3.32a.488.488 0 0 0-.59-.22l-2.39.96c-.5-.38-1.03-.7-1.62-.94l-.36-2.54a.484.484 0 0 0-.48-.41h-3.84c-.24 0-.43.17-.47.41l-.36 2.54c-.59.24-1.13.57-1.62.94l-2.39-.96c-.22-.08-.47 0-.59.22L2.74 8.87c-.12.21-.08.47.12.61l2.03 1.58c-.05.3-.09.63-.09.94s.02.64.07.94l-2.03 1.58a.49.49 0 0 0-.12.61l1.92 3.32c.12.22.37.29.59.22l2.39-.96c.5.38 1.03.7 1.62.94l.36 2.54c.05.24.24.41.48.41h3.84c.24 0 .44-.17.47-.41l.36-2.54c.59-.24 1.13-.56 1.62-.94l2.39.96c.22.08.47 0 .59-.22l1.92-3.32c.12-.22.07-.47-.12-.61l-2.01-1.58zM12 15.6c-1.98 0-3.6-1.62-3.6-3.6s1.62-3.6 3.6-3.6 3.6 1.62 3.6 3.6-1.62 3.6-3.6 3.6z" />
                        </svg>
                    </div>
                    <div
                        class="absolute bottom-0 left-0 w-0 h-1 bg-blue-600 group-hover:w-full transition-all duration-500">
                    </div>
                </div>

                {{-- 4. Card Selesai (Emerald Glow) --}}
                <div
                    class="bg-white rounded-sm shadow-md p-6 relative overflow-hidden group hover:-translate-y-2 transition-all duration-300 border-l-4 border-emerald-500 hover:shadow-emerald-500/20 hover:shadow-xl">
                    <div class="relative z-10">
                        <p
                            class="text-xs font-black text-emerald-600 uppercase tracking-widest mb-1 group-hover:text-emerald-700 transition-colors">
                            Selesai</p>
                        <p class="text-5xl font-black text-slate-900">{{ $countCompleted }}</p>
                    </div>
                    <div
                        class="absolute -right-6 -bottom-6 text-emerald-500 opacity-10 group-hover:opacity-20 group-hover:scale-110 group-hover:-rotate-12 transition-all duration-500 ease-out">
                        <svg class="w-40 h-40" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z" />
                        </svg>
                    </div>
                    <div
                        class="absolute bottom-0 left-0 w-0 h-1 bg-emerald-500 group-hover:w-full transition-all duration-500">
                    </div>
                </div>
            </div>

            {{-- Grid Grafik --}}
            {{-- GRID GRAFIK (INDUSTRIAL STYLE) --}}
            <div class="bg-white p-6 rounded-sm shadow-md border-t-4 border-yellow-400 mb-6">
                <div class="flex flex-col md:flex-row justify-between items-center mb-6">
                    <div>
                        <h3 class="text-lg font-black text-slate-900 uppercase tracking-wider">
                            Pencapaian Bulanan
                        </h3>
                        <p class="text-xs text-slate-500 font-bold mt-1">
                            Berdasarkan Target Penyelesaian Tiket
                        </p>
                    </div>

                    {{-- FILTER KHUSUS BULAN --}}
                    <form action="{{ route('ga.dashboard') }}" method="GET" class="flex items-center gap-2">
                        {{-- Keep other filters if exist (optional, agar tidak reset filter lain) --}}
                        @if (request('start_date'))
                            <input type="hidden" name="start_date" value="{{ request('start_date') }}">
                        @endif
                        @if (request('end_date'))
                            <input type="hidden" name="end_date" value="{{ request('end_date') }}">
                        @endif

                        <label for="filter_month" class="text-xs font-bold text-slate-600 uppercase">Pilih
                            Bulan:</label>
                        <input type="month" name="filter_month" id="filter_month" value="{{ $filterMonth }}"
                            onchange="this.form.submit()"
                            class="border-2 border-slate-200 rounded-sm text-sm font-bold text-slate-800 focus:border-yellow-400 focus:ring-0 py-1">
                    </form>
                </div>

                <div class="flex flex-col md:flex-row items-center gap-8">
                    {{-- KANVAS CHART DOUGHNUT --}}
                    <div class="relative w-48 h-48">
                        <canvas id="performanceChart"></canvas>
                        {{-- Teks Persentase di Tengah --}}
                        <div class="absolute inset-0 flex items-center justify-center flex-col pointer-events-none">
                            <span class="text-3xl font-black text-slate-900">{{ $perfPercentage }}%</span>
                            <span class="text-[10px] font-bold text-slate-400 uppercase">Selesai</span>
                        </div>
                    </div>

                    {{-- KETERANGAN TEKS --}}
                    <div class="flex-1 w-full">
                        <div class="grid grid-cols-2 gap-4">
                            <div class="bg-slate-50 p-4 rounded-sm border-l-4 border-slate-300">
                                <p class="text-xs font-bold text-slate-500 uppercase">Total Target</p>
                                <p class="text-2xl font-black text-slate-800">{{ $perfTotal }} <span
                                        class="text-sm font-normal text-slate-400">Tiket</span></p>
                            </div>
                            <div class="bg-green-50 p-4 rounded-sm border-l-4 border-green-500">
                                <p class="text-xs font-bold text-green-600 uppercase">Terealisasi</p>
                                <p class="text-2xl font-black text-green-700">{{ $perfCompleted }} <span
                                        class="text-sm font-normal text-green-500">Tiket</span></p>
                            </div>
                        </div>
                        <div class="mt-4">
                            {{-- Progress Bar Visual --}}
                            <div class="w-full bg-slate-200 rounded-full h-4 overflow-hidden">
                                <div class="bg-gradient-to-r from-yellow-400 to-yellow-600 h-4 rounded-full transition-all duration-1000 ease-out"
                                    style="width: {{ $perfPercentage }}%"></div>
                            </div>
                            <p class="text-xs text-slate-400 mt-2 font-medium italic text-right">
                                *Menghitung tiket dengan target selesai bulan
                                {{ Carbon\Carbon::parse($filterMonth)->translatedFormat('F Y') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">

                {{-- 1. Lokasi --}}
                <div class="bg-white p-5 rounded-sm shadow-md border-t-4 border-slate-900">
                    <h4
                        class="text-sm font-black text-slate-900 uppercase tracking-widest mb-4 border-b border-slate-200 pb-2 flex items-center gap-2">
                        <span class="text-yellow-400 text-lg leading-none">///</span> Statistik per Lokasi
                    </h4>
                    <div class="h-64"><canvas id="locChart"></canvas></div>
                </div>

                {{-- 2. Department --}}
                <div class="bg-white p-5 rounded-sm shadow-md border-t-4 border-slate-900">
                    <h4
                        class="text-sm font-black text-slate-900 uppercase tracking-widest mb-4 border-b border-slate-200 pb-2 flex items-center gap-2">
                        <span class="text-yellow-400 text-lg leading-none">///</span> Statistik per Department
                    </h4>
                    <div class="h-64"><canvas id="deptChart"></canvas></div>
                </div>

                {{-- 3. Parameter --}}
                <div class="bg-white p-5 rounded-sm shadow-md border-t-4 border-slate-900">
                    <h4
                        class="text-sm font-black text-slate-900 uppercase tracking-widest mb-4 border-b border-slate-200 pb-2 flex items-center gap-2">
                        <span class="text-yellow-400 text-lg leading-none">///</span> Parameter Permintaan
                    </h4>
                    <div class="h-64"><canvas id="paramChart"></canvas></div>
                </div>

                {{-- 4. Bobot --}}
                <div class="bg-white p-5 rounded-sm shadow-md border-t-4 border-slate-900">
                    <h4
                        class="text-sm font-black text-slate-900 uppercase tracking-widest mb-4 border-b border-slate-200 pb-2 flex items-center gap-2">
                        <span class="text-yellow-400 text-lg leading-none">///</span> Bobot Pekerjaan
                    </h4>
                    <div class="h-64"><canvas id="bobotChart"></canvas></div>
                </div>
            </div>
            <div class="bg-white p-4 rounded-sm shadow-md border-l-4 border-slate-900 mb-6">
                <form action="{{ route('ga.dashboard') }}" method="GET"
                    class="flex flex-col md:flex-row items-end gap-4">

                    {{-- Input Tanggal Mulai --}}
                    <div class="w-full md:w-auto">
                        <label for="start_date"
                            class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-1">
                            Tanggal Mulai
                        </label>
                        <input type="date" name="start_date" id="start_date" value="{{ request('start_date') }}"
                            class="w-full md:w-48 border-slate-300 rounded-sm shadow-sm focus:border-yellow-400 focus:ring focus:ring-yellow-200 focus:ring-opacity-50 text-sm">
                    </div>

                    {{-- Input Tanggal Akhir --}}
                    <div class="w-full md:w-auto">
                        <label for="end_date"
                            class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-1">
                            Tanggal Akhir
                        </label>
                        <input type="date" name="end_date" id="end_date" value="{{ request('end_date') }}"
                            class="w-full md:w-48 border-slate-300 rounded-sm shadow-sm focus:border-yellow-400 focus:ring focus:ring-yellow-200 focus:ring-opacity-50 text-sm">
                    </div>

                    {{-- Tombol Filter --}}
                    <div class="flex gap-2">
                        <button type="submit"
                            class="bg-slate-900 text-white hover:bg-slate-800 font-bold py-2 px-6 rounded-sm text-sm uppercase tracking-wide transition shadow-md">
                            Filter Data
                        </button>

                        {{-- Tombol Reset (Jika sedang ada filter) --}}
                        @if (request('start_date'))
                            <a href="{{ route('ga.dashboard') }}"
                                class="bg-red-50 text-red-600 hover:bg-red-100 border border-red-200 font-bold py-2 px-4 rounded-sm text-sm uppercase tracking-wide transition">
                                Reset
                            </a>
                        @endif
                    </div>
                </form>
            </div>
            {{-- Gantt Chart (Container Kuning) --}}
            <div class="bg-white p-6 rounded-sm shadow-md border-t-4 border-yellow-400 mb-8">
                <div
                    class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 border-b border-slate-200 pb-3 gap-2">
                    <h4 class="text-sm font-black text-slate-900 uppercase tracking-widest flex items-center gap-2">
                        <svg class="w-5 h-5 text-slate-900" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Timeline Pengerjaan
                    </h4>

                    {{-- Legend (Warna Tetap Standar Merah/Kuning/Hijau sesuai request) --}}
                    <div class="text-xs flex gap-3 font-bold uppercase text-slate-600">
                        <span class="flex items-center gap-1.5">
                            <span class="w-3 h-3 bg-red-500 rounded-sm shadow-sm"></span> Berat
                        </span>
                        <span class="flex items-center gap-1.5">
                            <span class="w-3 h-3 bg-yellow-500 rounded-sm shadow-sm"></span> Sedang
                        </span>
                        <span class="flex items-center gap-1.5">
                            <span class="w-3 h-3 bg-green-500 rounded-sm shadow-sm"></span> Ringan
                        </span>
                        <span class="flex items-center gap-1.5">
                            <span class="w-3 h-3 bg-gray-500 rounded-sm shadow-sm"></span> Selesai
                        </span>
                    </div>
                </div>
                @php
                    $chartHeight = count($ganttLabels) > 0 ? count($ganttLabels) * 40 + 120 : 300;
                @endphp
                <div style="height: {{ $chartHeight }}px;" class="relative w-full">
                    <canvas id="ganttChart"></canvas>
                </div>
            </div>
            @php
                // Ambil tanggal created_at paling lama dari koleksi data
                // Pastikan variabel $workOrders dikirim dari Controller!
                $minDate = $workOrders->min('created_at');

                $startDateFilename = $minDate ? $minDate->format('Y-m-d') : date('Y-m-d');
                $startDateHeader = $minDate ? $minDate->translatedFormat('d F Y') : date('d F Y');
            @endphp
            <script>
                // --- CHART BARU: PERFORMANCE CHART ---
                const ctxPerf = document.getElementById('performanceChart');
                if (ctxPerf) {
                    const perfPercentage = {{ $perfPercentage }};
                    const remaining = 100 - perfPercentage;

                    new Chart(ctxPerf, {
                        type: 'doughnut',
                        data: {
                            labels: ['Selesai', 'Belum Selesai'],
                            datasets: [{
                                data: [perfPercentage, remaining],
                                backgroundColor: [
                                    '#FACC15', // Kuning (Selesai)
                                    '#E2E8F0' // Abu-abu (Sisa)
                                ],
                                borderWidth: 0,
                                hoverOffset: 4
                            }]
                        },
                        options: {
                            responsive: true,
                            cutout: '75%', // Membuat lubang tengah lebih besar
                            plugins: {
                                legend: {
                                    display: false
                                },
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            return context.label + ': ' + context.raw + '%';
                                        }
                                    }
                                }
                            }
                        }
                    });
                }
            </script>
            <script>
                document.addEventListener('DOMContentLoaded', function() {

                    // 1. LOKASI (Bar Biru Standar)
                    const ctxLoc = document.getElementById('locChart');
                    if (ctxLoc) {
                        new Chart(ctxLoc.getContext('2d'), {
                            type: 'bar',
                            data: {
                                labels: @json($chartLocLabels),
                                datasets: [{
                                    label: 'Total',
                                    data: @json($chartLocValues),
                                    backgroundColor: '#3b82f6', // Biru Standar
                                    borderRadius: 4
                                }]
                            },
                            options: {
                                indexAxis: 'y',
                                responsive: true,
                                maintainAspectRatio: false
                            }
                        });
                    }

                    // 2. DEPARTMENT (Bar Ungu Standar)
                    const ctxDept = document.getElementById('deptChart');
                    if (ctxDept) {
                        new Chart(ctxDept.getContext('2d'), {
                            type: 'bar',
                            data: {
                                labels: @json($chartDeptLabels),
                                datasets: [{
                                    label: 'Total',
                                    data: @json($chartDeptValues),
                                    backgroundColor: '#8b5cf6', // Ungu Standar
                                    borderRadius: 4
                                }]
                            },
                            options: {
                                indexAxis: 'y',
                                responsive: true,
                                maintainAspectRatio: false
                            }
                        });
                    }

                    // 3. PARAMETER (Pie Warna-Warni)
                    const ctxParam = document.getElementById('paramChart');
                    if (ctxParam) {
                        new Chart(ctxParam.getContext('2d'), {
                            type: 'doughnut',
                            data: {
                                labels: @json($chartParamLabels),
                                datasets: [{
                                    data: @json($chartParamValues),
                                    backgroundColor: ['#36a2eb', '#ff6384', '#4bc0c0', '#ff9f40',
                                        '#9966ff'
                                    ],
                                    borderWidth: 1
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    // Konfigurasi Data Labels (Persentase)
                                    datalabels: {
                                        color: '#fff', // Warna Teks Putih
                                        font: {
                                            weight: 'bold',
                                            size: 12
                                        },
                                        formatter: (value, ctx) => {
                                            let sum = 0;
                                            let dataArr = ctx.chart.data.datasets[0].data;
                                            dataArr.map(data => {
                                                sum += data;
                                            });
                                            // Hitung Persen
                                            let percentage = (value * 100 / sum).toFixed(0) + "%";
                                            // Jangan tampilkan jika 0%
                                            return value > 0 ? percentage : '';
                                        }
                                    }
                                }
                            },
                            // Wajib mendaftarkan plugin di sini
                            plugins: [ChartDataLabels]
                        });
                    }

                    // 4. BOBOT (Pie Merah/Kuning/Hijau)
                    const ctxBobot = document.getElementById('bobotChart');
                    if (ctxBobot) {
                        new Chart(ctxBobot.getContext('2d'), {
                            type: 'pie',
                            data: {
                                labels: @json($chartBobotLabels),
                                datasets: [{
                                    data: @json($chartBobotValues),
                                    backgroundColor: ['#ef4444', '#f59e0b', '#22c55e'],
                                    borderWidth: 1
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    // Konfigurasi Data Labels
                                    datalabels: {
                                        color: '#fff',
                                        font: {
                                            weight: 'bold',
                                            size: 14
                                        },
                                        formatter: (value, ctx) => {
                                            let sum = 0;
                                            let dataArr = ctx.chart.data.datasets[0].data;
                                            dataArr.map(data => {
                                                sum += data;
                                            });
                                            let percentage = (value * 100 / sum).toFixed(0) + "%";
                                            return value > 0 ? percentage : '';
                                        }
                                    },
                                    legend: {
                                        position: 'bottom' // Pindahkan legenda ke bawah agar rapi
                                    }
                                }
                            },
                            // Wajib mendaftarkan plugin
                            plugins: [ChartDataLabels]
                        });
                    }

                    // 5. GANTT CHART
                    const ctxGantt = document.getElementById('ganttChart');
                    if (ctxGantt) {
                        const rawData = @json($ganttRawData ?? []);

                        new Chart(ctxGantt.getContext('2d'), {
                            type: 'bar',
                            data: {
                                labels: @json($ganttLabels ?? []),
                                datasets: [{
                                    label: 'Durasi Pengerjaan',
                                    data: @json($ganttData ?? []),
                                    backgroundColor: @json($ganttColors ?? []),
                                    borderColor: @json($ganttColors ?? []),
                                    borderWidth: 1,
                                    barPercentage: 0.6,

                                    // --- DATA CUSTOM (MAPPING DARI PHP) ---
                                    departments: rawData.map(item => item.dept),
                                    locations: rawData.map(item => item.loc),
                                    statuses: rawData.map(item => item.status) // <--- TAMBAHAN PENTING
                                }]
                            },
                            options: {
                                indexAxis: 'y',
                                responsive: true,
                                maintainAspectRatio: false,
                                scales: {
                                    x: {
                                        type: 'time',
                                        time: {
                                            unit: 'day',
                                            displayFormats: {
                                                day: 'd MMM'
                                            },
                                            tooltipFormat: 'd MMM yyyy'
                                        },
                                        min: new Date(new Date().setDate(new Date().getDate() - 7)),
                                        grid: {
                                            color: '#f1f5f9'
                                        }
                                    },
                                    y: {
                                        grid: {
                                            display: false
                                        },
                                        ticks: {
                                            font: {
                                                weight: 'bold',
                                                size: 11
                                            }
                                        }
                                    }
                                },
                                plugins: {
                                    legend: {
                                        display: false
                                    },
                                    tooltip: {
                                        backgroundColor: 'rgba(15, 23, 42, 0.9)',
                                        padding: 12,
                                        titleFont: {
                                            size: 13
                                        },
                                        bodyFont: {
                                            size: 12
                                        },
                                        displayColors: true,
                                        callbacks: {
                                            title: function(context) {
                                                return context[0].label;
                                            },
                                            label: function(context) {
                                                const raw = context.raw;

                                                // Ambil data custom
                                                const deptName = context.dataset.departments[context
                                                    .dataIndex] || '-';
                                                const locName = context.dataset.locations[context.dataIndex] ||
                                                    '-';
                                                const status = context.dataset.statuses[context
                                                    .dataIndex]; // <--- AMBIL STATUS ASLI

                                                // LOGIKA CEKLIS (SEKARANG LEBIH AKURAT)
                                                let statusText = '';
                                                if (status === 'completed') {
                                                    statusText = ' (✅ SELESAI)';
                                                }

                                                const start = new Date(raw[0]).toLocaleDateString('id-ID', {
                                                    day: 'numeric',
                                                    month: 'short'
                                                });
                                                const end = new Date(raw[1]).toLocaleDateString('id-ID', {
                                                    day: 'numeric',
                                                    month: 'short'
                                                });

                                                return [
                                                    `📍 Lokasi: ${locName}`,
                                                    `🏢 Dept: ${deptName}${statusText}`, // Tambahkan ceklis disini
                                                    `📅 Jadwal: ${start} - ${end}`
                                                ];
                                            }
                                        }
                                    },
                                    annotation: {
                                        annotations: {
                                            todayLine: {
                                                type: 'line',
                                                xMin: new Date(), // Posisi garis di tanggal "Sekarang"
                                                xMax: new Date(),
                                                borderColor: 'rgba(255, 99, 132, 0.8)', // Warna Merah Transparan
                                                borderWidth: 2,
                                                borderDash: [5, 5], // Garis putus-putus
                                                label: {
                                                    display: true,
                                                    content: 'Hari Ini', // Teks Label
                                                    position: 'start',
                                                    backgroundColor: 'rgba(255, 99, 132, 0.8)',
                                                    color: 'white',
                                                    font: {
                                                        size: 10,
                                                        weight: 'bold'
                                                    },
                                                    yAdjust: -10 // Geser label sedikit ke atas agar tidak menutupi grafik
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        });
                    }
                });
            </script>
            <script>
                window.exportToPDF = function() {
                    const {
                        jsPDF
                    } = window.jspdf;
                    const element = document.getElementById('dashboard-content');

                    // --- 1. AMBIL NILAI DARI INPUT FILTER ---
                    const filterStart = document.getElementById('start_date').value;
                    const filterEnd = document.getElementById('end_date').value;

                    // --- 2. TENTUKAN START DATE ---
                    // Jika user memfilter, pakai input user. Jika tidak, pakai default dari DB.
                    let startDateVal = "{{ $startDateFilename }}"; // Default
                    if (filterStart) {
                        startDateVal = filterStart; // Format input: YYYY-MM-DD
                    }

                    // --- 3. TENTUKAN END DATE ---
                    let endDateFilename, endDateHeader;

                    let dateObj;

                    if (filterEnd) {
                        // A. JIKA ADA FILTER: Pakai tanggal dari input
                        dateObj = new Date(filterEnd);
                    } else {
                        // B. JIKA TIDAK ADA FILTER: Pakai Hari Ini
                        dateObj = new Date();
                    }

                    // --- FORMAT TANGGAL UNTUK FILE & HEADER ---
                    const year = dateObj.getFullYear();
                    const day = String(dateObj.getDate()).padStart(2, '0');
                    const monthName = dateObj.toLocaleString('id-ID', {
                        month: 'long'
                    }); // "Desember"

                    // Hasil: 15-Desember-2023
                    endDateFilename = `${day}-${monthName}-${year}`;

                    // Hasil: 15 Desember 2023
                    endDateHeader = `${day} ${monthName} ${year}`;

                    // --- 4. FORMAT ULANG START DATE UNTUK HEADER (OPSIONAL) ---
                    // Agar header terlihat rapi (misal: 01 Desember 2023 s/d ...), kita format ulang startDateVal
                    let startDateHeader = "{{ $startDateHeader }}"; // Default PHP
                    if (filterStart) {
                        const sDate = new Date(filterStart);
                        const sDay = String(sDate.getDate()).padStart(2, '0');
                        const sMonth = sDate.toLocaleString('id-ID', {
                            month: 'long'
                        });
                        const sYear = sDate.getFullYear();
                        startDateHeader = `${sDay} ${sMonth} ${sYear}`;
                    }

                    // --- 5. SETUP FINAL ---
                    const fileName = `Laporan-GA-${startDateVal}_sd_${endDateFilename}.pdf`;
                    const headerText = `Periode Data: ${startDateHeader} s/d ${endDateHeader}`;

                    // --- VALIDASI SWEETALERT ---
                    if (typeof Swal === 'undefined') {
                        alert('Library SweetAlert belum dimuat.');
                        return;
                    }

                    Swal.fire({
                        title: 'Memproses PDF...',
                        // Tampilkan tanggal yang BENAR di loading screen
                        text: `Menyiapkan rentang: ${startDateVal} s/d ${endDateFilename}`,
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading()
                        }
                    });

                    html2canvas(element, {
                        scale: 2,
                        useCORS: true,
                        logging: false,
                        backgroundColor: '#f8fafc',
                        ignoreElements: (el) => el.tagName === 'BUTTON'
                    }).then(canvas => {
                        const imgData = canvas.toDataURL('image/png');
                        const pdf = new jsPDF('p', 'mm', 'a4');
                        const imgWidth = 210;
                        const pageHeight = 297;
                        const imgHeight = canvas.height * imgWidth / canvas.width;
                        let heightLeft = imgHeight;
                        let position = 15;

                        // --- HEADER PDF ---
                        pdf.setFontSize(10);
                        pdf.text("Laporan Dashboard General Affair", 10, 8);
                        pdf.setFontSize(9);
                        pdf.setTextColor(100);
                        // Gunakan headerText yang sudah dinamis
                        pdf.text(headerText, 10, 13);
                        // ------------------

                        pdf.addImage(imgData, 'PNG', 0, position, imgWidth, imgHeight);
                        heightLeft -= pageHeight;

                        while (heightLeft >= 0) {
                            position = heightLeft - imgHeight;
                            pdf.addPage();
                            pdf.addImage(imgData, 'PNG', 0, position, imgWidth, imgHeight);
                            heightLeft -= pageHeight;
                        }

                        pdf.save(fileName);

                        Swal.fire({
                            icon: 'success',
                            title: 'Selesai!',
                            text: 'File: ' + fileName,
                            timer: 3000,
                            showConfirmButton: false
                        });

                    }).catch(err => {
                        console.error(err);
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal Export'
                        });
                    });
                }
            </script>
</x-app-layout>
