@section('browser_title', 'Facilities Dashboard')

<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center -my-2">
            <h2 class="font-bold text-2xl text-[#1E3A5F] leading-tight uppercase tracking-wider flex items-center gap-4">
                <span class="w-4 h-8 bg-[#22C55E] inline-block shadow-sm"></span>
                {{ __('Facilities Analytics') }}
            </h2>

            <button onclick="exportToPDF()"
                class="bg-[#1E3A5F] text-white px-4 py-2 rounded-sm font-bold text-sm uppercase shadow-sm hover:bg-[#162c46]">
                Export Report
            </button>
        </div>
    </x-slot>

    {{-- SCRIPTS --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns/dist/chartjs-adapter-date-fns.bundle.min.js">
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <div class="py-12 bg-[#F8FAFC]">
        <div id="dashboard-content" class="max-w-8xl mx-auto sm:px-6 lg:px-8 p-4 bg-[#F8FAFC]">

            {{-- 1. COUNTERS --}}
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white p-6 rounded-sm shadow-sm border-l-4 border-[#1E3A5F]">
                    <p class="text-xs font-bold text-slate-500 uppercase">Total</p>
                    <p class="text-4xl font-bold text-[#1E3A5F]">{{ $countTotal }}</p>
                </div>
                <div class="bg-white p-6 rounded-sm shadow-sm border-l-4 border-gray-400">
                    <p class="text-xs font-bold text-slate-500 uppercase">Pending</p>
                    <p class="text-4xl font-bold text-gray-600">{{ $countPending }}</p>
                </div>
                <div class="bg-white p-6 rounded-sm shadow-sm border-l-4 border-blue-600">
                    <p class="text-xs font-bold text-slate-500 uppercase">In Progress</p>
                    <p class="text-4xl font-bold text-blue-700">{{ $countProgress }}</p>
                </div>
                <div class="bg-white p-6 rounded-sm shadow-sm border-l-4 border-[#22C55E]">
                    <p class="text-xs font-bold text-slate-500 uppercase">Completed</p>
                    <p class="text-4xl font-bold text-[#22C55E]">{{ $countDone }}</p>
                </div>
            </div>

            {{-- 2. CHARTS GRID --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">

                {{-- Chart Category --}}
                <div class="bg-white p-6 rounded-sm shadow-sm border-t-4 border-[#1E3A5F]">
                    <h4 class="text-sm font-bold text-[#1E3A5F] uppercase mb-4">Request by Category</h4>
                    <div class="h-64"><canvas id="catChart"></canvas></div>
                </div>

                {{-- Chart Status --}}
                <div class="bg-white p-6 rounded-sm shadow-sm border-t-4 border-[#1E3A5F]">
                    <h4 class="text-sm font-bold text-[#1E3A5F] uppercase mb-4">Workload Status</h4>
                    <div class="h-64"><canvas id="statusChart"></canvas></div>
                </div>
            </div>

            {{-- 3. GANTT CHART --}}
            <div class="bg-white p-6 rounded-sm shadow-sm border-t-4 border-[#22C55E] mb-8">
                <h4 class="text-sm font-bold text-[#1E3A5F] uppercase mb-4">Work Timeline</h4>
                <div class="h-[500px] w-full relative">
                    <canvas id="ganttChart"></canvas>
                </div>
            </div>

        </div>
    </div>

    {{-- CHART SCRIPTS --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // 1. Category Chart (Bar - Navy)
            new Chart(document.getElementById('catChart'), {
                type: 'bar',
                data: {
                    labels: @json($chartCatLabels),
                    datasets: [{
                        label: 'Total',
                        data: @json($chartCatValues),
                        backgroundColor: '#1E3A5F',
                        borderRadius: 2
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false
                }
            });

            // 2. Status Chart (Doughnut - Corporate Colors)
            new Chart(document.getElementById('statusChart'), {
                type: 'doughnut',
                data: {
                    labels: @json($chartStatusLabels),
                    datasets: [{
                        data: @json($chartStatusValues),
                        backgroundColor: ['#1E3A5F', '#22C55E', '#64748B',
                        '#ef4444'], // Navy, Green, Slate, Red
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false
                }
            });

            // 3. Gantt Chart
            new Chart(document.getElementById('ganttChart'), {
                type: 'bar',
                data: {
                    labels: @json($ganttLabels),
                    datasets: [{
                        label: 'Duration',
                        data: @json($ganttData),
                        backgroundColor: @json($ganttColors),
                        barPercentage: 0.5
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
                                unit: 'day'
                            },
                            grid: {
                                color: '#f1f5f9'
                            }
                        },
                        y: {
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });
        });

        // Copy your exportToPDF script here (logic same as GA)
    </script>
</x-app-layout>
