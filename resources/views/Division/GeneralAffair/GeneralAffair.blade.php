@section('browser_title', 'General Affair Work Order')

<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center -my-2">
            <h2
                class="font-black text-3xl text-slate-900 leading-tight uppercase tracking-wider flex items-center gap-4">
                {{-- Industrial Accent: Striped Bar --}}
                <div
                    class="w-2 h-10 bg-[url('data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSI4IiBoZWlnaHQ9IjgiPgo8cmVjdCB3aWR0aD0iOCIgaGVpZ2h0PSI4IiBmaWxsPSIjZmFjYzE1Ii8+CjxwYXRoIGQ9Ik0wIDBMOCA4Wk04IDBMMCA4WiIgc3Ryb2tlPSIjMTExIiBzdHJva2Utd2lkdGg9IjEiLz4KPC9zdmc+')] shadow-sm border border-slate-900">
                </div>
                {{ __('General Affair Request Order') }}
            </h2>
        </div>
    </x-slot>

    {{-- LOAD LIBRARY --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    {{-- CUSTOM PATTERN BACKGROUND --}}
    <div class="py-12 min-h-screen font-sans bg-slate-100 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAiIGhlaWdodD0iNDAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGNpcmNsZSBjeD0iMSIgY3k9IjEiIHI9IjEiIGZpbGw9IiNjYmQ1ZTEiIGZpbGwtb3BhY2l0eT0iMC4zIi8+PC9zdmc+')] bg-fixed"
        x-data="{
            // --- 1. MODAL STATES ---
            showDetailModal: false,
            showCreateModal: false,
            showConfirmModal: false,
            showEditModal: false,
            show: false,
        
            selected: JSON.parse(localStorage.getItem('ga_selected_ids') || '[]').map(String),
            pageIds: {{ Js::from($pageIds ?? []) }}.map(String),
        
            // --- MAPPING LOKASI KE DEPARTMENT ---
            locationMap: {
                'Plant A': 'Low Voltage',
                'Plant B': 'Medium Voltage',
                'Plant C': 'Low Voltage',
                'Plant D': 'Medium Voltage',
                'Autowire': 'Low Voltage',
                'MC Cable': 'Low Voltage',
                'QC Lab': 'QR',
                'QC LV': 'QR',
                'QC MV': 'QR',
                'QC FO': 'QR',
                'RM 1': 'SC',
                'RM 2': 'SC',
                'RM 3': 'SC',
                'RM 5': 'SC',
                'RM Office': 'SC',
                'Workshop Electric': 'MT',
                'Konstruksi': 'FH',
                'Plant E': 'FO',
                'Plant Tools': 'PE',
                'Gudang Jadi': 'SS',
                'GA': 'GA',
                'FA': 'FA',
                'IT': 'IT',
                'HC': 'HC',
                'Sales': 'Sales',
                'Marketing': 'Marketing',
            },
        
            form: { plant: '', plant_name: '', department: '', category: 'RINGAN', description: '', file_name: '', parameter_permintaan: '', status_permintaan: '' },
            editForm: { id: '', ticket_num: '', status: '', photo_path: '', target_date: '', actual_date: '' },
        
            get selectedTickets() { return this.selected; },
        
            toggleSelectAll() {
                const allSelected = this.pageIds.every(id => this.selected.includes(id));
                if (allSelected) { this.selected = this.selected.filter(id => !this.pageIds.includes(id)); } else { this.pageIds.forEach(id => { if (!this.selected.includes(id)) this.selected.push(id); }); }
            },
            clearSelection() {
                this.selected = [];
                localStorage.removeItem('ga_selected_ids');
            },
        
            updateDepartment() {
                let select = document.getElementById('plantSelect');
                let selectedOption = select.options[select.selectedIndex];
                let selectedText = selectedOption.text;
        
                this.form.plant_name = selectedText;
                if (this.locationMap[selectedText]) { this.form.department = this.locationMap[selectedText]; }
            },
        
            // --- DATA HOLDER & TIME ---
            ticket: null,
            currentDate: '',
            currentTime: '',
        
            updateTime() {
                const now = new Date();
                this.currentDate = now.toISOString().split('T')[0];
                this.currentTime = now.toTimeString().split(' ')[0].substring(0, 5);
            },
            handleFile(e) { this.form.file_name = e.target.files[0] ? e.target.files[0].name : ''; },
            submitForm() { this.$refs.createForm.reportValidity() ? this.$refs.createForm.submit() : this.showConfirmModal = false; },
        
            openEditModal(data) {
                this.ticket = data;
                this.editForm.id = data.id;
                this.editForm.ticket_num = data.ticket_num;
                this.editForm.status = data.status;
                this.editForm.category = data.category;
                this.editForm.target_date = data.target_completion_date || '';
                this.editForm.photo_path = data.photo_path;
                this.showEditModal = true;
            },
        
            init() {
                this.updateTime();
                setInterval(() => this.updateTime(), 60000);
                setTimeout(() => this.show = true, 100);
                this.$watch('showCreateModal', (v) => {
                    if (!v) {
                        this.form.plant = '';
                        this.form.plant_name = '';
                        this.form.department = '';
                        this.form.category = 'RINGAN';
                        this.form.description = '';
                        this.form.file_name = '';
                    }
                });
            }
        }" x-init="init()">

        @if ($errors->any())
            <div x-init="setTimeout(() => showCreateModal = true, 500)"></div>
        @endif

        <div class="max-w-[95%] mx-auto px-4 sm:px-6 lg:px-8">

            {{-- B. STATISTIK CARDS (Industrial & Clean) --}}
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-10" x-show="show"
                x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4"
                x-transition:enter-end="opacity-100 translate-y-0">

                @php
                    $cards = [
                        [
                            'title' => 'Total Tiket',
                            'value' => $countTotal,
                            'color' => 'slate',
                            'icon_path' =>
                                'M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2',
                        ],
                        [
                            'title' => 'Pending',
                            'value' => $countPending,
                            'color' => 'amber',
                            'icon_path' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
                        ],
                        [
                            'title' => 'On Progress',
                            'value' => $countInProgress,
                            'color' => 'blue',
                            'icon_path' =>
                                'M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z',
                        ],
                        [
                            'title' => 'Selesai',
                            'value' => $countCompleted,
                            'color' => 'emerald',
                            'icon_path' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
                        ],
                    ];
                @endphp

                @foreach ($cards as $card)
                    <div
                        class="relative bg-white border-b-4 border-{{ $card['color'] }}-500 shadow-lg rounded-sm overflow-hidden group hover:-translate-y-1 transition-all duration-300">
                        {{-- Background Pattern --}}
                        <div
                            class="absolute top-0 right-0 p-4 opacity-5 group-hover:opacity-10 group-hover:scale-110 transition-transform duration-500">
                            <svg class="w-24 h-24 text-{{ $card['color'] }}-900" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="{{ $card['icon_path'] }}" />
                            </svg>
                        </div>
                        <div class="p-6 relative z-10">
                            <div class="flex items-center justify-between mb-4">
                                <h3
                                    class="text-xs font-black text-{{ $card['color'] }}-600 uppercase tracking-widest border-l-2 border-{{ $card['color'] }}-500 pl-2">
                                    {{ $card['title'] }}
                                </h3>
                            </div>
                            <div class="flex items-baseline">
                                <span
                                    class="text-4xl font-black text-slate-800 tracking-tight">{{ $card['value'] }}</span>
                                <span class="ml-2 text-xs font-bold text-slate-400">Tiket</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- C. CONTROL PANEL (Search & Filter) --}}
            <div class="bg-white shadow-xl rounded-sm border border-slate-200 mb-8" x-data="{ showFilters: {{ request()->anyFilled(['category', 'status', 'parameter', 'start_date']) ? 'true' : 'false' }} }">
                {{-- Header Bar (Yellow Accent) --}}
                <div class="h-1.5 bg-yellow-400 w-full"></div>

                <form action="{{ route('ga.index') }}" method="GET" class="divide-y divide-slate-100">
                    <div class="p-5 flex flex-col lg:flex-row gap-4 items-center justify-between">

                        {{-- Search Input Group --}}
                        <div class="w-full lg:w-1/2 flex relative group">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="w-5 h-5 text-slate-400 group-focus-within:text-yellow-500 transition-colors"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                            <input type="text" name="search" value="{{ request('search') }}"
                                class="block w-full pl-10 pr-3 py-2.5 border-2 border-slate-200 rounded-l-sm text-sm font-bold text-slate-900 placeholder-slate-400 focus:outline-none focus:border-yellow-400 focus:ring-0 transition-colors bg-slate-50 focus:bg-white"
                                placeholder="Cari No. Tiket, Nama, atau Lokasi...">
                            <button type="submit"
                                class="bg-slate-900 text-white px-6 py-2.5 rounded-r-sm text-sm font-black uppercase tracking-wider hover:bg-slate-800 transition-colors border-2 border-slate-900">
                                Cari
                            </button>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="w-full lg:w-auto flex gap-3 justify-end items-center">
                            {{-- Filter Toggle --}}
                            <button type="button" @click="showFilters = !showFilters"
                                class="flex items-center gap-2 px-4 py-2.5 border-2 border-slate-200 bg-white text-slate-600 rounded-sm hover:border-yellow-400 hover:text-slate-900 font-bold text-xs uppercase transition-all"
                                :class="showFilters ? 'bg-yellow-50 border-yellow-400 text-slate-900' : ''">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z">
                                    </path>
                                </svg>
                                Filter
                                @if (request()->anyFilled(['category', 'status', 'parameter', 'start_date']))
                                    <span class="w-2 h-2 bg-red-500 rounded-full animate-pulse"></span>
                                @endif
                            </button>

                            {{-- Export Button --}}
                            <a href="{{ route('ga.export', request()->query()) }}"
                                class="flex items-center justify-center w-10 h-10 border-2 border-slate-200 text-slate-500 hover:text-green-600 hover:border-green-500 bg-white rounded-sm transition-all"
                                title="Export Excel">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                    </path>
                                </svg>
                            </a>

                            {{-- Admin Stats --}}
                            @if (auth()->user()->role === 'ga.admin')
                                <a href="{{ route('ga.dashboard') }}"
                                    class="flex items-center justify-center w-10 h-10 border-2 border-slate-900 text-slate-900 hover:bg-slate-900 hover:text-yellow-400 bg-white rounded-sm transition-all"
                                    title="Dashboard">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                                        </path>
                                    </svg>
                                </a>
                            @endif

                            {{-- Create Button --}}
                            <button @click="showCreateModal = true" type="button"
                                class="flex items-center gap-2 bg-yellow-400 text-slate-900 px-5 py-2.5 rounded-sm font-black uppercase tracking-wider shadow-md hover:bg-yellow-300 hover:shadow-lg transition-all transform active:scale-95">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                        d="M12 4v16m8-8H4"></path>
                                </svg>
                                <span class="hidden sm:inline">Buat Tiket</span>
                                <span class="sm:hidden">Baru</span>
                            </button>
                        </div>
                    </div>

                    {{-- Collapsible Filter Panel --}}
                    <div x-show="showFilters" x-collapse class="bg-slate-50 px-5 pb-5 pt-2 border-t border-slate-200">
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-4">
                            {{-- Filter Item Wrapper --}}
                            @foreach (['status' => ['pending', 'in_progress', 'completed', 'cancelled'], 'category' => ['BERAT', 'SEDANG', 'RINGAN'], 'parameter' => ['KEBERSIHAN', 'PEMELIHARAAN', 'PERBAIKAN', 'PEMBUATAN BARU', 'PERIZINAN', 'RESERVASI']] as $key => $opts)
                                <div>
                                    <label
                                        class="text-[10px] font-black text-slate-500 uppercase block mb-1 tracking-wider">{{ ucfirst($key) }}</label>
                                    <select name="{{ $key }}"
                                        class="w-full text-xs font-bold border-slate-300 focus:border-yellow-400 focus:ring-0 rounded-sm bg-white h-10 uppercase">
                                        <option value="">SEMUA {{ strtoupper($key) }}</option>
                                        @foreach ($opts as $opt)
                                            <option value="{{ $opt }}"
                                                {{ request($key) == $opt ? 'selected' : '' }}>
                                                {{ str_replace('_', ' ', strtoupper($opt)) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            @endforeach

                            {{-- Date Picker --}}
                            <div class="md:col-span-2">
                                <label
                                    class="text-[10px] font-black text-slate-500 uppercase block mb-1 tracking-wider">RENTANG
                                    TANGGAL</label>
                                <div class="relative">
                                    <input type="text" id="date_range_picker"
                                        class="w-full text-xs font-bold border-slate-300 focus:border-yellow-400 focus:ring-0 rounded-sm bg-white h-10 pl-9"
                                        placeholder="Pilih Tanggal...">
                                    <div
                                        class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                            </path>
                                        </svg></div>
                                    <input type="hidden" name="start_date" id="start_date"
                                        value="{{ request('start_date') }}">
                                    <input type="hidden" name="end_date" id="end_date"
                                        value="{{ request('end_date') }}">
                                </div>
                            </div>
                        </div>
                        <div class="mt-4 flex justify-end border-t border-slate-200 pt-3">
                            <a href="{{ route('ga.index') }}"
                                class="text-xs font-bold text-red-500 hover:text-red-700 flex items-center gap-1 uppercase tracking-wide transition-colors">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"></path>
                                </svg> Reset Filter
                            </a>
                        </div>
                    </div>
                </form>

                {{-- Bulk Action Bar --}}
                <div x-show="selected.length > 0" x-transition
                    class="bg-yellow-50 px-5 py-3 border-t border-yellow-200 flex justify-between items-center">
                    <div class="flex items-center gap-2 text-xs font-bold text-slate-800 uppercase tracking-wider">
                        <span class="flex h-3 w-3 relative"><span
                                class="animate-ping absolute inline-flex h-full w-full rounded-full bg-yellow-400 opacity-75"></span><span
                                class="relative inline-flex rounded-full h-3 w-3 bg-yellow-500"></span></span>
                        <span x-text="selected.length"></span> ITEM TERPILIH
                    </div>
                    <div class="flex gap-4">
                        <form id="exportForm" action="{{ route('ga.export') }}" method="GET"
                            class="flex items-center">
                            <input type="hidden" name="selected_ids" :value="selected.join(',')">
                            <button type="submit"
                                class="text-xs font-bold text-slate-800 hover:text-blue-700 uppercase flex items-center gap-1 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                </svg> Download Selected
                            </button>
                        </form>
                        <button type="button" @click="clearSelection()"
                            class="text-xs font-bold text-red-400 hover:text-red-600 uppercase transition-colors">Batal</button>
                    </div>
                </div>
            </div>

            {{-- D. DATA TABLE --}}
            <div class="bg-white shadow-xl rounded-sm overflow-hidden border border-slate-200">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-100">
                        <thead class="bg-slate-900">
                            <tr>
                                <th class="px-6 py-4 w-10"><input type="checkbox" @change="toggleSelectAll()"
                                        :checked="pageIds.length > 0 && pageIds.every(id => selected.includes(id))"
                                        class="rounded-sm border-slate-600 bg-slate-700 text-yellow-400 focus:ring-offset-slate-900 focus:ring-yellow-400 cursor-pointer">
                                </th>
                                @foreach (['Tiket', 'Pelapor', 'Lokasi / Dept', 'Parameter', 'Bobot', 'Uraian', 'Diterima Oleh', 'Status', 'Aksi'] as $head)
                                    <th
                                        class="px-6 py-4 text-left text-[11px] font-black text-white uppercase tracking-widest {{ $head == 'Tiket' ? 'text-yellow-400' : '' }}">
                                        {{ $head }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-slate-100">
                            @forelse ($workOrders as $item)
                                <tr class="hover:bg-yellow-50/50 transition-colors duration-150 group">
                                    <td class="px-6 py-4"><input type="checkbox" value="{{ (string) $item->id }}"
                                            x-model="selected"
                                            class="rounded-sm border-slate-300 text-slate-900 focus:ring-yellow-400 cursor-pointer" />
                                    </td>

                                    {{-- Tiket --}}
                                    <td class="px-6 py-4">
                                        <div
                                            class="text-sm font-black text-slate-900 font-mono group-hover:text-blue-600 transition-colors">
                                            {{ $item->ticket_num }}</div>
                                        <div class="text-[10px] text-slate-400 font-bold mt-0.5 uppercase">
                                            {{ $item->created_at->format('d M Y') }}</div>
                                    </td>

                                    <td class="px-6 py-4 text-xs font-bold text-slate-700">{{ $item->requester_name }}
                                    </td>

                                    {{-- Lokasi --}}
                                    <td class="px-6 py-4">
                                        <div class="flex flex-col gap-1 items-start">
                                            @if ($item->plant)
                                                <span
                                                    class="px-2 py-0.5 rounded-sm text-[10px] font-black bg-slate-100 text-slate-600 border border-slate-200 uppercase tracking-tight">LOC:
                                                    {{ $item->plant }}</span>
                                            @endif
                                            @if ($item->department)
                                                <span
                                                    class="px-2 py-0.5 rounded-sm text-[10px] font-black bg-slate-800 text-white uppercase tracking-tight">DEPT:
                                                    {{ $item->department }}</span>
                                            @endif
                                            @if (!$item->plant && !$item->department)
                                                <span class="text-xs text-slate-300 italic">-</span>
                                            @endif
                                        </div>
                                    </td>

                                    <td class="px-6 py-4 text-xs font-bold text-slate-600 uppercase">
                                        {{ $item->parameter_permintaan }}</td>

                                    {{-- Bobot --}}
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $catDisplay = match ($item->category) {
                                                'HIGH' => 'BERAT',
                                                'MEDIUM' => 'SEDANG',
                                                'LOW' => 'RINGAN',
                                                default => $item->category,
                                            };
                                            $catColor = match ($catDisplay) {
                                                'BERAT' => 'text-red-700 bg-red-50 border-red-200',
                                                'SEDANG' => 'text-yellow-700 bg-yellow-50 border-yellow-200',
                                                default => 'text-green-700 bg-green-50 border-green-200',
                                            };
                                        @endphp
                                        <span
                                            class="px-2 py-1 text-[10px] font-black rounded-sm border {{ $catColor }} uppercase tracking-wide">{{ $catDisplay }}</span>
                                    </td>

                                    <td class="px-6 py-4 text-xs text-slate-500 max-w-xs truncate font-medium">
                                        {{ Str::limit($item->description, 35) }}</td>

                                    {{-- PIC --}}
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if ($item->processed_by_name)
                                            <div class="flex items-center gap-2">
                                                <div
                                                    class="w-6 h-6 rounded-full bg-slate-800 text-white flex items-center justify-center text-[10px] font-black border border-slate-600">
                                                    {{ substr($item->processed_by_name, 0, 1) }}</div>
                                                <span
                                                    class="text-xs font-bold text-slate-700 uppercase">{{ $item->processed_by_name }}</span>
                                            </div>
                                        @else
                                            <span
                                                class="text-[10px] font-bold text-slate-400 uppercase tracking-wide border border-dashed border-slate-300 px-2 py-1 rounded-sm">Menunggu</span>
                                        @endif
                                    </td>

                                    {{-- Status --}}
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $statusClass = match ($item->status) {
                                                'completed' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                                                'pending' => 'bg-amber-100 text-amber-800 border-amber-200',
                                                'cancelled' => 'bg-rose-100 text-rose-800 border-rose-200',
                                                'in_progress' => 'bg-blue-100 text-blue-800 border-blue-200',
                                                default => 'bg-slate-100 text-slate-800',
                                            };
                                        @endphp
                                        <span
                                            class="px-3 py-1 text-[10px] font-black uppercase rounded-sm border {{ $statusClass }} tracking-wider">{{ str_replace('_', ' ', $item->status) }}</span>
                                    </td>

                                    <td class="px-6 py-4 text-sm font-medium whitespace-nowrap">
                                        <button
                                            @click='ticket = @json($item); ticket.user_name = "{{ $item->user->name ?? 'User' }}"; showDetailModal=true'
                                            class="text-slate-900 hover:text-yellow-600 font-bold mr-3 underline decoration-2 decoration-yellow-400 underline-offset-4 hover:decoration-slate-900 transition-all text-xs uppercase tracking-wide">Detail</button>
                                        @if (in_array(auth()->user()->role, ['ga.admin']))
                                            <button @click='openEditModal(@json($item))'
                                                class="text-slate-400 hover:text-slate-900 font-bold transition text-xs uppercase tracking-wide">Update</button>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="px-6 py-16 text-center">
                                        <div class="flex flex-col items-center justify-center">
                                            <svg class="w-12 h-12 text-slate-300 mb-3" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                                </path>
                                            </svg>
                                            <span class="text-slate-500 font-bold uppercase tracking-wide">Tidak ada
                                                data ditemukan</span>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                {{-- Pagination with custom styling --}}
                <div class="bg-slate-50 px-6 py-4 border-t border-slate-200">
                    {{ $workOrders->appends(request()->all())->links() }}
                </div>
            </div>

            {{-- 
                MODAL TEMPLATES (Sama Secara Logic, Styling Header Dipertajam) 
                Saya hanya memoles bagian header modal agar konsisten dengan tema "Industrial".
            --}}

            {{-- MODAL 1: CREATE TICKET --}}
            <template x-teleport="body">
                <div x-show="showCreateModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto">
                    <div class="fixed inset-0 bg-slate-900/80 backdrop-blur-sm transition-opacity"
                        @click="showCreateModal = false"></div>
                    <div class="flex min-h-full items-center justify-center p-4">
                        <div
                            class="relative w-full max-w-2xl bg-white rounded-none shadow-2xl overflow-hidden border-y-8 border-yellow-400 transform transition-all">
                            {{-- Header --}}
                            <div
                                class="bg-slate-900 px-6 py-5 flex justify-between items-center bg-[url('data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSI0IiBoZWlnaHQ9IjQiPgo8cmVjdCB3aWR0aD0iNCIgaGVpZ2h0PSI0IiBmaWxsPSIjMGYxNzJhIi8+CjxyZWN0IHdpZHRoPSIxIiBoZWlnaHQ9IjEiIGZpbGw9IiMzMzMiLz4KPC9zdmc+')]">
                                <h3
                                    class="text-xl font-black text-white uppercase tracking-wider flex items-center gap-3">
                                    <span class="bg-yellow-400 text-slate-900 px-2 text-sm">NEW</span> Form Work Order
                                </h3>
                                <button @click="showCreateModal = false"
                                    class="text-slate-500 hover:text-white transition-colors"><svg class="w-6 h-6"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12"></path>
                                    </svg></button>
                            </div>
                            {{-- Body (Keep logic same) --}}
                            <form x-ref="createForm" action="{{ route('ga.store') }}" method="POST"
                                enctype="multipart/form-data">
                                @csrf
                                <div class="p-8 space-y-6">
                                    {{-- DATE & TIME DISPLAY --}}
                                    <div class="grid grid-cols-2 gap-6">
                                        <div>
                                            <label
                                                class="text-[10px] font-black text-slate-400 uppercase mb-1 tracking-wider">Tanggal</label>
                                            <input type="text" x-model="currentDate" readonly
                                                class="w-full bg-slate-100 border-0 border-b-2 border-slate-300 font-mono text-sm font-bold text-slate-800 focus:ring-0">
                                        </div>
                                        <div>
                                            <label
                                                class="text-[10px] font-black text-slate-400 uppercase mb-1 tracking-wider">Jam</label>
                                            <input type="text" x-model="currentTime" readonly
                                                class="w-full bg-slate-100 border-0 border-b-2 border-slate-300 font-mono text-sm font-bold text-slate-800 focus:ring-0">
                                        </div>
                                    </div>

                                    {{-- REQUESTOR NAME --}}
                                    <div>
                                        <label class="block text-xs font-bold text-slate-700 uppercase mb-2">Nama
                                            Requestor <span class="text-red-500">*</span></label>
                                        <input type="text" name="manual_requester_name"
                                            class="w-full border-2 border-slate-300 focus:border-slate-900 focus:ring-0 rounded-sm text-sm font-bold placeholder-slate-300 py-3"
                                            placeholder="Masukkan Nama Lengkap..." required>
                                    </div>

                                    {{-- LOCATION GROUP --}}
                                    <div class="bg-slate-50 p-5 rounded-sm border border-slate-200">
                                        <label
                                            class="block text-xs font-black text-slate-400 uppercase mb-4 tracking-widest">Area
                                            Kerja</label>
                                        <div class="grid grid-cols-2 gap-4">
                                            <div>
                                                <label
                                                    class="text-xs font-bold text-slate-600 uppercase mb-1">Lokasi</label>
                                                <select name="plant_id" id="plantSelect" x-model="form.plant"
                                                    @change="updateDepartment()"
                                                    class="w-full border-2 border-slate-300 focus:border-slate-900 focus:ring-0 rounded-sm text-sm font-bold h-11"
                                                    required>
                                                    <option value="">-- PILIH LOKASI --</option>
                                                    @foreach ($plants as $plant)
                                                        <option value="{{ $plant->id }}">{{ $plant->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div>
                                                <label
                                                    class="text-xs font-bold text-slate-600 uppercase mb-1">Department</label>
                                                <select name="department" x-model="form.department"
                                                    class="w-full border-2 border-slate-300 focus:border-slate-900 focus:ring-0 rounded-sm text-sm font-bold bg-white h-11"
                                                    required>
                                                    <option value="">-- PILIH DEPT --</option>
                                                    <option value="Low Voltage">Low Voltage</option>
                                                    <option value="Medium Voltage">Medium Voltage</option>
                                                    <option value="IT">IT</option>
                                                    <option value="FH">FH</option>
                                                    <option value="PE">PE</option>
                                                    <option value="MT">MT</option>
                                                    <option value="GA">GA</option>
                                                    <option value="FO">FO</option>
                                                    <option value="SS">SS</option>
                                                    <option value="SC">SC</option>
                                                    <option value="RM">RM</option>
                                                    <option value="QR">QR</option>
                                                    <option value="FA">FA</option>
                                                    <option value="HC">HC</option>
                                                    <option value="Sales">Sales</option>
                                                    <option value="Marketing">Marketing</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- PARAMETERS --}}
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="text-xs font-bold text-slate-700 uppercase mb-1">Kategori
                                                Bobot</label>
                                            <select name="category" x-model="form.category"
                                                class="w-full border-2 border-slate-300 focus:border-slate-900 focus:ring-0 rounded-sm text-sm font-bold h-11">
                                                <option value="RINGAN">Ringan</option>
                                                <option value="SEDANG">Sedang</option>
                                                <option value="BERAT">Berat</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="text-xs font-bold text-slate-700 uppercase mb-1">Jenis
                                                Permintaan <span class="text-red-500">*</span></label>
                                            <select name="parameter_permintaan" x-model="form.parameter_permintaan"
                                                class="w-full border-2 border-slate-300 focus:border-slate-900 focus:ring-0 rounded-sm text-sm font-bold h-11"
                                                required>
                                                <option value="">-- PILIH --</option>
                                                <option value="KEBERSIHAN">Kebersihan</option>
                                                <option value="PEMELIHARAAN">Pemeliharaan</option>
                                                <option value="PERBAIKAN">Perbaikan</option>
                                                <option value="PEMBUATAN BARU">Pembuatan Baru</option>
                                                <option value="PERIZINAN">Perizinan</option>
                                                <option value="RESERVASI">Reservasi</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div>
                                        <label class="text-xs font-bold text-slate-700 uppercase mb-1">Status
                                            Permintaan</label>
                                        <select name="status_permintaan" x-model="form.status_permintaan"
                                            class="w-full border-2 border-slate-300 focus:border-slate-900 focus:ring-0 rounded-sm text-sm font-bold h-11">
                                            <option value="">-- Pilih --</option>
                                            <option value="OPEN">Open</option>
                                            <option value="SUDAH DIRENCANAKAN">Sudah Direncanakan</option>
                                        </select>
                                    </div>

                                    <div>
                                        <label class="text-xs font-bold text-slate-700 uppercase mb-1">Uraian Pekerjaan
                                            <span class="text-red-500">*</span></label>
                                        <textarea name="description" x-model="form.description" rows="3"
                                            class="w-full border-2 border-slate-300 focus:border-slate-900 focus:ring-0 rounded-sm text-sm font-medium"
                                            placeholder="Deskripsikan detail pekerjaan secara lengkap..." required></textarea>
                                    </div>

                                    <div>
                                        <label class="text-xs font-bold text-slate-700 uppercase mb-1">Foto Bukti
                                            (Opsional)</label>
                                        <input type="file" name="photo" @change="handleFile"
                                            class="block w-full text-sm text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-sm file:border-0 file:text-xs file:font-black file:uppercase file:bg-slate-900 file:text-white hover:file:bg-slate-700 cursor-pointer border border-slate-300 rounded-sm">
                                    </div>
                                </div>
                                <div
                                    class="px-8 py-5 bg-slate-50 flex flex-row-reverse gap-3 border-t border-slate-200">
                                    <button type="button" @click="showConfirmModal = true"
                                        class="bg-yellow-400 text-slate-900 hover:bg-yellow-300 px-8 py-3 rounded-sm font-black uppercase tracking-wider shadow-sm transition hover:-translate-y-0.5">Kirim
                                        Tiket</button>
                                    <button type="button" @click="showCreateModal = false"
                                        class="bg-white border-2 border-slate-200 text-slate-500 hover:border-slate-400 hover:text-slate-800 px-6 py-3 rounded-sm font-bold uppercase tracking-wide transition">Batal</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </template>

            {{-- MODAL 2: CONFIRMATION --}}
            <template x-teleport="body">
                <div x-show="showConfirmModal" style="display: none;" class="fixed inset-0 z-[60] overflow-y-auto">
                    <div class="fixed inset-0 bg-slate-900/90 backdrop-blur-sm transition-opacity"
                        @click="showConfirmModal = false"></div>
                    <div class="flex min-h-full items-center justify-center p-4 relative z-10">
                        <div
                            class="bg-white rounded-sm shadow-2xl max-w-sm w-full p-0 overflow-hidden transform transition-all">
                            <div class="h-2 bg-yellow-400 w-full"></div>
                            <div class="p-6">
                                <h3 class="text-xl font-black text-slate-900 uppercase mb-6 tracking-wide text-center">
                                    Konfirmasi Data</h3>
                                <div class="bg-slate-50 p-4 mb-6 border border-slate-200 text-sm space-y-3">
                                    <div class="flex justify-between border-b border-slate-200 pb-2"><span
                                            class="font-bold text-slate-400 text-xs uppercase">Lokasi</span><span
                                            class="font-bold text-slate-900 text-right"
                                            x-text="form.plant_name"></span></div>
                                    <div class="flex justify-between border-b border-slate-200 pb-2"><span
                                            class="font-bold text-slate-400 text-xs uppercase">Dept</span><span
                                            class="font-bold text-slate-900 text-right"
                                            x-text="form.department"></span></div>
                                    <div class="flex justify-between border-b border-slate-200 pb-2"><span
                                            class="font-bold text-slate-400 text-xs uppercase">Bobot</span><span
                                            class="font-bold text-slate-900 text-right" x-text="form.category"></span>
                                    </div>
                                    <div class="pt-1"><span
                                            class="font-bold text-slate-400 text-xs uppercase block mb-1">Uraian</span><span
                                            class="font-medium text-slate-800 leading-snug"
                                            x-text="form.description"></span></div>
                                </div>
                                <div class="flex flex-col gap-3">
                                    <button @click="submitForm()"
                                        class="w-full bg-slate-900 text-white py-3.5 rounded-sm font-black uppercase tracking-wider hover:bg-slate-800 transition shadow-lg">Ya,
                                        Proses</button>
                                    <button @click="showConfirmModal = false"
                                        class="w-full bg-white border-2 border-slate-200 text-slate-500 py-3.5 rounded-sm font-bold uppercase tracking-wider hover:bg-slate-50 transition">Periksa
                                        Lagi</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </template>

            {{-- Include Detail & Edit Modal (Logic kept same, just minor styling classes applied implicitly via Tailwind defaults) --}}
            {{-- Note: For brevity, I assume Detail/Edit Modal use similar classes. You can apply the same "Industrial" header style to them. --}}

            {{-- MODAL 3: DETAIL TICKET (Simplified for this response, keeping functionality) --}}
            <template x-teleport="body">
                <div x-show="showDetailModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto">
                    <div class="fixed inset-0 bg-slate-900/90 backdrop-blur-sm" @click="showDetailModal = false">
                    </div>
                    <div class="flex min-h-full items-center justify-center p-4">
                        <div
                            class="relative w-full max-w-3xl bg-white rounded-sm shadow-2xl border-t-8 border-yellow-400">
                            {{-- Header --}}
                            <div
                                class="bg-slate-100 px-6 py-4 flex justify-between items-center border-b border-slate-200">
                                <h3 class="text-lg font-black text-slate-800 uppercase tracking-wider">Detail Tiket
                                </h3>
                                <button @click="showDetailModal = false"
                                    class="text-slate-400 hover:text-red-500 text-2xl font-bold">&times;</button>
                            </div>
                            <div class="p-8 max-h-[80vh] overflow-y-auto">
                                <template x-if="ticket">
                                    <div>
                                        {{-- Ticket Info --}}
                                        <div
                                            class="flex justify-between items-end border-b-2 border-slate-100 pb-6 mb-6">
                                            <div>
                                                <span
                                                    class="text-xs font-bold text-slate-400 uppercase tracking-widest">NO
                                                    TIKET</span>
                                                <p class="text-4xl font-black text-slate-900 font-mono tracking-tighter mt-1"
                                                    x-text="ticket.ticket_num"></p>
                                            </div>
                                            <span
                                                class="px-4 py-2 bg-yellow-400 text-slate-900 font-black rounded-sm uppercase tracking-wide border-2 border-slate-900 text-sm"
                                                x-text="ticket.status.replace('_',' ')"></span>
                                        </div>
                                        {{-- Grid --}}
                                        <div class="grid grid-cols-2 gap-x-8 gap-y-6 mb-8 text-sm">
                                            <div><span
                                                    class="text-[10px] font-black text-slate-400 uppercase block">Lokasi</span><span
                                                    class="font-bold text-slate-800 text-base"
                                                    x-text="ticket.plant"></span></div>
                                            <div><span
                                                    class="text-[10px] font-black text-slate-400 uppercase block">Dept</span><span
                                                    class="font-bold text-slate-800 text-base"
                                                    x-text="ticket.department"></span></div>
                                            <div><span
                                                    class="text-[10px] font-black text-slate-400 uppercase block">Bobot</span><span
                                                    class="font-bold text-slate-800 text-base"
                                                    x-text="ticket.category"></span></div>
                                            <div><span
                                                    class="text-[10px] font-black text-slate-400 uppercase block">Jenis</span><span
                                                    class="font-bold text-slate-800 text-base"
                                                    x-text="ticket.parameter_permintaan"></span></div>
                                            <div class="col-span-2"><span
                                                    class="text-[10px] font-black text-slate-400 uppercase block mb-1">Uraian</span>
                                                <p class="bg-slate-50 p-4 border border-slate-200 rounded-sm font-medium text-slate-700 whitespace-pre-wrap"
                                                    x-text="ticket.description"></p>
                                            </div>
                                        </div>
                                        {{-- Photos Logic (Same as original) --}}
                                        <div class="grid grid-cols-2 gap-4">
                                            <template x-if="ticket.photo_path">
                                                <div>
                                                    <span
                                                        class="text-xs font-bold text-slate-500 uppercase mb-2 block">Foto
                                                        Awal</span>
                                                    <a :href="'/storage/' + ticket.photo_path" target="_blank"><img
                                                            :src="'/storage/' + ticket.photo_path"
                                                            class="w-full h-32 object-cover border rounded-sm hover:opacity-90"></a>
                                                </div>
                                            </template>
                                            <template x-if="ticket.photo_completed_path">
                                                <div>
                                                    <span
                                                        class="text-xs font-bold text-green-600 uppercase mb-2 block">Foto
                                                        Selesai</span>
                                                    <a :href="'/storage/' + ticket.photo_completed_path"
                                                        target="_blank"><img
                                                            :src="'/storage/' + ticket.photo_completed_path"
                                                            class="w-full h-32 object-cover border-2 border-green-400 rounded-sm hover:opacity-90"></a>
                                                </div>
                                            </template>
                                        </div>
                                        {{-- History --}}
                                        <div class="mt-6 pt-4 border-t border-slate-200">
                                            <h4 class="font-bold text-slate-900 uppercase text-xs tracking-wider mb-3">
                                                Log Aktivitas</h4>
                                            <div class="space-y-2">
                                                <template x-for="h in ticket.histories">
                                                    <div class="flex gap-3 text-xs">
                                                        <div class="font-mono font-bold text-slate-400"
                                                            x-text="new Date(h.created_at).toLocaleDateString('id-ID')">
                                                        </div>
                                                        <div><span class="font-bold text-slate-900"
                                                                x-text="h.action"></span> <span class="text-slate-500"
                                                                x-text="'- ' + h.description"></span></div>
                                                    </div>
                                                </template>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
            </template>

            {{-- MODAL 4: EDIT (Copy logic from original but use style above) --}}
            <template x-teleport="body">
                <div x-show="showEditModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto">
                    <div class="fixed inset-0 bg-slate-900/90 backdrop-blur-sm" @click="showEditModal = false"></div>
                    <div class="flex min-h-full items-center justify-center p-4 relative z-10">
                        <div class="bg-white rounded-none shadow-2xl w-full max-w-lg border-t-8 border-yellow-400">
                            {{-- Header --}}
                            <div class="bg-slate-900 px-6 py-4 flex justify-between items-center">
                                <h3
                                    class="text-lg font-black text-white uppercase tracking-wider flex items-center gap-2">
                                    <span class="text-yellow-400">///</span> <span
                                        x-text="editForm.status == 'pending' ? 'Approval' : 'Update'"></span>
                                </h3>
                                <div class="text-xs font-mono text-yellow-400 bg-slate-800 px-2 py-1 rounded"
                                    x-text="editForm.ticket_num"></div>
                            </div>
                            <form :action="'/ga/' + editForm.id + '/update-status'" method="POST"
                                enctype="multipart/form-data">
                                @csrf @method('PUT')
                                <div class="p-6 space-y-5">
                                    <div>
                                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Nama Admin
                                            / PIC <span class="text-red-500">*</span></label>
                                        <input type="text" name="admin_name"
                                            class="w-full border-2 border-slate-300 focus:border-slate-900 focus:ring-0 rounded-sm text-sm font-bold py-2.5"
                                            placeholder="Nama Anda..." required>
                                    </div>

                                    {{-- Logic Pending (Approval) --}}
                                    <template x-if="editForm.status == 'pending'">
                                        <div x-data="{ decision: null }">
                                            <div x-show="!decision" class="grid grid-cols-2 gap-4">
                                                <button type="button" @click="decision = 'accept'"
                                                    class="bg-yellow-400 text-slate-900 hover:bg-yellow-300 py-4 px-4 rounded-sm font-black uppercase tracking-wider shadow-sm hover:-translate-y-1 transition-all border-2 border-slate-900">Accept</button>
                                                <button type="button" @click="decision = 'decline'"
                                                    class="bg-white text-slate-600 hover:text-red-600 hover:border-red-400 py-4 px-4 rounded-sm font-black uppercase tracking-wider border-2 border-slate-300 transition-all">Decline</button>
                                            </div>
                                            {{-- Decline Form --}}
                                            <div x-show="decision == 'decline'"
                                                class="bg-red-50 p-4 rounded-sm border-l-4 border-red-500 mt-2">
                                                <h4 class="font-bold text-red-800 uppercase mb-3 text-xs">Konfirmasi
                                                    Penolakan</h4>
                                                <div class="flex gap-2">
                                                    <button type="submit" name="action" value="decline"
                                                        class="bg-red-600 text-white px-4 py-2 rounded-sm hover:bg-red-700 text-xs font-bold uppercase shadow-sm">Tolak
                                                        Tiket</button>
                                                    <button type="button" @click="decision = null"
                                                        class="text-slate-500 hover:text-slate-800 px-3 py-2 text-xs font-bold uppercase">Batal</button>
                                                </div>
                                            </div>
                                            {{-- Accept Form --}}
                                            <div x-show="decision == 'accept'"
                                                class="space-y-4 bg-yellow-50 p-5 rounded-sm border-l-4 border-yellow-400 mt-2">
                                                <h4
                                                    class="font-black text-slate-900 uppercase border-b border-yellow-200 pb-2 mb-2 text-xs">
                                                    Parameter Pengerjaan</h4>
                                                <div>
                                                    <label
                                                        class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Bobot</label>
                                                    <select name="category" x-model="editForm.category"
                                                        class="w-full border border-slate-300 rounded-sm text-xs font-bold h-9">
                                                        <option value="RINGAN">Ringan</option>
                                                        <option value="SEDANG">Sedang</option>
                                                        <option value="BERAT">Berat</option>
                                                    </select>
                                                </div>
                                                <div>
                                                    <label
                                                        class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Target
                                                        Penyelesaian</label>
                                                    <input type="text" name="target_date"
                                                        class="w-full border border-slate-300 rounded-sm date-picker text-xs font-bold h-9"
                                                        placeholder="Pilih Tanggal..." x-init="flatpickr($el, { dateFormat: 'Y-m-d', minDate: 'today' })">
                                                </div>
                                                <div class="flex gap-2 pt-2">
                                                    <button type="submit" name="action" value="accept"
                                                        class="bg-slate-900 text-white px-4 py-2 rounded-sm hover:bg-slate-800 text-xs font-black uppercase shadow-md">Simpan</button>
                                                    <button type="button" @click="decision = null"
                                                        class="bg-white border border-slate-300 text-slate-600 px-3 py-2 rounded-sm text-xs font-bold uppercase">Batal</button>
                                                </div>
                                            </div>
                                        </div>
                                    </template>

                                    {{-- Logic Update (Ongoing) --}}
                                    <template x-if="editForm.status != 'pending'">
                                        <div class="space-y-4 bg-slate-50 p-5 border border-slate-200">
                                            <div>
                                                <label
                                                    class="block text-xs font-bold text-slate-600 uppercase mb-1">Status
                                                    Baru</label>
                                                <select name="status" x-model="editForm.status"
                                                    class="w-full border-2 border-slate-300 rounded-sm text-sm font-bold">
                                                    <option value="in_progress">In Progress</option>
                                                    <option value="completed">Completed</option>
                                                    <option value="cancelled">Cancelled</option>
                                                </select>
                                            </div>
                                            {{-- Dept Update --}}
                                            <div>
                                                <label
                                                    class="block text-xs font-bold text-slate-600 uppercase mb-1">Update
                                                    Dept <span
                                                        class="text-slate-400 font-normal italic">(Opsional)</span></label>
                                                <select name="department" x-model="editForm.department"
                                                    class="w-full border-2 border-slate-300 rounded-sm text-sm font-semibold">
                                                    <option value="">-- Tidak Berubah --</option>
                                                    <option value="Low Voltage">Low Voltage</option>
                                                    <option value="Medium Voltage">Medium Voltage</option>
                                                    <option value="IT">IT</option>
                                                    <option value="FH">FH</option>
                                                    <option value="PE">PE</option>
                                                    <option value="MT">MT</option>
                                                    <option value="GA">GA</option>
                                                    <option value="FO">FO</option>
                                                    <option value="SS">SS</option>
                                                    <option value="SC">SC</option>
                                                    <option value="RM">RM</option>
                                                    <option value="QR">QR</option>
                                                    <option value="FA">FA</option>
                                                    <option value="HC">HC</option>
                                                    <option value="Sales">Sales</option>
                                                    <option value="Marketing">Marketing</option>
                                                </select>
                                            </div>

                                            <div x-show="editForm.status == 'completed'">
                                                <label
                                                    class="block text-xs font-bold text-emerald-700 uppercase mb-1">Foto
                                                    Bukti</label>
                                                <input type="file" name="completion_photo"
                                                    class="block w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-sm file:border-0 file:text-xs file:font-bold file:uppercase file:bg-emerald-600 file:text-white hover:file:bg-emerald-700 border border-emerald-200 bg-white">
                                            </div>

                                            <div x-show="editForm.status == 'in_progress'">
                                                <label
                                                    class="block text-xs font-bold text-blue-700 uppercase mb-1">Revisi
                                                    Target</label>
                                                <input type="text" name="target_date"
                                                    x-model="editForm.target_date"
                                                    class="w-full border-2 border-blue-200 rounded-sm date-picker text-sm"
                                                    placeholder="Update Tanggal..." x-init="flatpickr($el, { dateFormat: 'Y-m-d', minDate: 'today' })">
                                            </div>
                                            <div class="flex justify-end gap-2 mt-4">
                                                <button type="submit"
                                                    class="bg-yellow-400 text-slate-900 px-5 py-2 rounded-sm font-black uppercase text-xs shadow-sm">Simpan</button>
                                                <button type="button" @click="showEditModal = false"
                                                    class="bg-white border border-slate-300 text-slate-600 px-4 py-2 rounded-sm font-bold uppercase text-xs">Batal</button>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </template>

        </div>

        {{-- SCRIPT DATE RANGE DAN ALERT (Sama seperti original) --}}
        @if (session('success'))
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        title: 'Berhasil!',
                        text: "{{ session('success') }}",
                        icon: 'success',
                        confirmButtonColor: '#3085d6',
                        confirmButtonText: 'OK'
                    });
                });
            </script>
        @endif
        @if (session('error'))
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        title: 'Gagal!',
                        text: "{{ session('error') }}",
                        icon: 'error',
                        confirmButtonColor: '#d33',
                        confirmButtonText: 'Tutup'
                    });
                });
            </script>
        @endif
        @push('scripts')
            <script>
                document.addEventListener('alpine:init', () => {
                    Alpine.data('gaForm', () => ({
                        initFlatpickr() {
                            flatpickr(".date-picker", {
                                dateFormat: "Y-m-d",
                                minDate: "today",
                                allowInput: true
                            });
                        }
                    }));
                });
                document.addEventListener('DOMContentLoaded', function() {
                    const pickerInput = document.getElementById("date_range_picker");
                    if (pickerInput) {
                        flatpickr(pickerInput, {
                            mode: "range",
                            dateFormat: "Y-m-d",
                            altInput: true,
                            altFormat: "j F Y",
                            defaultDate: ["{{ request('start_date') }}", "{{ request('end_date') }}"],
                            onChange: function(selectedDates, dateStr, instance) {
                                if (selectedDates.length === 2) {
                                    const offset = selectedDates[0].getTimezoneOffset();
                                    const startDate = new Date(selectedDates[0].getTime() - (offset * 60 *
                                        1000)).toISOString().split('T')[0];
                                    const endDate = new Date(selectedDates[1].getTime() - (offset * 60 * 1000))
                                        .toISOString().split('T')[0];
                                    document.getElementById('start_date').value = startDate;
                                    document.getElementById('end_date').value = endDate;
                                }
                            },
                            onClose: function(selectedDates) {
                                if (selectedDates.length === 0) {
                                    document.getElementById('start_date').value = "";
                                    document.getElementById('end_date').value = "";
                                }
                            }
                        });
                    }
                });
            </script>
        @endpush
    </div>
</x-app-layout>
