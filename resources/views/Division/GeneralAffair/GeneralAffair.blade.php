@section('browser_title', 'General Affair Work Order')
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight relative z-10">
            {{ __('General Affair Work Order') }}
        </h2>
    </x-slot>

    {{-- LOAD LIBRARY --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <div class="py-12" x-data="{
        // --- 1. MODAL STATES ---
        showDetailModal: false,
        showCreateModal: false,
        showConfirmModal: false,
        showEditModal: false,
        showExportModal: false,
    
    
        selected: [],
        pageIds: {{ Js::from($pageIds ?? []) }}, // Pastikan controller mengirim ini
    
        toggleSelectAll() {
            // Cek apakah semua ID di halaman ini sudah dicentang
            const allSelected = this.pageIds.every(id => this.selected.includes(id));
    
            if (allSelected) {
                // Uncheck semua yang ada di halaman ini
                this.selected = this.selected.filter(id => !this.pageIds.includes(id));
            } else {
                // Check semua yang ada di halaman ini
                this.pageIds.forEach(id => {
                    if (!this.selected.includes(id)) this.selected.push(id);
                });
            }
        },
    
        // --- 2. DATA HOLDER ---
        ticket: null,
    
        // --- 3. FORM VARIABLES ---
        currentDate: '',
        currentTime: '',
    
        // Form Create
        form: {
            plant: '',
            department: '',
            category: 'LOW',
            description: '',
            file_name: ''
        },
    
        // Form Edit (Admin)
        editForm: {
            id: '',
            ticket_num: '',
            status: '',
            photo_path: '',
            target_date: '',
            actual_date: ''
        },
    
        // ================= FUNCTIONS =================
    
        updateTime() {
            const now = new Date();
            // Format YYYY-MM-DD
            const year = now.getFullYear();
            const month = String(now.getMonth() + 1).padStart(2, '0');
            const day = String(now.getDate()).padStart(2, '0');
            this.currentDate = `${year}-${month}-${day}`;
    
            // Format HH:MM
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            this.currentTime = `${hours}:${minutes}`;
        },
    
        handleFile(event) {
            this.form.file_name = event.target.files[0] ? event.target.files[0].name : '';
        },
    
        submitForm() {
            // Manual trigger submit karena tombol ada di modal konfirmasi
            if (this.$refs.createForm.reportValidity()) {
                this.$refs.createForm.submit();
            } else {
                this.showConfirmModal = false;
            }
        },
    
        // --- OPEN EDIT MODAL (ADMIN) ---
        openEditModal(data) {
            console.log('Data yang diterima:', data);
            this.ticket = data;
            this.editForm.id = data.id;
            this.editForm.ticket_num = data.ticket_num;
            this.editForm.status = data.status;
            this.editForm.category = data.category;
            this.editForm.target_date = data.target_completion_date ? data.target_completion_date : '';
            this.editForm.photo_path = data.photo_path;
            this.showEditModal = true;
        },
    
        // --- INIT ---
        init() {
            this.updateTime();
            setInterval(() => { this.updateTime(); }, 60000);
    
            // Reset Form Create saat modal ditutup
            this.$watch('showCreateModal', (value) => {
                if (!value) {
                    this.form.plant = '';
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
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- A. ALERT SUCCESS --}}
            @if (session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative"
                    role="alert">
                    <strong class="font-bold">Berhasil!</strong>
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            {{-- B. STATISTIK CARDS --}}
            {{-- B. STATISTIK CARDS (INTERACTIVE) --}}
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6" x-data="{ show: false }" x-init="setTimeout(() => show = true, 100)">

                {{-- Card Total --}}
                <div x-show="show" x-transition:enter="transition ease-out duration-500"
                    x-transition:enter-start="opacity-0 translate-y-4"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    class="relative group bg-white overflow-hidden shadow-sm sm:rounded-xl p-6 border border-indigo-100 
                           transition-all duration-300 hover:shadow-xl hover:-translate-y-1 hover:border-indigo-300 cursor-default">

                    {{-- Background Glow Effect saat Hover --}}
                    <div
                        class="absolute -right-6 -top-6 w-24 h-24 bg-indigo-50 rounded-full group-hover:bg-indigo-100 transition-all duration-500">
                    </div>

                    <div class="relative flex items-center justify-between z-10">
                        <div>
                            <div class="text-sm font-semibold text-indigo-500 mb-1 tracking-wide uppercase">Total Tiket
                            </div>
                            <div
                                class="text-3xl font-extrabold text-slate-800 group-hover:text-indigo-600 transition-colors">
                                {{ $workOrders->total() }}
                            </div>
                        </div>
                        <div
                            class="p-3 bg-indigo-50 rounded-lg text-indigo-600 group-hover:scale-110 group-hover:bg-indigo-600 group-hover:text-white transition-all duration-300 shadow-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                                stroke="currentColor" class="w-6 h-6">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M16.5 6v.75m0 3v.75m0 3v.75m0 3V18m-9-5.25h5.25M7.5 15h3M3.375 5.25c-.621 0-1.125.504-1.125 1.125v3.026a2.999 2.999 0 010 5.198v3.026c0 .621.504 1.125 1.125 1.125h17.25c.621 0 1.125-.504 1.125-1.125v-3.026a2.999 2.999 0 010-5.198V6.375c0-.621-.504-1.125-1.125-1.125H3.375z" />
                            </svg>
                        </div>
                    </div>
                </div>

                {{-- Card Pending --}}
                <div x-show="show" x-transition:enter="transition ease-out duration-500 delay-100"
                    x-transition:enter-start="opacity-0 translate-y-4"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    class="relative group bg-white overflow-hidden shadow-sm sm:rounded-xl p-6 border border-amber-100 
                           transition-all duration-300 hover:shadow-xl hover:-translate-y-1 hover:border-amber-300 cursor-default">

                    <div
                        class="absolute -right-6 -top-6 w-24 h-24 bg-amber-50 rounded-full group-hover:bg-amber-100 transition-all duration-500">
                    </div>

                    <div class="relative flex items-center justify-between z-10">
                        <div>
                            <div class="text-sm font-semibold text-amber-500 mb-1 tracking-wide uppercase">Pending</div>
                            <div
                                class="text-3xl font-extrabold text-slate-800 group-hover:text-amber-600 transition-colors">
                                {{ \App\Models\Engineering\WorkOrderEngineering::where('improvement_status', 'pending')->count() }}
                            </div>
                        </div>
                        <div
                            class="p-3 bg-amber-50 rounded-lg text-amber-600 group-hover:scale-110 group-hover:bg-amber-500 group-hover:text-white transition-all duration-300 shadow-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                                stroke="currentColor" class="w-6 h-6">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                </div>
                <div x-show="show" x-transition:enter="transition ease-out duration-500 delay-200"
                    x-transition:enter-start="opacity-0 translate-y-4"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    class="relative group bg-white overflow-hidden shadow-sm sm:rounded-xl p-6 border border-indigo-100 
            transition-all duration-300 hover:shadow-xl hover:-translate-y-1 hover:border-indigo-300 cursor-default">

                    {{-- Background Glow Effect --}}
                    <div
                        class="absolute -right-6 -top-6 w-24 h-24 bg-indigo-50 rounded-full group-hover:bg-indigo-100 transition-all duration-500">
                    </div>

                    <div class="relative flex items-center justify-between z-10">
                        <div>
                            <div class="text-sm font-semibold text-indigo-500 mb-1 tracking-wide uppercase">In Progress
                            </div>
                            <div
                                class="text-3xl font-extrabold text-slate-800 group-hover:text-indigo-600 transition-colors">
                                {{ \App\Models\GeneralAffair\WorkOrderGeneralAffair::where('status', 'in_progress')->count() }}
                            </div>
                        </div>

                        {{-- Icon dengan efek interaktif --}}
                        <div
                            class="p-3 bg-indigo-50 rounded-lg text-indigo-600 group-hover:scale-110 group-hover:bg-indigo-600 group-hover:text-white transition-all duration-300 shadow-sm">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z">
                                </path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z">
                                </path>
                            </svg>
                        </div>
                    </div>
                </div>
                {{-- Card Selesai --}}
                <div x-show="show" x-transition:enter="transition ease-out duration-500 delay-200"
                    x-transition:enter-start="opacity-0 translate-y-4"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    class="relative group bg-white overflow-hidden shadow-sm sm:rounded-xl p-6 border border-emerald-100 
                           transition-all duration-300 hover:shadow-xl hover:-translate-y-1 hover:border-emerald-300 cursor-default">

                    <div
                        class="absolute -right-6 -top-6 w-24 h-24 bg-emerald-50 rounded-full group-hover:bg-emerald-100 transition-all duration-500">
                    </div>

                    <div class="relative flex items-center justify-between z-10">
                        <div>
                            <div class="text-sm font-semibold text-emerald-500 mb-1 tracking-wide uppercase">Selesai
                            </div>
                            <div
                                class="text-3xl font-extrabold text-slate-800 group-hover:text-emerald-600 transition-colors">
                                {{ \App\Models\Engineering\WorkOrderEngineering::where('improvement_status', 'completed')->count() }}
                            </div>
                        </div>
                        <div
                            class="p-3 bg-emerald-50 rounded-lg text-emerald-600 group-hover:scale-110 group-hover:bg-emerald-500 group-hover:text-white transition-all duration-300 shadow-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                                stroke="currentColor" class="w-6 h-6">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
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
                    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center mb-6 gap-4">

                        <div class="w-full lg:flex-1 bg-white p-4 rounded-lg shadow-sm border border-gray-200">
                            <form id="filterForm" action="{{ route('ga.index') }}" method="GET"
                                class="flex flex-col md:flex-row gap-4 md:items-end">

                                <div class="w-full md:flex-1">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Cari Lokasi /
                                        Dept</label>
                                    <div class="relative">
                                        <div
                                            class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                            </svg>
                                        </div>
                                        <input type="text" name="search" value="{{ request('search') }}"
                                            class="pl-10 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                            placeholder="Tiket, Plant, Dept, Uraian...">
                                    </div>
                                </div>

                                <div class="w-full md:w-48">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                                    <select name="status"
                                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                        <option value="">Semua</option>
                                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>
                                            Pending</option>
                                        <option value="in_progress"
                                            {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress
                                        </option>
                                        <option value="completed"
                                            {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                                        <option value="cancelled"
                                            {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                    </select>
                                </div>

                                <input type="hidden" name="selected_ids" :value="selected.join(',')">

                                <div class="flex flex-col md:flex-row gap-2 w-full md:w-auto">
                                    <button type="submit"
                                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium transition shadow-sm">Filter</button>
                                    <a href="{{ route('ga.index') }}"
                                        class="text-center bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-md text-sm font-medium border border-gray-300 transition">Reset</a>

                                    {{-- EXPORT BUTTON (Dynamic Text) --}}
                                    <button type="submit" formaction="{{ route('ga.export') }}"
                                        {{-- Jika ada yang dipilih, kirim input hidden 'selected_ids' (lewat JS handleExportClick atau hidden input manual) --}}
                                        class="group relative overflow-hidden bg-emerald-600 hover:bg-emerald-500 text-white font-bold py-2.5 px-5 rounded-lg text-sm transition-all duration-200 shadow-md hover:shadow-lg focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 active:scale-95 transform w-full md:w-auto flex items-center justify-center gap-2">
                                        <div
                                            class="absolute inset-0 bg-white/20 translate-x-[-100%] group-hover:translate-x-[100%] transition-transform duration-700">
                                        </div>
                                        <svg class="w-5 h-5 transition-transform group-hover:-translate-y-0.5"
                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                            </path>
                                        </svg>

                                        {{-- Logic Teks Tombol --}}
                                        <span
                                            x-text="selectedTickets.length > 0 ? 'Export (' + selectedTickets.length + ') Terpilih' : 'Export Data'"></span>

                                        {{-- Input Hidden untuk Selected IDs (Agar terkirim saat klik tombol ini) --}}
                                        <input type="hidden" name="selected_ids" :value="selected.join(',')">
                                    </button>
                                </div>
                            </form>
                        </div>

                        <div class="w-full lg:w-auto">
                            <button @click="showCreateModal = true" type="button"
                                class="group relative overflow-hidden bg-indigo-600 hover:bg-indigo-500 text-white font-bold py-2.5 px-5 rounded-lg text-sm transition-all duration-200 shadow-md hover:shadow-lg focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 active:scale-95 transform w-full md:w-auto flex items-center justify-center gap-2">
                                <div
                                    class="absolute inset-0 bg-white/20 translate-x-[-100%] group-hover:translate-x-[100%] transition-transform duration-700">
                                </div>
                                <svg class="w-5 h-5 transition-transform group-hover:rotate-90" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4v16m8-8H4"></path>
                                </svg>
                                Buat Ticket GA
                            </button>
                        </div>

                    </div>

                    {{-- Tabel --}}
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-200">
                            {{-- HEAD --}}
                            <thead class="bg-slate-50">
                                <tr>
                                    {{-- Kolom 1: Checkbox Select All --}}
                                    <th scope="col" class="px-6 py-3 w-10 text-left">
                                        <input type="checkbox" @change="toggleSelectAll()"
                                            :checked="pageIds.length > 0 && pageIds.every(id => selected.includes(id))"
                                            class="rounded border-slate-300 text-blue-600 focus:ring-blue-500 cursor-pointer">
                                    </th>
                                    {{-- Kolom 2-8: Judul --}}
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">
                                        Tiket / Tanggal</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">
                                        Lokasi / Dept</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">
                                        Kategori</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">
                                        Uraian</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">
                                        Open / Rencana</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">
                                        Status</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">
                                        Aksi</th>
                                </tr>
                            </thead>

                            <tbody class="bg-white divide-y divide-slate-200">
                                @forelse ($workOrders as $item)
                                    <tr class="hover:bg-slate-50 transition-colors"
                                        :class="selected.includes({{ $item->id }}) ? 'bg-blue-50' : ''">

                                        {{-- Kolom 1: Checkbox Row --}}
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <input type="checkbox" value="{{ $item->id }}" x-model="selected"
                                                class="rounded border-slate-300 text-blue-600 focus:ring-blue-500 cursor-pointer" />
                                        </td>

                                        {{-- Kolom 2: Tiket --}}
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-bold text-blue-600 font-mono">
                                                {{ $item->ticket_num }}</div>
                                            <div class="text-xs text-slate-500">
                                                {{ $item->created_at->format('d M Y') }}</div>
                                        </td>

                                        {{-- Kolom 3: Lokasi --}}
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            {{-- Container Flex Column agar badge tersusun atas-bawah --}}
                                            <div class="flex flex-col items-start gap-1">

                                                {{-- Badge Plant (Hanya muncul jika ada data plant) --}}
                                                @if ($item->plant)
                                                    <span
                                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 border border-gray-200">
                                                        Plant: {{ $item->plant }}
                                                    </span>
                                                @endif

                                                {{-- Badge Department (Hanya muncul jika ada data department) --}}
                                                @if ($item->department)
                                                    <span
                                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800 border border-purple-200">
                                                        Dept: {{ $item->department }}
                                                    </span>
                                                @endif

                                                {{-- Fallback: Tampilkan strip jika keduanya kosong (opsional) --}}
                                                @if (!$item->plant && !$item->department)
                                                    <span class="text-xs text-gray-400 italic">-</span>
                                                @endif

                                            </div>
                                        </td>

                                        {{-- Kolom 4: Kategori --}}
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span
                                                class="px-2 py-1 text-xs font-bold rounded {{ $item->category == 'HIGH' ? 'bg-red-100 text-red-800' : ($item->category == 'MEDIUM' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800') }}">
                                                {{ $item->category }}
                                            </span>
                                        </td>

                                        {{-- Kolom 5: Uraian --}}
                                        <td class="px-6 py-4">
                                            <div class="text-sm text-slate-700 truncate w-48">
                                                {{ Str::limit($item->description, 50) }}</div>
                                        </td>

                                        {{-- Kolom 6: Status Permintaan --}}
                                        <td class="px-6 py-4">
                                            <div class="text-sm text-slate-700">
                                                {{ Str::limit($item->status_permintaan) }}</div>
                                        </td>

                                        {{-- Kolom 7: Status --}}
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span
                                                class="px-3 py-1 inline-flex text-xs leading-5 font-bold rounded-full 
                            {{ $item->status == 'completed'
                                ? 'bg-emerald-100 text-emerald-800'
                                : ($item->status == 'pending'
                                    ? 'bg-amber-100 text-amber-800'
                                    : ($item->status == 'cancelled'
                                        ? 'bg-rose-100 text-rose-800'
                                        : 'bg-blue-100 text-blue-800')) }}">
                                                {{ strtoupper(str_replace('_', ' ', $item->status)) }}
                                            </span>
                                        </td>

                                        {{-- Kolom 8: Aksi --}}
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <button type="button"
                                                @click='ticket = @json($item); ticket.user_name = "{{ $item->user->name ?? 'User' }}"; showDetailModal=true'
                                                class="text-blue-600 hover:text-blue-900 mr-3">Detail</button>
                                            @if (in_array(auth()->user()->role, ['ga.admin']))
                                                <button type="button"
                                                    @click='openEditModal(@json($item))'
                                                    class="text-slate-600 hover:text-slate-900 font-bold">Update</button>
                                            @endif
                                        </td>
                                    </tr>
                                @empty

                                    <tr>
                                        <td colspan="8" class="px-6 py-12 text-center">
                                            <div class="flex flex-col justify-center items-center">
                                                <svg class="w-12 h-12 text-slate-300 mb-3" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                                </svg>
                                                <span class="text-slate-500 font-medium">Data tidak ditemukan</span>
                                                <p class="text-slate-400 text-sm mt-1">Coba kata kunci lain atau reset
                                                    filter.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">{{ $workOrders->links() }}</div>
                </div>
            </div>
        </div>

        {{-- MODAL 1: CREATE TICKET (GA) --}}
        <template x-teleport="body">
            <div x-show="showCreateModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto"
                role="dialog" aria-modal="true">
                <div x-show="showCreateModal" x-transition.opacity
                    class="fixed inset-0 bg-slate-500 bg-opacity-75 transition-opacity"
                    @click="showCreateModal = false">
                </div>
                <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                    <div x-show="showCreateModal" x-transition:enter="ease-out duration-300"
                        x-transition:enter-start="opacity-0 translate-y-4 sm:scale-95"
                        x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                        class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-2xl">

                        <div class="bg-white px-4 py-4 sm:px-6 flex justify-between items-center border-b">
                            <h3 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                                Form Permintaan General Affair
                            </h3>
                            <button @click="showCreateModal = false" class="text-slate-400 hover:text-slate-500"><svg
                                    class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"></path>
                                </svg></button>
                        </div>
                        {{-- Tampilkan semua error validasi --}}
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <form x-ref="createForm" action="{{ route('ga.store') }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="px-4 py-5 sm:p-6 space-y-6">

                                {{-- Row 1: Info Dasar --}}
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-semibold text-slate-700 mb-1">Tanggal</label>
                                        <input type="text" x-model="currentDate" readonly
                                            class="w-full rounded-md border-slate-300 bg-slate-100 text-slate-500 cursor-not-allowed">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-slate-700 mb-1">Jam</label>
                                        <input type="text" x-model="currentTime" readonly
                                            class="w-full rounded-md border-slate-300 bg-slate-100 text-slate-500 cursor-not-allowed">
                                    </div>
                                </div>

                                {{-- Row 2: Lokasi (Plant OR Dept) --}}
                                <div class="bg-blue-50 p-4 rounded-md border border-blue-100">
                                    <label class="block text-sm font-bold text-blue-800 mb-2">Lokasi Pekerjaan <span
                                            class="text-red-500">*Wajib</span></label>
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="text-xs text-slate-600 block mb-1">Plant</label>
                                            <select name="plant_id"
                                                class="w-full rounded-md border-slate-300 focus:ring-blue-500 focus:border-blue-500"
                                                required>
                                                <option value="">-- Pilih Plant --</option>

                                                @foreach ($plants as $plant)
                                                    <option value="{{ $plant->id }}">{{ $plant->name }}</option>
                                                @endforeach

                                            </select>
                                        </div>
                                        <div>
                                            <label class="text-xs text-slate-600 block mb-1">Department</label>
                                            <select name="department" x-model="form.department"
                                                class="w-full rounded-md border-slate-300 focus:ring-blue-500 focus:border-blue-500"
                                                required">
                                                <option value="">-- Pilih Dept --</option>
                                                <option value="IT">IT</option>
                                                <option value="HR">HR</option>
                                                <option value="Finance">Finance</option>
                                                <option value="Facility">Facility</option>
                                                <option value="Production">Production</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                {{-- Row 3: Detail --}}
                                <div class="grid grid-cols-2 gap-4">

                                    <div>
                                        <label class="block text-sm font-semibold text-slate-700 mb-1">Kategori
                                            Prioritas</label>
                                        <select name="category" x-model="form.category"
                                            class="w-full rounded-md border-slate-300 focus:ring-blue-500 focus:border-blue-500">
                                            <option value="LOW">LOW (Biasa)</option>
                                            <option value="MEDIUM">MEDIUM (Penting)</option>
                                            <option value="HIGH">HIGH (Darurat)</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-slate-700 mb-1">Parameter
                                            Permintaan <span class="text-red-500">*Wajib</span></label>
                                        <select name="parameter_permintaan" x-model="form.category"
                                            class="w-full rounded-md border-slate-300 focus:ring-blue-500 focus:border-blue-500"
                                            required>
                                            <option>-- Pilih Parameter --</option>
                                            <option value="KEBERSIHAN">Kebersihan</option>
                                            <option value="PEMELIHARAAN">Pemeliharaan</option>
                                            <option value="PERBAIKAN">Perbaikan</option>
                                            <option value="PEMBUATAN_BARU">Pembuatan Baru</option>
                                        </select>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 mb-1">Status
                                        Permintaan</label>
                                    <select name="status_permintaan" x-model="form.status_permintaan"
                                        class="w-full rounded-md border-slate-300 focus:ring-blue-500 focus:border-blue-500">
                                        <option value="">-- Open atau Sudah Direncanakan --</option>
                                        <option value="OPEN">Open</option>
                                        <option value="SUDAH_DIRENCANAKAN">Sudah Direncanakan</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 mb-1">Uraian
                                        Permintaan <span class="text-red-500">*Wajib</span></label>
                                    <textarea name="description" x-model="form.description" rows="3"
                                        class="w-full rounded-md border-slate-300 focus:ring-blue-500 focus:border-blue-500"
                                        placeholder="Jelaskan detail pekerjaan..." required></textarea>
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 mb-1">Foto Bukti <span
                                            class="text-red-500">*Wajib</span></label>
                                    <input type="file" name="photo" @change="handleFile" required
                                        class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                                    <p class="text-xs text-slate-500 mt-1">Format: JPG, PNG, JPEG. Max: 5MB.</p>
                                </div>

                            </div>
                            <div class="px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse bg-slate-50 rounded-b-lg gap-2">
                                <button type="button" @click="showConfirmModal = true"
                                    class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none sm:w-auto sm:text-sm">Submit
                                    Ticket</button>
                                <button type="button" @click="showCreateModal = false"
                                    class="mt-3 w-full inline-flex justify-center rounded-md border border-slate-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-slate-700 hover:bg-slate-50 focus:outline-none sm:mt-0 sm:w-auto sm:text-sm">Batal</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </template>

        {{-- MODAL 2: CONFIRMATION --}}
        <template x-teleport="body">
            <div x-show="showConfirmModal" style="display: none;" class="fixed inset-0 z-[60] overflow-y-auto">
                <div x-show="showConfirmModal" class="fixed inset-0 bg-slate-900 bg-opacity-90 transition-opacity"
                    @click="showConfirmModal = false"></div>
                <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                    <div x-show="showConfirmModal"
                        class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg border-2 border-blue-500">
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6">
                            <h3 class="text-lg font-semibold text-slate-900 mb-4">Konfirmasi Permintaan GA</h3>
                            <div class="space-y-2 text-sm text-slate-600 bg-slate-50 p-4 rounded">
                                <p><span class="font-bold">Lokasi:</span> <span
                                        x-text="form.plant ? 'Plant ' + form.plant : 'Dept ' + form.department"></span>
                                </p>
                                <p><span class="font-bold">Kategori:</span> <span x-text="form.category"></span></p>
                                <p><span class="font-bold">Uraian:</span> <span x-text="form.description"></span></p>
                                <p><span class="font-bold">File:</span> <span x-text="form.file_name"></span></p>
                            </div>
                        </div>
                        <div class="bg-slate-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6 gap-2">
                            <button type="button" @click="submitForm()"
                                class="inline-flex w-full justify-center rounded-md bg-blue-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 sm:w-auto">Ya,
                                Kirim</button>
                            <button type="button" @click="showConfirmModal = false"
                                class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-slate-900 shadow-sm ring-1 ring-inset ring-slate-300 hover:bg-slate-50 sm:mt-0 sm:w-auto">Periksa
                                Lagi</button>
                        </div>
                    </div>
                </div>
            </div>
        </template>

        {{-- MODAL 3: DETAIL TICKET --}}
        <template x-teleport="body">
            <div x-show="showDetailModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto">
                <div x-show="showDetailModal" class="fixed inset-0 bg-slate-500 bg-opacity-75 transition-opacity"
                    @click="showDetailModal = false"></div>
                <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                    <div x-show="showDetailModal"
                        class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-3xl">
                        <div class="bg-slate-50 px-4 py-3 border-b flex justify-between items-center">
                            <h3 class="text-base font-semibold text-slate-900">Detail Ticket GA</h3>
                            <button @click="showDetailModal = false"
                                class="text-slate-400 hover:text-slate-500">&times;</button>
                        </div>
                        <div class="bg-white px-6 py-6 max-h-[80vh] overflow-y-auto">
                            <template x-if="ticket">
                                <div class="space-y-6">
                                    <div class="flex justify-between items-start border-b pb-4">
                                        <div>
                                            <span class="text-xs font-bold text-slate-500 uppercase">Nomor Tiket</span>
                                            <p class="text-2xl font-bold text-blue-600 font-mono mt-1"
                                                x-text="ticket.ticket_num"></p>
                                        </div>
                                        <div class="text-right">
                                            <span class="text-xs font-bold text-slate-500 uppercase">Status</span>
                                            <div class="mt-1">
                                                <span
                                                    class="px-3 py-1 text-sm font-semibold rounded-full bg-blue-100 text-blue-800"
                                                    x-text="ticket.status.toUpperCase().replace('_', ' ')"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <span class="text-xs text-slate-500 block mb-1">User Pelapor</span>
                                            <p class="font-medium text-slate-900" x-text="ticket.user_name"></p>
                                        </div>
                                        <div>
                                            <span class="text-xs text-slate-500 block mb-1">Lokasi</span>
                                            <p class="font-medium text-slate-900"
                                                x-text="ticket.plant ? 'Plant: ' + ticket.plant : 'Dept: ' + ticket.department">
                                            </p>
                                        </div>
                                        <div>
                                            <span class="text-xs text-slate-500 block mb-1">Kategori</span>
                                            <p class="font-medium text-slate-900" x-text="ticket.category"></p>
                                        </div>
                                        <div>
                                            <span class="text-xs text-slate-500 block mb-1">Target Selesai</span>
                                            <p class="font-medium text-slate-900"
                                                x-text="ticket.target_completion_date ? ticket.target_completion_date : '-'">
                                            </p>
                                        </div>
                                    </div>
                                    <div class="bg-slate-50 p-4 rounded border">
                                        <span class="text-xs font-bold text-slate-500 uppercase block mb-2">Uraian
                                            Permintaan</span>
                                        <p class="text-slate-800 whitespace-pre-wrap" x-text="ticket.description"></p>
                                    </div>
                                    <template x-if="ticket.photo_path">
                                        <div class="mb-4 p-3 bg-gray-50 border rounded-lg text-center">
                                            <span class="text-xs font-bold text-slate-500 uppercase block mb-2">Foto
                                                Bukti</span>
                                            <img :src="'/storage/' + ticket.photo_path" alt="Bukti Foto"
                                                class="mx-auto max-h-64 object-contain rounded border border-gray-200">
                                        </div>
                                    </template>
                                </div>
                            </template>
                        </div>
                        <div class="bg-slate-50 px-4 py-3 sm:flex sm:flex-row-reverse">
                            <button @click="showDetailModal = false"
                                class="w-full inline-flex justify-center rounded-md border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50 sm:w-auto">Tutup</button>
                        </div>
                    </div>
                </div>
            </div>
        </template>

        {{-- MODAL 4: EDIT TICKET (ADMIN UPDATE) --}}
        {{-- MODAL 4: EDIT / APPROVAL TICKET --}}
        <template x-teleport="body">
            <div x-show="showEditModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto">
                <div x-show="showEditModal" class="fixed inset-0 bg-slate-500 bg-opacity-75 transition-opacity"
                    @click="showEditModal = false"></div>
                <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                    <div x-show="showEditModal"
                        class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">

                        {{-- Header --}}
                        <div class="bg-white px-4 py-4 border-b">
                            <h3 class="text-lg font-bold text-slate-900"
                                x-text="editForm.status == 'pending' ? 'Approval Tiket: ' + editForm.ticket_num : 'Update Status: ' + editForm.ticket_num">
                            </h3>
                        </div>

                        <form :action="'/ga/' + editForm.id + '/update-status'" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="px-6 py-6 space-y-4">
                                <template x-if="editForm.status == 'pending'">
                                    <div x-data="{ decision: null }">

                                        {{-- Tombol Pilihan Awal (Tidak Berubah) --}}
                                        <div x-show="!decision" class="flex gap-4 justify-center py-4">
                                            <button type="button" @click="decision = 'accept'"
                                                class="flex-1 bg-emerald-100 text-emerald-700 hover:bg-emerald-200 py-4 rounded-lg font-bold flex flex-col items-center gap-2 border border-emerald-300 transition-all">
                                                <svg class="w-8 h-8" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                </svg>
                                                Accept (Terima)
                                            </button>
                                            <button type="button" @click="decision = 'decline'"
                                                class="flex-1 bg-rose-100 text-rose-700 hover:bg-rose-200 py-4 rounded-lg font-bold flex flex-col items-center gap-2 border border-rose-300 transition-all">

                                                <svg class="w-8 h-8" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                </svg>
                                                Decline (Tolak)
                                            </button>
                                        </div>

                                        {{-- Jika Memilih DECLINE --}}
                                        <div x-show="decision == 'decline'"
                                            class="bg-rose-50 p-4 rounded border border-rose-200">
                                            <p class="text-rose-800 font-semibold mb-2">Anda yakin ingin menolak tiket
                                                ini?</p>
                                            <p class="text-sm text-rose-600 mb-4">Status akan berubah menjadi
                                                Cancelled/Declined.</p>



                                            <div class="flex gap-2">

                                                <button type="submit" name="action" value="decline"
                                                    class="bg-rose-600 text-white px-4 py-2 rounded hover:bg-rose-700 text-sm font-bold">
                                                    Ya, Tolak Tiket
                                                </button>

                                                <button type="button" @click="decision = null"
                                                    class="bg-white border border-slate-300 text-slate-700 px-4 py-2 rounded hover:bg-slate-50 text-sm">Kembali</button>
                                            </div>
                                        </div>


                                        <div x-show="decision == 'accept'"
                                            class="space-y-4 bg-emerald-50 p-4 rounded border border-emerald-200">



                                            <h4 class="font-bold text-emerald-800 border-b border-emerald-200 pb-2">
                                                Konfirmasi Pengerjaan</h4>

                                            {{-- 1. Kategori --}}
                                            <div>
                                                <label class="block text-sm font-medium text-slate-700 mb-1">Kategori
                                                    Prioritas</label>

                                                <select name="category" x-model="editForm.category"
                                                    :disabled="decision !== 'accept'"
                                                    class="w-full rounded-md border-slate-300 focus:ring-emerald-500 text-sm">
                                                    <option value="LOW">LOW (Biasa)</option>
                                                    <option value="MEDIUM">MEDIUM (Penting)</option>
                                                    <option value="HIGH">HIGH (Darurat)</option>
                                                </select>
                                            </div>

                                            {{-- 2. Target Penyelesaian --}}
                                            <div>
                                                <label class="block text-sm font-medium text-slate-700 mb-1">Target
                                                    Penyelesaian</label>

                                                <input type="text" name="target_date"
                                                    :disabled="decision !== 'accept'"
                                                    class="w-full rounded-md border-slate-300 focus:ring-emerald-500 date-picker text-sm"
                                                    placeholder="Pilih Tanggal..." x-init="flatpickr($el, { dateFormat: 'Y-m-d', minDate: 'today' })">
                                            </div>

                                            <div class="flex gap-2 mt-4">

                                                <button type="submit" name="action" value="accept"
                                                    class="bg-emerald-600 text-white px-4 py-2 rounded hover:bg-emerald-700 text-sm font-bold">
                                                    Simpan & Proses
                                                </button>

                                                <button type="button" @click="decision = null"
                                                    class="bg-white border border-slate-300 text-slate-700 px-4 py-2 rounded hover:bg-slate-50 text-sm">Kembali</button>
                                            </div>
                                        </div>
                                    </div>
                                </template>



                                <template x-if="editForm.status != 'pending'">
                                    <div class="space-y-4">
                                        <div>
                                            <label class="block text-sm font-medium text-slate-700 mb-1">Update
                                                Status</label>
                                            <select name="status" x-model="editForm.status"
                                                class="w-full rounded-md border-slate-300 focus:ring-blue-500">
                                                <option value="in_progress">In Progress</option>
                                                <option value="completed">Completed</option>
                                                <option value="cancelled">Cancelled</option>
                                            </select>
                                        </div>


                                        <div x-show="editForm.status == 'in_progress'">
                                            <label class="block text-sm font-medium text-blue-700 mb-1">Revisi Target
                                                (Opsional)</label>
                                            <input type="text" name="target_date" x-model="editForm.target_date"
                                                class="w-full rounded-md border-slate-300 focus:ring-blue-500 date-picker"
                                                placeholder="Update Tanggal..." x-init="flatpickr($el, { dateFormat: 'Y-m-d', minDate: 'today' })">
                                        </div>

                                        <div class="pt-4 flex flex-row-reverse gap-2">
                                            <button type="submit"
                                                class="inline-flex justify-center rounded-md bg-blue-600 px-4 py-2 text-white font-medium hover:bg-blue-700 sm:w-auto">Simpan
                                                Update</button>
                                            <button type="button" @click="showEditModal = false"
                                                class="inline-flex justify-center rounded-md border border-slate-300 bg-white px-4 py-2 text-slate-700 hover:bg-slate-50 sm:w-auto">Batal</button>
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
    @push('scripts')
        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.data('gaForm', () => ({
                    // Helper untuk inisialisasi Flatpickr pada input tanggal
                    initFlatpickr() {
                        flatpickr(".date-picker", {
                            dateFormat: "Y-m-d",
                            minDate: "today",
                            allowInput: true
                        });
                    }
                }));
            });

            // Event Listener Global (Opsional: Untuk debugging atau alert tambahan)
            document.addEventListener('DOMContentLoaded', function() {
                // Cek jika ada session error dari Laravel controller
                @if ($errors->any())
                    console.warn('Form validation errors:', @json($errors->all()));
                @endif
            });
        </script>
    @endpush
</x-app-layout>
