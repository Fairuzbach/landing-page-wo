@section('browser_title', 'GA Dashboard')
<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-slate-800 leading-tight">
                {{ __('Dashboard General Affair') }}
            </h2>
            {{-- Tombol Kembali ke Tabel --}}
            <a href="{{ route('ga.index') }}"
                class="bg-white border border-slate-300 text-slate-700 hover:bg-slate-50 font-bold py-2 px-4 rounded shadow-sm text-sm">
                &larr; Kembali ke Data Tiket
            </a>
        </div>
    </x-slot>

    {{-- 1. LOAD LIBRARY --}}
    {{-- Chart.js Utama --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    {{-- PENTING: Adapter Tanggal untuk Gantt Chart (Wajib ada!) --}}
    <script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns/dist/chartjs-adapter-date-fns.bundle.min.js">
    </script>

    <div class="py-12">
        <div class="max-w-8xl mx-auto sm:px-6 lg:px-8">

            {{-- 2. COUNTER CARDS --}}
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white p-6 rounded-xl shadow-sm border border-indigo-100">
                    <div class="text-sm font-bold text-indigo-500 uppercase">Total Tiket</div>
                    <div class="text-3xl font-extrabold text-slate-800 mt-2">{{ $countTotal }}</div>
                </div>
                <div class="bg-white p-6 rounded-xl shadow-sm border border-amber-100">
                    <div class="text-sm font-bold text-amber-500 uppercase">Pending</div>
                    <div class="text-3xl font-extrabold text-slate-800 mt-2">{{ $countPending }}</div>
                </div>
                <div class="bg-white p-6 rounded-xl shadow-sm border border-blue-100">
                    <div class="text-sm font-bold text-blue-500 uppercase">In Progress</div>
                    <div class="text-3xl font-extrabold text-slate-800 mt-2">{{ $countInProgress }}</div>
                </div>
                <div class="bg-white p-6 rounded-xl shadow-sm border border-emerald-100">
                    <div class="text-sm font-bold text-emerald-500 uppercase">Selesai</div>
                    <div class="text-3xl font-extrabold text-slate-800 mt-2">{{ $countCompleted }}</div>
                </div>
            </div>

            {{-- 3. GRID GRAFIK (Tren, Kategori, Lokasi) --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                {{-- Grafik 1: Lokasi --}}
                <div class="bg-white p-5 rounded-xl shadow-sm border border-slate-100">
                    <h4 class="text-sm font-bold text-slate-700 mb-4">Statistik per Lokasi</h4>
                    <div class="h-64"><canvas id="locChart"></canvas></div>
                </div>

                {{-- Grafik 2: Department --}}
                <div class="bg-white p-5 rounded-xl shadow-sm border border-slate-100">
                    <h4 class="text-sm font-bold text-slate-700 mb-4">Statistik per Department</h4>
                    <div class="h-64"><canvas id="deptChart"></canvas></div>
                </div>

                {{-- Grafik 3: Parameter --}}
                <div class="bg-white p-5 rounded-xl shadow-sm border border-slate-100">
                    <h4 class="text-sm font-bold text-slate-700 mb-4">Parameter Permintaan</h4>
                    <div class="h-64 relative"><canvas id="paramChart"></canvas></div>
                </div>
                {{-- Grafik 4: Bobot --}}
                <div class="bg-white p-5 rounded-xl shadow-sm border border-slate-100">
                    <h4 class="text-sm font-bold text-slate-700 mb-4">Bobot Pekerjaan</h4>
                    <div class="h-64 relative"><canvas id="bobotChart"></canvas></div>
                </div>
            </div>

            {{-- 4. GANTT CHART SECTION (Timeline) --}}
            <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-100 mb-8">
                <div class="flex justify-between items-center mb-4">
                    <h4 class="text-sm font-bold text-slate-700">Timeline Pengerjaan (10 Tiket Terakhir)</h4>
                    <div class="text-xs flex gap-2">
                        <span class="flex items-center gap-1"><span class="w-3 h-3 bg-red-400 rounded-sm"></span>
                            High</span>
                        <span class="flex items-center gap-1"><span class="w-3 h-3 bg-amber-400 rounded-sm"></span>
                            Medium</span>
                        <span class="flex items-center gap-1"><span class="w-3 h-3 bg-emerald-400 rounded-sm"></span>
                            Low</span>
                    </div>
                </div>
                <div class="h-80">
                    <canvas id="ganttChart"></canvas>
                </div>
            </div>

        </div>
    </div>

    {{-- 5. SCRIPT CONFIGURATION --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // 1. TREND CHART// 1. LOKASI CHART (Bar)
            new Chart(document.getElementById('locChart').getContext('2d'), {
                type: 'bar',
                data: {
                    labels: @json($chartLocLabels),
                    datasets: [{
                        label: 'Total',
                        data: @json($chartLocValues),
                        backgroundColor: '#3b82f6',
                        borderRadius: 4
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false
                }
            });
            // 2. DEPARTMENT CHART (Bar)
            new Chart(document.getElementById('deptChart').getContext('2d'), {
                type: 'bar',
                data: {
                    labels: @json($chartDeptLabels),
                    datasets: [{
                        label: 'Total',
                        data: @json($chartDeptValues),
                        backgroundColor: '#8b5cf6',
                        borderRadius: 4
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false
                }
            });
            // 3. PARAMETER CHART (Pie)
            new Chart(document.getElementById('paramChart').getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: @json($chartParamLabels),
                    datasets: [{
                        data: @json($chartParamValues),
                        backgroundColor: ['#10b981', '#f59e0b', '#ef4444', '#3b82f6'],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false
                }
            });
            // 4. BOBOT PEKERJAAN (Pie - Dulu Kategori)
            new Chart(document.getElementById('bobotChart').getContext('2d'), {
                type: 'pie',
                data: {
                    labels: @json($chartBobotLabels),
                    datasets: [{
                        data: @json($chartBobotValues),
                        backgroundColor: ['#ef4444', '#f59e0b',
                            '#22c55e'
                        ], // Merah (Berat), Kuning, Hijau
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false
                }
            });
            // 4. GANTT CHART (TIMELINE)
            const ctxGantt = document.getElementById('ganttChart');
            if (ctxGantt) {
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
                            barPercentage: 0.5
                        }]
                    },
                    options: {
                        indexAxis: 'y', // Wajib Horizontal
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            x: {
                                type: 'time', // Butuh Date Adapter
                                time: {
                                    unit: 'day',
                                    displayFormats: {
                                        day: 'd MMM'
                                    }
                                },
                                min: new Date().setDate(new Date().getDate() - 7),
                                grid: {
                                    color: '#f1f5f9'
                                }
                            },
                            y: {
                                grid: {
                                    display: false
                                }
                            }
                        },
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        const start = new Date(context.raw[0]).toLocaleDateString(
                                            'id-ID');
                                        const end = new Date(context.raw[1]).toLocaleDateString(
                                            'id-ID');
                                        return `Target: ${start} s/d ${end}`;
                                    }
                                }
                            }
                        }
                    }
                });
            }
        });
    </script>
</x-app-layout>
