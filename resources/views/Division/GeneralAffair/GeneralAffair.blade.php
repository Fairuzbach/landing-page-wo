@section('browser_title', 'General Affair Work Order')
<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center -my-2">
            <h2
                class="font-black text-2xl text-slate-900 leading-tight uppercase tracking-wider flex items-center gap-4">
                {{-- Aksen Balok Kuning Khas CAT --}}
                <span class="w-4 h-8 bg-yellow-400 inline-block shadow-sm"></span>
                {{ __('General Affair Request Order') }}
            </h2>
        </div>
    </x-slot>

    {{-- LOAD LIBRARY --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


    <div class="py-12 bg-slate-100 min-h-screen font-sans" x-data="{
        // --- 1. MODAL STATES ---
        showDetailModal: false,
        showCreateModal: false,
        showConfirmModal: false,
        showEditModal: false,
        show: false,
    
        selected: JSON.parse(localStorage.getItem('ga_selected_ids') || '[]').map(String),
        pageIds: {{ Js::from($pageIds ?? []) }}.map(String),
    
        // --- MAPPING LOKASI KE DEPARTMENT (Update Terbaru) ---
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
            'Plant SC': 'SC',
            'Plant RM1': 'RM',
            'Plant RM2': 'RM',
            'Plant RM5': 'RM',
            'Workshop Electric': 'MT',
            'Konstruksi': 'FH',
            'Plant E': 'FO',
            'Plant Tools': 'PE',
            'Gudang Jadi': 'SS'
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
                    this.form.category = 'LOW';
                    this.form.description = '';
                    this.form.file_name = '';
                }
            });
        }
    }" x-init="init()">

        @if ($errors->any())
            <div x-init="setTimeout(() => showCreateModal = true, 500)"></div>
        @endif
        <div class="max-w-8xl mx-auto sm:px-6 lg:px-8">

            {{-- A. ALERT SUCCESS --}}
            @if (session('success'))
                <script>
                    // FIX: Sebelumnya tertulis function({ yang salah sintaks
                    document.addEventListener('DOMContentLoaded', function() {
                        Swal.fire({
                            title: 'Berhasil',
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
            {{-- B. STATISTIK CARDS --}}
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





            {{-- C. TABEL DATA --}}
            {{-- debug role --}}
            {{-- <div class="bg-red-200 p-2 text-red-800">
                Role Anda: <strong>{{ auth()->user()->role }}</strong> <br>
                Dept Anda: <strong>{{ auth()->user()->divisi ?? 'Tidak ada dept' }}</strong>
            </div> --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-slate-900">

                    {{-- Header & Create Button --}}
                    {{-- C. FILTER & ACTIONS (Tactile Inputs) --}}
                    <div
                        class="bg-white shadow-lg rounded-none border-t-4 border-yellow-400 mb-8 p-6 transition-shadow hover:shadow-xl duration-500">
                        <div class="flex flex-col lg:flex-row justify-between items-end gap-6">

                            {{-- Form Filter --}}
                            {{-- Form Filter --}}
                            <form action="{{ route('ga.index') }}" method="GET"
                                class="w-full lg:flex-1 flex flex-col md:flex-row gap-4">

                                {{-- 1. Input Pencarian --}}
                                <div class="flex-1 group">
                                    <label
                                        class="block text-xs font-bold text-slate-500 uppercase mb-1 tracking-wider group-focus-within:text-yellow-600 transition-colors">Pencarian</label>
                                    <div class="relative">
                                        <span
                                            class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400 group-focus-within:text-yellow-500 transition-colors">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                            </svg>
                                        </span>
                                        <input type="text" name="search" value="{{ request('search') }}"
                                            class="block w-full pl-10 border-2 border-slate-200 bg-slate-50 rounded-sm focus:bg-white focus:border-yellow-400 focus:ring-0 text-sm font-bold text-slate-900 transition-all duration-200 placeholder-slate-400"
                                            placeholder="Cari Tiket / Lokasi...">
                                    </div>
                                </div>

                                {{-- 2. Filter Bobot (BARU) --}}
                                <div class="w-full md:w-40 group">
                                    <label
                                        class="block text-xs font-bold text-slate-500 uppercase mb-1 tracking-wider group-focus-within:text-yellow-600 transition-colors">Bobot</label>
                                    <select name="category"
                                        class="block w-full border-2 border-slate-200 bg-slate-50 rounded-sm focus:bg-white focus:border-yellow-400 focus:ring-0 text-sm font-bold text-slate-900 transition-all duration-200 cursor-pointer uppercase">
                                        <option value="">SEMUA BOBOT</option>
                                        <option value="HIGH" {{ request('category') == 'HIGH' ? 'selected' : '' }}>
                                            BERAT</option>
                                        <option value="MEDIUM" {{ request('category') == 'MEDIUM' ? 'selected' : '' }}>
                                            SEDANG</option>
                                        <option value="LOW" {{ request('category') == 'LOW' ? 'selected' : '' }}>
                                            RINGAN</option>
                                    </select>
                                </div>

                                {{-- 3. Filter Status --}}
                                <div class="w-full md:w-40 group">
                                    <label
                                        class="block text-xs font-bold text-slate-500 uppercase mb-1 tracking-wider group-focus-within:text-yellow-600 transition-colors">Status</label>
                                    <select name="status"
                                        class="block w-full border-2 border-slate-200 bg-slate-50 rounded-sm focus:bg-white focus:border-yellow-400 focus:ring-0 text-sm font-bold text-slate-900 transition-all duration-200 cursor-pointer uppercase">
                                        <option value="">SEMUA STATUS</option>
                                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>
                                            PENDING</option>
                                        <option value="in_progress"
                                            {{ request('status') == 'in_progress' ? 'selected' : '' }}>IN PROGRESS
                                        </option>
                                        <option value="completed"
                                            {{ request('status') == 'completed' ? 'selected' : '' }}>COMPLETED</option>
                                        <option value="cancelled"
                                            {{ request('status') == 'cancelled' ? 'selected' : '' }}>CANCELLED</option>
                                    </select>
                                </div>
                                {{-- 4. FILTER RENTANG TANGGAL (FLATPICKR) --}}
                                <div class="w-full md:w-64 group">
                                    <label
                                        class="block text-xs font-bold text-slate-500 uppercase mb-1 tracking-wider group-focus-within:text-yellow-600 transition-colors">
                                        Rentang Tanggal
                                    </label>
                                    <div class="relative">
                                        <div
                                            class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400 group-focus-within:text-yellow-500 transition-colors">
                                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                        </div>

                                        <input type="text" id="date_range_picker"
                                            class="block w-full pl-10 border-2 border-slate-200 bg-slate-50 rounded-sm focus:bg-white focus:border-yellow-400 focus:ring-0 text-sm font-bold text-slate-900 transition-all duration-200 placeholder-slate-400"
                                            placeholder="PILIH TANGGAL...">

                                        <input type="hidden" name="start_date" id="start_date"
                                            value="{{ request('start_date') }}">
                                        <input type="hidden" name="end_date" id="end_date"
                                            value="{{ request('end_date') }}">
                                    </div>
                                </div>
                                {{-- Tombol Aksi --}}
                                <div class="flex gap-2 items-end">
                                    <button type="submit"
                                        class="bg-slate-900 text-white hover:bg-slate-800 hover:shadow-lg px-6 py-2.5 rounded-sm text-sm font-black uppercase tracking-wider transition-all duration-200 active:scale-95 flex items-center gap-2 h-[42px]">
                                        {{-- Set height fix --}}
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z">
                                            </path>
                                        </svg>
                                        Filter
                                    </button>
                                    <a href="{{ route('ga.index') }}"
                                        class="bg-white border-2 border-slate-200 text-slate-600 hover:border-slate-400 hover:text-slate-800 px-4 py-2.5 rounded-sm text-sm font-bold uppercase tracking-wider transition-all duration-200 active:scale-95 text-center flex items-center justify-center h-[42px]">
                                        Reset
                                    </a>
                                </div>
                            </form>

                            {{-- Action Buttons --}}
                            <div class="flex gap-3 w-full lg:w-auto justify-end">
                                @if (auth()->user()->role === 'ga.admin')
                                    <a href="{{ route('ga.dashboard') }}"
                                        class="group bg-white border-2 border-slate-900 text-slate-900 hover:bg-slate-900 hover:text-yellow-400 px-5 py-2.5 rounded-sm text-sm font-black uppercase tracking-wider transition-all duration-200 flex items-center gap-2 active:scale-95 shadow-sm hover:shadow-md">
                                        <svg class="w-5 h-5 transition-transform group-hover:scale-110" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                                            </path>
                                        </svg>
                                        Statistik
                                    </a>
                                @endif

                                <button @click="showCreateModal = true" type="button"
                                    class="group bg-yellow-400 text-slate-900 hover:bg-yellow-300 px-6 py-2.5 rounded-sm text-sm font-black uppercase tracking-wider shadow-md hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200 flex items-center gap-2 active:scale-95">
                                    <svg class="w-5 h-5 transition-transform group-hover:rotate-90" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                    Buat Tiket
                                </button>
                            </div>
                        </div>

                        {{-- Export Bar (Interactive Link) --}}
                        <div class="flex justify-end mt-4 pt-4 border-t border-slate-100 gap-6" x-show="true">
                            <form id="exportForm" action="{{ route('ga.export') }}" method="GET"
                                class="flex items-center gap-4">
                                <input type="hidden" name="selected_ids" :value="selected.join(',')">

                                <div class="flex items-center gap-2 text-xs font-bold text-slate-400 uppercase tracking-wider transition-opacity duration-300"
                                    :class="selected.length > 0 ? 'opacity-100' : 'opacity-0 pointer-events-none'">
                                    <span class="w-2 h-2 bg-yellow-400 rounded-full animate-pulse"></span>
                                    <span x-text="selected.length"></span> Item Dipilih
                                </div>

                                <button type="submit"
                                    class="group text-xs font-bold text-slate-600 hover:text-slate-900 uppercase flex items-center gap-1 transition-colors">
                                    <span
                                        class="group-hover:underline decoration-yellow-400 underline-offset-4">Download
                                        Data (XSLX)</span>
                                    <svg class="w-4 h-4 text-slate-400 group-hover:text-yellow-500 transition-colors"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                    </svg>
                                </button>

                                <button type="button" x-show="selected.length > 0" @click="clearSelection()"
                                    class="text-xs font-bold text-red-500 hover:text-red-700 uppercase transition-colors flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                    Reset
                                </button>
                            </form>
                        </div>
                    </div>

                    {{-- Tabel --}}
                    <table class="min-w-full divide-y divide-slate-200">
                        {{-- Header Hitam Pekat (Industrial) --}}
                        <thead class="bg-slate-900 border-b-4 border-yellow-400">
                            <tr>
                                <th class="px-6 py-4 w-10">
                                    <input type="checkbox" @change="toggleSelectAll()"
                                        :checked="pageIds.length > 0 && pageIds.every(id => selected.includes(id))"
                                        class="rounded-sm border-slate-500 bg-slate-700 text-yellow-400 focus:ring-yellow-400 cursor-pointer">
                                </th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-black text-yellow-400 uppercase tracking-widest">
                                    Tiket</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">
                                    Pelapor</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">
                                    Lokasi / Dept</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">
                                    Bobot</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">
                                    Uraian</th>

                                {{-- KOLOM BARU: DITERIMA OLEH --}}
                                <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">
                                    Diterima Oleh</th>

                                <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">
                                    Status</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">
                                    Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-slate-200">
                            @forelse ($workOrders as $item)
                                <tr class="hover:bg-yellow-50 transition-colors group">
                                    <td class="px-6 py-4"><input type="checkbox" value="{{ (string) $item->id }}"
                                            x-model="selected"
                                            class="rounded-sm border-slate-300 text-slate-900 focus:ring-yellow-400 cursor-pointer" />
                                    </td>

                                    {{-- Kolom Tiket --}}
                                    <td class="px-6 py-4">
                                        <div
                                            class="text-sm font-black text-slate-900 font-mono group-hover:text-blue-700">
                                            {{ $item->ticket_num }}</div>
                                        <div class="text-xs text-slate-500 font-bold mt-1">
                                            {{ $item->created_at->format('d M Y') }}</div>
                                    </td>

                                    <td class="px-6 py-4 text-sm font-bold text-slate-700">{{ $item->requester_name }}
                                    </td>

                                    {{-- Lokasi / Dept --}}
                                    <td class="px-6 py-4">
                                        <div class="flex flex-col gap-1 items-start">
                                            @if ($item->plant)
                                                <span
                                                    class="px-2 py-0.5 rounded-sm text-[10px] font-black bg-slate-200 text-slate-800 uppercase tracking-wide">LOC:
                                                    {{ $item->plant }}</span>
                                            @endif
                                            @if ($item->department)
                                                <span
                                                    class="px-2 py-0.5 rounded-sm text-[10px] font-black bg-slate-800 text-white uppercase tracking-wide">DEPT:
                                                    {{ $item->department }}</span>
                                            @endif
                                            @if (!$item->plant && !$item->department)
                                                <span class="text-xs text-slate-400 italic">-</span>
                                            @endif
                                        </div>
                                    </td>

                                    {{-- Bobot --}}
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            class="px-2 py-1 text-xs font-bold rounded-sm border 
                                            {{ $item->category == 'HIGH' ? 'bg-red-100 text-red-800 border-red-200' : ($item->category == 'MEDIUM' ? 'bg-yellow-100 text-yellow-800 border-yellow-200' : 'bg-green-100 text-green-800 border-green-200') }}">
                                            {{ $item->category == 'HIGH' ? 'BERAT' : ($item->category == 'MEDIUM' ? 'SEDANG' : 'RINGAN') }}
                                        </span>
                                    </td>

                                    <td class="px-6 py-4 text-sm text-slate-600 truncate max-w-xs font-medium">
                                        {{ Str::limit($item->description, 40) }}</td>

                                    {{-- KOLOM BARU: DITERIMA OLEH --}}
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if ($item->processed_by_name)
                                            <div class="flex items-center gap-2">
                                                <div
                                                    class="w-6 h-6 rounded-full bg-slate-200 flex items-center justify-center text-[10px] font-black text-slate-600 border border-slate-300">
                                                    {{ substr($item->processed_by_name, 0, 1) }}
                                                </div>
                                                <span
                                                    class="text-sm font-bold text-slate-800 uppercase">{{ $item->processed_by_name }}</span>
                                            </div>
                                        @else
                                            <span
                                                class="text-[10px] font-bold text-slate-400 uppercase tracking-wide border border-dashed border-slate-300 px-2 py-1 rounded-sm">Belum
                                                Diproses</span>
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
                                            class="px-3 py-1 text-xs font-bold uppercase rounded-sm border {{ $statusClass }}">
                                            {{ str_replace('_', ' ', $item->status) }}
                                        </span>
                                    </td>

                                    <td class="px-6 py-4 text-sm font-medium">
                                        <button
                                            @click='ticket = @json($item); ticket.user_name = "{{ $item->user->name ?? 'User' }}"; showDetailModal=true'
                                            class="text-slate-900 hover:text-yellow-600 font-bold mr-3 underline decoration-2 decoration-yellow-400 underline-offset-2">Detail</button>
                                        @if (in_array(auth()->user()->role, ['ga.admin']))
                                            <button @click='openEditModal(@json($item))'
                                                class="text-slate-400 hover:text-slate-900 font-bold transition">Update</button>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9"
                                        class="px-6 py-12 text-center text-slate-500 font-bold uppercase tracking-wide">
                                        Data tidak ditemukan</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div class="mt-4">
                        {{ $workOrders->appends(request()->all())->links() }}
                    </div>
                </div>

                {{-- MODAL 1: CREATE TICKET (GA) --}}
                <template x-teleport="body">
                    <div x-show="showCreateModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto">
                        <div class="fixed inset-0 bg-slate-900 bg-opacity-90 transition-opacity"
                            @click="showCreateModal = false"></div>
                        <div class="flex min-h-full items-center justify-center p-4">
                            <div
                                class="relative w-full max-w-2xl bg-white rounded-none shadow-2xl overflow-hidden border-t-8 border-yellow-400 transform transition-all">

                                {{-- Header Modal Hitam --}}
                                <div class="bg-slate-900 px-6 py-4 flex justify-between items-center">
                                    <h3
                                        class="text-xl font-black text-white uppercase tracking-wider flex items-center gap-2">
                                        <span class="text-yellow-400">///</span> Form Work Order
                                    </h3>
                                    <button @click="showCreateModal = false"
                                        class="text-slate-400 hover:text-white font-bold text-2xl leading-none">&times;</button>
                                </div>

                                <form x-ref="createForm" action="{{ route('ga.store') }}" method="POST"
                                    enctype="multipart/form-data">
                                    @csrf
                                    <div class="p-6 space-y-6">
                                        {{-- Row 1 --}}
                                        <div class="grid grid-cols-2 gap-4">
                                            <div><label
                                                    class="text-xs font-bold text-slate-700 uppercase mb-1">Tanggal</label><input
                                                    type="text" x-model="currentDate" readonly
                                                    class="w-full bg-slate-100 border-2 border-slate-200 font-mono text-sm rounded-sm font-bold text-slate-600">
                                            </div>
                                            <div><label
                                                    class="text-xs font-bold text-slate-700 uppercase mb-1">Jam</label><input
                                                    type="text" x-model="currentTime" readonly
                                                    class="w-full bg-slate-100 border-2 border-slate-200 font-mono text-sm rounded-sm font-bold text-slate-600">
                                            </div>
                                        </div>

                                        {{-- Row 2: LOKASI & DEPT --}}
                                        <div class="bg-yellow-50 p-5 rounded-sm border-2 border-yellow-200">
                                            <label
                                                class="block text-sm font-black text-slate-900 uppercase mb-3 border-b border-yellow-200 pb-1">Area
                                                Kerja</label>
                                            <div class="grid grid-cols-2 gap-4">
                                                <div>
                                                    <label
                                                        class="text-xs font-bold text-slate-600 uppercase mb-1">Lokasi</label>
                                                    <select name="plant_id" id="plantSelect" x-model="form.plant"
                                                        @change="updateDepartment()"
                                                        class="w-full border-2 border-slate-300 focus:border-slate-900 focus:ring-0 rounded-sm text-sm font-semibold"
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
                                                        class="w-full border-2 border-slate-300 focus:border-slate-900 focus:ring-0 rounded-sm text-sm font-semibold bg-slate-50"
                                                        required>
                                                        <option value="">-- PILIH DEPT --</option>
                                                        <option value="IT">IT</option>
                                                        <option value="FH">FH</option>
                                                        <option value="PE">PE</option>
                                                        <option value="MT">MT</option>
                                                        <option value="GA">GA</option>
                                                        <option value="FO">FO</option>
                                                        <option value="SS">SS</option>
                                                        <option value="SC">SC</option>
                                                        <option value="Low Voltage">Low Voltage</option>
                                                        <option value="Medium Voltage">Medium Voltage</option>
                                                        <option value="RM">RM</option>
                                                        <option value="QR">QR</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Row 3 --}}
                                        <div class="grid grid-cols-2 gap-4">
                                            <div>
                                                <label class="text-xs font-bold text-slate-700 uppercase mb-1">Bobot
                                                    Pekerjaan</label>
                                                <select name="category" x-model="form.category"
                                                    class="w-full border-2 border-slate-300 focus:border-slate-900 focus:ring-0 rounded-sm text-sm">
                                                    <option value="LOW">Ringan</option>
                                                    <option value="MEDIUM">Sedang</option>
                                                    <option value="HIGH">Berat</option>
                                                </select>
                                            </div>
                                            <div>
                                                <label
                                                    class="text-xs font-bold text-slate-700 uppercase mb-1">Parameter
                                                    <span class="text-red-500">*</span></label>
                                                <select name="parameter_permintaan"
                                                    x-model="form.parameter_permintaan"
                                                    class="w-full border-2 border-slate-300 focus:border-slate-900 focus:ring-0 rounded-sm text-sm"
                                                    required>
                                                    <option value="">-- PILIH --</option>
                                                    <option value="KEBERSIHAN">Kebersihan</option>
                                                    <option value="PEMELIHARAAN">Pemeliharaan</option>
                                                    <option value="PERBAIKAN">Perbaikan</option>
                                                    <option value="PEMBUATAN BARU">Pembuatan Baru</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div>
                                            <label class="text-xs font-bold text-slate-700 uppercase mb-1">Status
                                                Permintaan</label>
                                            <select name="status_permintaan" x-model="form.status_permintaan"
                                                class="w-full border-2 border-slate-300 focus:border-slate-900 focus:ring-0 rounded-sm text-sm">
                                                <option value="">-- Pilih --</option>
                                                <option value="OPEN">Open</option>
                                                <option value="SUDAH DIRENCANAKAN">Sudah Direncanakan</option>
                                            </select>
                                        </div>

                                        <div>
                                            <label class="text-xs font-bold text-slate-700 uppercase mb-1">Uraian
                                                Pekerjaan <span class="text-red-500">*</span></label>
                                            <textarea name="description" x-model="form.description" rows="3"
                                                class="w-full border-2 border-slate-300 focus:border-slate-900 focus:ring-0 rounded-sm text-sm"
                                                placeholder="Deskripsikan detail pekerjaan..." required></textarea>
                                        </div>

                                        <div>
                                            <label class="text-xs font-bold text-slate-700 uppercase mb-1">Foto Bukti
                                                <span class="text-red-500">*</span></label>
                                            <input type="file" name="photo" @change="handleFile" required
                                                class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-sm file:border-0 file:text-xs file:font-black file:uppercase file:bg-slate-900 file:text-white hover:file:bg-slate-700 cursor-pointer">
                                        </div>
                                    </div>

                                    {{-- Footer Modal --}}
                                    <div
                                        class="px-6 py-4 bg-slate-100 flex flex-row-reverse gap-3 border-t border-slate-200">
                                        <button type="button" @click="showConfirmModal = true"
                                            class="bg-yellow-400 text-slate-900 hover:bg-yellow-300 px-6 py-2.5 rounded-sm font-black uppercase tracking-wider shadow-sm transition">
                                            Kirim Tiket
                                        </button>
                                        <button type="button" @click="showCreateModal = false"
                                            class="bg-white border-2 border-slate-300 text-slate-700 hover:bg-slate-200 px-4 py-2.5 rounded-sm font-bold uppercase tracking-wide transition">
                                            Batal
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </template>

                {{-- MODAL 2: CONFIRMATION --}}
                <template x-teleport="body">
                    <div x-show="showConfirmModal" style="display: none;"
                        class="fixed inset-0 z-[60] overflow-y-auto">
                        <div class="fixed inset-0 bg-slate-900 bg-opacity-75 transition-opacity"
                            @click="showConfirmModal = false"></div>
                        <div class="flex min-h-full items-center justify-center p-4 relative z-10">
                            <div
                                class="bg-white rounded-none shadow-xl max-w-sm w-full p-6 border-t-8 border-yellow-400 transform transition-all">

                                <h3 class="text-lg font-black text-slate-900 uppercase mb-4 tracking-wider">
                                    Konfirmasi Data
                                </h3>

                                <div class="bg-slate-100 p-4 mb-6 border border-slate-200 text-sm space-y-2">
                                    <p>
                                        <span class="font-bold text-slate-500 uppercase text-xs block">Lokasi:</span>
                                        {{-- FIX: Pastikan x-data sudah ada plant_name seperti saran sebelumnya --}}
                                        <span class="font-bold text-slate-900" x-text="form.plant_name"></span>
                                    </p>
                                    <p>
                                        <span class="font-bold text-slate-500 uppercase text-xs block">Dept:</span>
                                        <span class="font-bold text-slate-900" x-text="form.department"></span>
                                    </p>
                                    <p>
                                        <span class="font-bold text-slate-500 uppercase text-xs block">Bobot:</span>
                                        <span class="font-bold text-slate-900" x-text="form.category"></span>
                                    </p>
                                    <p>
                                        <span class="font-bold text-slate-500 uppercase text-xs block">Uraian
                                            Pekerjaan:</span>
                                        <span class="font-bold text-slate-900" x-text="form.description"></span>
                                    </p>
                                </div>

                                <div class="flex flex-col gap-3">
                                    <button @click="submitForm()"
                                        class="w-full bg-slate-900 text-white py-3 rounded-sm font-black uppercase tracking-wider hover:bg-slate-800 transition">
                                        Ya, Proses
                                    </button>
                                    <button @click="showConfirmModal = false"
                                        class="w-full bg-white border-2 border-slate-300 text-slate-600 py-3 rounded-sm font-bold uppercase tracking-wider hover:bg-slate-100 transition">
                                        Periksa Lagi
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>

                {{-- MODAL 3: DETAIL TICKET --}}
                <template x-teleport="body">
                    <div x-show="showDetailModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto">
                        <div class="fixed inset-0 bg-slate-900 bg-opacity-90" @click="showDetailModal = false"></div>
                        <div class="flex min-h-full items-center justify-center p-4">
                            <div
                                class="relative w-full max-w-3xl bg-white rounded-none shadow-2xl border-t-8 border-yellow-400">

                                {{-- Header Modal --}}
                                <div class="bg-slate-900 px-6 py-4 flex justify-between items-center">
                                    <h3 class="text-xl font-black text-white uppercase tracking-wider"><span
                                            class="text-yellow-400">///</span> Detail Tiket</h3>
                                    <button @click="showDetailModal = false"
                                        class="text-white text-2xl font-bold hover:text-yellow-400 transition">&times;</button>
                                </div>

                                <div class="p-6 max-h-[80vh] overflow-y-auto">
                                    <template x-if="ticket">
                                        <div>
                                            {{-- Header Info Tiket --}}
                                            <div
                                                class="flex justify-between items-start border-b-2 border-slate-100 pb-4 mb-4">
                                                <div>
                                                    <span class="text-xs font-bold text-slate-500 uppercase">NO
                                                        TIKET</span>
                                                    <p class="text-3xl font-black text-slate-900 font-mono"
                                                        x-text="ticket.ticket_num"></p>
                                                </div>
                                                <div class="text-right">
                                                    <span
                                                        class="px-4 py-1 bg-yellow-400 text-slate-900 font-black rounded-sm uppercase tracking-wide border-2 border-slate-900"
                                                        x-text="ticket.status.replace('_',' ')"></span>
                                                </div>
                                            </div>

                                            {{-- Grid Detail --}}
                                            <div class="grid grid-cols-2 gap-6 mb-6">
                                                <div><span
                                                        class="text-xs font-bold text-slate-500 uppercase">Lokasi</span>
                                                    <p class="font-bold text-slate-800" x-text="ticket.plant"></p>
                                                </div>
                                                <div><span
                                                        class="text-xs font-bold text-slate-500 uppercase">Department</span>
                                                    <p class="font-bold text-slate-800" x-text="ticket.department">
                                                    </p>
                                                </div>
                                                <div><span
                                                        class="text-xs font-bold text-slate-500 uppercase">Bobot</span>
                                                    <p class="font-bold text-slate-800" x-text="ticket.category"></p>
                                                </div>
                                                <div><span
                                                        class="text-xs font-bold text-slate-500 uppercase">Target</span>
                                                    <p class="font-bold text-slate-800"
                                                        x-text="ticket.target_completion_date || '-'"></p>
                                                </div>
                                            </div>

                                            {{-- Uraian --}}
                                            <div class="bg-slate-50 p-4 border border-slate-200 mb-4">
                                                <span
                                                    class="text-xs font-bold text-slate-500 uppercase block mb-1">Uraian</span>
                                                <p class="text-slate-900 whitespace-pre-wrap"
                                                    x-text="ticket.description"></p>
                                            </div>

                                            {{-- ========================================== --}}
                                            {{-- BAGIAN FOTO BUKTI (BARU DITAMBAHKAN)       --}}
                                            {{-- ========================================== --}}
                                            <template x-if="ticket.photo_path">
                                                <div class="bg-slate-50 p-4 border border-slate-200 mb-4">
                                                    <div class="flex justify-between items-center mb-2">
                                                        <span class="text-xs font-bold text-slate-500 uppercase">Foto
                                                            Bukti</span>
                                                        <a :href="'/storage/' + ticket.photo_path" target="_blank"
                                                            class="text-xs font-bold text-blue-600 hover:underline uppercase flex items-center gap-1">
                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14">
                                                                </path>
                                                            </svg>
                                                            Lihat Full Size
                                                        </a>
                                                    </div>
                                                    <div class="bg-white p-2 border border-slate-300 rounded-sm">
                                                        <img :src="'/storage/' + ticket.photo_path" alt="Bukti Foto"
                                                            class="max-h-64 w-full object-contain mx-auto">
                                                    </div>
                                                </div>
                                            </template>
                                            {{-- ========================================== --}}

                                            {{-- History Log --}}
                                            <div class="border-t border-slate-200 pt-4">
                                                <h4 class="font-bold text-slate-900 uppercase mb-3 tracking-wide">
                                                    Riwayat Aktivitas</h4>
                                                <div class="space-y-3">
                                                    <template x-for="h in ticket.histories">
                                                        <div class="flex gap-3 text-sm">
                                                            <div class="font-mono text-xs font-bold text-slate-400"
                                                                x-text="new Date(h.created_at).toLocaleDateString('id-ID')">
                                                            </div>
                                                            <div>
                                                                <span class="font-bold text-slate-900"
                                                                    x-text="h.action"></span>
                                                                <span class="text-slate-600"
                                                                    x-text="'- ' + h.description"></span>
                                                            </div>
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

                {{-- MODAL 4: EDIT TICKET (ADMIN UPDATE) --}}
                {{-- MODAL 4: EDIT / APPROVAL TICKET --}}
                <template x-teleport="body">
                    <div x-show="showEditModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto">
                        {{-- BACKDROP: Opacity 75 agar tidak terlalu gelap pekat --}}
                        <div x-show="showEditModal"
                            class="fixed inset-0 bg-slate-900 bg-opacity-75 transition-opacity"
                            @click="showEditModal = false">
                        </div>

                        {{-- MODAL WRAPPER --}}
                        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0 relative z-10"
                            @click.self="showEditModal = false">
                            <div x-show="showEditModal"
                                class="relative transform overflow-hidden rounded-none bg-white text-left shadow-2xl border-t-8 border-yellow-400 transition-all sm:my-8 sm:w-full sm:max-w-lg">

                                {{-- HEADER (Hitam) --}}
                                <div class="bg-slate-900 px-6 py-4 flex justify-between items-center">
                                    <h3
                                        class="text-lg font-black text-white uppercase tracking-wider flex items-center gap-2">
                                        <span class="text-yellow-400">///</span>
                                        <span
                                            x-text="editForm.status == 'pending' ? 'Approval Tiket' : 'Update Status'"></span>
                                    </h3>
                                    <div class="text-xs font-mono text-slate-400 bg-slate-800 px-2 py-1 rounded"
                                        x-text="editForm.ticket_num"></div>
                                </div>

                                {{-- FORM CONTENT --}}
                                <form x-ref="editFormHtml" :action="'/ga/' + editForm.id + '/update-status'"
                                    method="POST">
                                    @csrf
                                    @method('PUT')

                                    <div class="px-6 py-6 space-y-5">

                                        {{-- KONDISI 1: PENDING (APPROVAL) --}}
                                        <template x-if="editForm.status == 'pending'">
                                            <div x-data="{ decision: null }">

                                                {{-- A. PILIHAN AWAL (Accept/Decline) --}}
                                                <div x-show="!decision" class="flex gap-4 justify-center py-2">
                                                    {{-- Tombol ACCEPT (Kuning Industrial) --}}
                                                    <button type="button" @click="decision = 'accept'"
                                                        class="flex-1 bg-yellow-400 text-slate-900 hover:bg-yellow-300 py-6 px-4 rounded-sm font-black uppercase tracking-wider flex flex-col items-center gap-3 shadow-sm transition hover:-translate-y-1 border-2 border-transparent hover:border-yellow-500">
                                                        <svg class="w-8 h-8" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                        </svg>
                                                        Accept (Terima)
                                                    </button>

                                                    {{-- Tombol DECLINE (Hitam/Merah) --}}
                                                    <button type="button" @click="decision = 'decline'"
                                                        class="flex-1 bg-slate-100 text-slate-600 hover:bg-slate-200 py-6 px-4 rounded-sm font-black uppercase tracking-wider flex flex-col items-center gap-3 border-2 border-slate-300 transition hover:text-red-600 hover:border-red-400">
                                                        <svg class="w-8 h-8" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                        </svg>
                                                        Decline (Tolak)
                                                    </button>

                                                </div>

                                                {{-- B. JIKA MEMILIH DECLINE --}}
                                                <div x-show="decision == 'decline'"
                                                    class="bg-red-50 p-5 rounded-sm border-l-4 border-red-500">
                                                    <h4 class="font-bold text-red-800 uppercase mb-2 text-sm">
                                                        Konfirmasi Penolakan</h4>
                                                    <p class="text-sm text-red-600 mb-4 font-medium">Tiket akan
                                                        dibatalkan secara permanen.</p>
                                                    <div class="flex gap-2">
                                                        <button type="submit" @click="confirmCancel()"
                                                            name="action" value="decline"
                                                            class="bg-red-600 text-white px-4 py-2 rounded-sm hover:bg-red-700 text-sm font-bold uppercase tracking-wide shadow-sm">
                                                            Ya, Tolak Tiket
                                                        </button>
                                                        <button type="button" @click="decision = null"
                                                            class="bg-white border-2 border-slate-300 text-slate-700 px-4 py-2 rounded-sm hover:bg-slate-100 text-sm font-bold uppercase tracking-wide">
                                                            Batal
                                                        </button>
                                                    </div>
                                                </div>

                                                {{-- C. JIKA MEMILIH ACCEPT --}}
                                                <div x-show="decision == 'accept'"
                                                    class="space-y-4 bg-yellow-50 p-5 rounded-sm border-l-4 border-yellow-400">
                                                    <h4
                                                        class="font-black text-slate-900 uppercase border-b border-yellow-200 pb-2 mb-3">
                                                        Konfirmasi Pengerjaan</h4>

                                                    <div>
                                                        <label
                                                            class="block text-xs font-bold text-slate-600 uppercase mb-1">Bobot
                                                            Pekerjaan</label>
                                                        <select name="category" x-model="editForm.category"
                                                            :disabled="decision !== 'accept'"
                                                            class="w-full border-2 border-slate-300 focus:border-slate-900 focus:ring-0 rounded-sm text-sm font-semibold">
                                                            <option value="RINGAN">Ringan</option>
                                                            <option value="SEDANG">Sedang</option>
                                                            <option value="BERAT">Berat</option>
                                                        </select>
                                                    </div>

                                                    <div>
                                                        <label
                                                            class="block text-xs font-bold text-slate-600 uppercase mb-1">Target
                                                            Penyelesaian</label>
                                                        <input type="text" name="target_date"
                                                            :disabled="decision !== 'accept'"
                                                            class="w-full border-2 border-slate-300 focus:border-slate-900 focus:ring-0 rounded-sm date-picker text-sm font-semibold"
                                                            placeholder="Pilih Tanggal..." x-init="flatpickr($el, { dateFormat: 'Y-m-d', minDate: 'today' })">
                                                    </div>

                                                    <div class="flex gap-2 mt-4 pt-2 border-t border-yellow-200">
                                                        <button type="submit" name="action" value="accept"
                                                            class="bg-slate-900 text-white px-5 py-2.5 rounded-sm hover:bg-slate-800 text-sm font-black uppercase tracking-wider shadow-md transition">
                                                            Simpan & Proses
                                                        </button>
                                                        <button type="button" @click="decision = null"
                                                            class="bg-white border-2 border-slate-300 text-slate-700 px-4 py-2.5 rounded-sm hover:bg-slate-50 text-sm font-bold uppercase tracking-wide transition">
                                                            Kembali
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </template>

                                        {{-- KONDISI 2: UPDATE STATUS (NON-PENDING) --}}
                                        <template x-if="editForm.status != 'pending'">
                                            <div class="space-y-5 bg-slate-50 p-5 border border-slate-200">
                                                <div>
                                                    <label
                                                        class="block text-xs font-bold text-slate-600 uppercase mb-1">Update
                                                        Status</label>
                                                    <select name="status" x-model="editForm.status"
                                                        class="w-full border-2 border-slate-300 focus:border-slate-900 focus:ring-0 rounded-sm text-sm font-bold">
                                                        <option value="in_progress">In Progress</option>
                                                        <option value="completed">Completed</option>
                                                        <option value="cancelled">Cancelled</option>
                                                    </select>
                                                </div>

                                                <div x-show="editForm.status == 'in_progress'">
                                                    <label
                                                        class="block text-xs font-bold text-blue-700 uppercase mb-1">Revisi
                                                        Target (Opsional)</label>
                                                    <input type="text" name="target_date"
                                                        x-model="editForm.target_date"
                                                        class="w-full border-2 border-blue-200 focus:border-blue-500 focus:ring-0 rounded-sm date-picker text-sm"
                                                        placeholder="Update Tanggal..." x-init="flatpickr($el, { dateFormat: 'Y-m-d', minDate: 'today' })">
                                                </div>

                                                <div class="pt-2 flex flex-row-reverse gap-2">
                                                    <button type="submit"
                                                        class="inline-flex justify-center rounded-sm bg-yellow-400 px-5 py-2 text-slate-900 font-black uppercase tracking-wider hover:bg-yellow-300 shadow-sm transition">
                                                        Simpan Update
                                                    </button>
                                                    <button type="button" @click="showEditModal = false"
                                                        class="inline-flex justify-center rounded-sm border-2 border-slate-300 bg-white px-4 py-2 text-slate-600 font-bold uppercase tracking-wide hover:bg-slate-50 transition">
                                                        Batal
                                                    </button>
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
                        @if ($errors->any())
                            console.warn('Form validation errors:', @json($errors->all()));
                        @endif
                    });
                </script>
            @endpush
            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
            <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

            <script>
                // Tunggu sampai halaman selesai loading
                document.addEventListener("DOMContentLoaded", function() {

                    // Cek apakah elemennya ada?
                    const pickerInput = document.getElementById("date_range_picker");

                    if (pickerInput) {
                        flatpickr(pickerInput, {
                            mode: "range",
                            dateFormat: "Y-m-d",
                            altInput: true,
                            altFormat: "j F Y",
                            // Ambil default date dari hidden input jika ada
                            defaultDate: [
                                "{{ request('start_date') }}",
                                "{{ request('end_date') }}"
                            ],
                            onChange: function(selectedDates, dateStr, instance) {
                                if (selectedDates.length === 2) {
                                    // Format ke YYYY-MM-DD manual agar zona waktu aman
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
                        console.log("Flatpickr berhasil di-init!"); // Cek Console browser (F12)
                    } else {
                        console.error("Input #date_range_picker tidak ditemukan!");
                    }
                });
            </script>
</x-app-layout>
