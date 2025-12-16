@section('browser_title', 'Facilities Work Order')

<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center -my-2">
            <h2 class="font-bold text-2xl text-[#1E3A5F] leading-tight uppercase tracking-wider flex items-center gap-4">
                {{-- Corporate Accent Bar --}}
                <span class="w-4 h-8 bg-[#1E3A5F] inline-block shadow-sm"></span>
                {{ __('Facilities Request Order') }}
            </h2>
        </div>
    </x-slot>

    {{-- LIBRARIES --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <div class="py-12 bg-[#F8FAFC] min-h-screen font-sans" x-data="{
        // --- STATES MODAL ---
        showCreateModal: false,
        showEditModal: false,
        showDetailModal: false,
        ticket: null,
    
        // --- FORM DATA (CREATE) ---
        form: {
            plant_id: '',
            machine_id: '',
            category: '',
            description: '',
            photo: null
        },
    
        // --- [FIX] FORM DATA (EDIT) ---
        // Ini yang sebelumnya hilang
        editForm: {
            id: '',
            status: '',
            target_date: ''
        },
    
        // --- DATA DARI DATABASE ---
        machinesData: {{ Js::from($machines) }},
        filteredMachines: [],
    
        // --- LOGIKA WAKTU ---
        currentDate: '',
        currentDateDB: '',
        currentTime: '',
        currentShift: '',
    
        init() {
            this.updateTime();
            setInterval(() => this.updateTime(), 1000);
        },
    
        updateTime() {
            const now = new Date();
    
            // 1. Format Tampilan (Bahasa Indonesia)
            this.currentDate = now.toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric' });
    
            // 2. [BARU] Format Database (YYYY-MM-DD)
            // Kita buat manual agar sesuai Timezone lokal user (bukan UTC)
            const year = now.getFullYear();
            const month = String(now.getMonth() + 1).padStart(2, '0'); // Tambah 0 jika angka tunggal
            const day = String(now.getDate()).padStart(2, '0');
            this.currentDateDB = `${year}-${month}-${day}`;
    
            // 3. Jam
            this.currentTime = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', hour12: false });
    
            // 4. Shift
            const hour = now.getHours();
            if (hour >= 7 && hour < 15) {
                this.currentShift = '1 (Pagi)';
            } else if (hour >= 15 && hour < 23) {
                this.currentShift = '2 (Sore)';
            } else {
                this.currentShift = '3 (Malam)';
            }
        },
    
        filterMachines() {
            this.form.machine_id = '';
            this.filteredMachines = this.machinesData.filter(m => m.plant_id == this.form.plant_id);
        },
    
        needsMachine() {
            const categoriesRequiringMachine = [
                'Modifikasi Mesin',
                'Pemasangan Mesin',
                'Pembongkaran Mesin',
                'Relokasi Mesin',
                'Perbaikan'
            ];
            return categoriesRequiringMachine.includes(this.form.category);
        }
    }">

        {{-- FLASH MESSAGE HANDLING --}}
        @if (session('success'))
            <script>
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: '{{ session('success') }}',
                    confirmButtonColor: '#1E3A5F'
                })
            </script>
        @endif

        <div class="max-w-8xl mx-auto sm:px-6 lg:px-8">

            {{-- A. STATS CARDS (Corporate Style) --}}
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">

                {{-- Total --}}
                <div class="bg-white rounded-sm shadow-sm p-6 border-l-4 border-[#1E3A5F]">
                    <p class="text-xs font-bold text-[#64748B] uppercase tracking-widest mb-1">Total Requests</p>
                    <p class="text-4xl font-bold text-[#1E3A5F]">{{ $countTotal }}</p>
                </div>

                {{-- Pending --}}
                <div class="bg-white rounded-sm shadow-sm p-6 border-l-4 border-gray-400">
                    <p class="text-xs font-bold text-[#64748B] uppercase tracking-widest mb-1">Pending</p>
                    <p class="text-4xl font-bold text-[#64748B]">{{ $countPending }}</p>
                </div>

                {{-- In Progress --}}
                <div class="bg-white rounded-sm shadow-sm p-6 border-l-4 border-blue-600">
                    <p class="text-xs font-bold text-blue-600 uppercase tracking-widest mb-1">In Progress</p>
                    <p class="text-4xl font-bold text-blue-800">{{ $countProgress }}</p>
                </div>

                {{-- Done --}}
                <div class="bg-white rounded-sm shadow-sm p-6 border-l-4 border-[#22C55E]">
                    <p class="text-xs font-bold text-[#22C55E] uppercase tracking-widest mb-1">Completed</p>
                    <p class="text-4xl font-bold text-[#15803d]">{{ $countDone }}</p>
                </div>
            </div>

            {{-- B. TABLE SECTION --}}
            <div class="bg-white shadow-sm rounded-sm border-t-4 border-[#1E3A5F]">

                {{-- Toolbar --}}
                <div class="p-6 border-b border-gray-100 flex flex-col md:flex-row justify-between items-end gap-4">
                    {{-- Search Form --}}
                    <form action="{{ route('fh.index') }}" method="GET" class="w-full md:w-auto flex gap-2">
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Search Ticket / Description..."
                            class="border-gray-300 rounded-sm text-sm focus:border-[#1E3A5F] focus:ring-[#1E3A5F]">
                        <button type="submit"
                            class="bg-[#1E3A5F] text-white px-4 py-2 rounded-sm text-sm font-bold uppercase hover:bg-[#162c46]">Filter</button>
                    </form>

                    {{-- Actions --}}
                    <div class="flex gap-2">
                        @if (Auth::user()->role == 'fh.admin')
                            <a href="{{ route('fh.dashboard') }}"
                                class="border-2 border-[#1E3A5F] text-[#1E3A5F] px-4 py-2 rounded-sm text-sm font-bold uppercase hover:bg-[#1E3A5F] hover:text-white transition">
                                Dashboard
                            </a>
                        @endif
                        <button @click="showCreateModal = true"
                            class="bg-[#22C55E] text-white px-6 py-2 rounded-sm text-sm font-bold uppercase shadow-sm hover:bg-[#16a34a] transition">
                            + New Request
                        </button>
                    </div>
                </div>

                {{-- Table --}}
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-[#F8FAFC]">
                            <tr>
                                <th
                                    class="px-6 py-4 text-left text-xs font-bold text-[#1E3A5F] uppercase tracking-wider">
                                    Ticket</th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-bold text-[#64748B] uppercase tracking-wider">
                                    Requester</th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-bold text-[#64748B] uppercase tracking-wider">
                                    Location</th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-bold text-[#64748B] uppercase tracking-wider">
                                    Category</th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-bold text-[#64748B] uppercase tracking-wider">
                                    Description</th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-bold text-[#64748B] uppercase tracking-wider">
                                    Status</th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-bold text-[#64748B] uppercase tracking-wider">
                                    Action</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @forelse($workOrders as $wo)
                                <tr class="hover:bg-slate-50 transition">
                                    <td class="px-6 py-4 font-mono text-sm font-bold text-[#1E3A5F]">
                                        {{ $wo->ticket_num }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-700">{{ $wo->requester_name }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-600">
                                        <span class="block font-bold">{{ $wo->plant }}</span>
                                        <span class="text-xs text-gray-400">{{ $wo->location_detail }}</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span
                                            class="px-2 py-1 text-xs font-bold rounded-sm bg-slate-100 text-slate-700 uppercase">{{ $wo->category }}</span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600 truncate max-w-xs">
                                        {{ Str::limit($wo->description, 30) }}</td>
                                    <td class="px-6 py-4">
                                        @php
                                            $statusColor = match ($wo->status) {
                                                'completed' => 'bg-green-100 text-[#22C55E]',
                                                'in_progress' => 'bg-blue-100 text-blue-700',
                                                'cancelled' => 'bg-red-100 text-red-700',
                                                default => 'bg-gray-100 text-gray-600',
                                            };
                                        @endphp
                                        <span
                                            class="px-3 py-1 rounded-full text-xs font-bold uppercase {{ $statusColor }}">
                                            {{ str_replace('_', ' ', $wo->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm">
                                        <button @click='ticket = @json($wo); showDetailModal = true'
                                            class="text-[#1E3A5F] font-bold hover:underline">View</button>
                                        @if (Auth::user()->role == 'fh.admin')
                                            <button
                                                @click='ticket = @json($wo); editForm.id = {{ $wo->id }}; showEditModal = true'
                                                class="ml-3 text-gray-400 font-bold hover:text-gray-700">Update</button>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-8 text-center text-gray-400">No Data Found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="p-4 border-t border-gray-100">
                    {{ $workOrders->links() }}
                </div>
            </div>
        </div>

        {{-- MODAL 1: CREATE REQUEST --}}
        <template x-teleport="body">
            <div x-show="showCreateModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
                <div class="fixed inset-0 bg-slate-900/80 backdrop-blur-sm transition-opacity" x-show="showCreateModal"
                    x-transition.opacity @click="showCreateModal = false"></div>

                <div class="flex min-h-full items-center justify-center p-4">
                    <div class="relative w-full max-w-3xl bg-white rounded-lg shadow-2xl overflow-hidden transform transition-all"
                        x-show="showCreateModal" x-transition:enter="ease-out duration-300"
                        x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                        x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100">

                        {{-- Header --}}
                        <div class="bg-[#1E3A5F] px-6 py-4 flex justify-between items-center border-b border-white/10">
                            <div>
                                <h3
                                    class="text-white font-bold text-lg uppercase tracking-wider flex items-center gap-2">
                                    <svg class="w-5 h-5 text-[#22C55E]" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 4v16m8-8H4"></path>
                                    </svg>
                                    Buat Laporan Facilities
                                </h3>
                                <p class="text-slate-300 text-xs mt-1">Isi formulir di bawah untuk permintaan pekerjaan
                                    baru.</p>
                            </div>
                            <button @click="showCreateModal = false"
                                class="text-white/70 hover:text-white transition bg-white/10 hover:bg-white/20 rounded-full p-1">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>

                        <form action="{{ route('fh.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="p-6 space-y-6">

                                {{-- SECTION 1: WAKTU & SHIFT (AUTO) --}}
                                <div class="bg-slate-50 p-4 rounded-md border border-slate-200">
                                    <h4 class="text-xs font-bold text-slate-500 uppercase mb-3 tracking-wide">Informasi
                                        Waktu (Otomatis)</h4>
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                        {{-- Tanggal --}}
                                        {{-- Tanggal --}}
                                        <div>
                                            <label
                                                class="block text-[10px] font-bold text-[#1E3A5F] uppercase mb-1">Tanggal
                                                Lapor</label>
                                            <div
                                                class="flex items-center bg-white border border-slate-300 rounded-sm px-3 py-2">
                                                <svg class="w-4 h-4 text-slate-400 mr-2" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                                    </path>
                                                </svg>

                                                {{-- 1. INPUT VISUAL (Hanya untuk dilihat user, tidak dikirim ke server) --}}
                                                {{-- HAPUS attribute 'name="report_date"' dari sini --}}
                                                <input type="text" x-model="currentDate" readonly
                                                    class="text-sm font-bold text-slate-700 bg-transparent border-none p-0 w-full focus:ring-0 cursor-default">

                                                {{-- 2. INPUT RAHASIA (Dikirim ke Server format YYYY-MM-DD) --}}
                                                <input type="hidden" name="report_date" x-model="currentDateDB">
                                            </div>
                                        </div>
                                        {{-- Jam --}}
                                        <div>
                                            <label
                                                class="block text-[10px] font-bold text-[#1E3A5F] uppercase mb-1">Jam
                                                Lapor</label>
                                            <div
                                                class="flex items-center bg-white border border-slate-300 rounded-sm px-3 py-2">
                                                <svg class="w-4 h-4 text-slate-400 mr-2" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                <input type="text" name="report_time" x-model="currentTime"
                                                    readonly
                                                    class="text-sm font-bold text-slate-700 bg-transparent border-none p-0 w-full focus:ring-0 cursor-default">
                                            </div>
                                        </div>
                                        {{-- Shift --}}
                                        <div>
                                            <label
                                                class="block text-[10px] font-bold text-[#1E3A5F] uppercase mb-1">Shift
                                                Saat Ini</label>
                                            <div
                                                class="flex items-center bg-yellow-50 border border-yellow-300 rounded-sm px-3 py-2">
                                                <span
                                                    class="w-2 h-2 rounded-full bg-yellow-500 mr-2 animate-pulse"></span>
                                                <input type="text" name="shift" x-model="currentShift" readonly
                                                    class="text-sm font-bold text-yellow-800 bg-transparent border-none p-0 w-full focus:ring-0 cursor-default uppercase">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <hr class="border-slate-100">

                                {{-- SECTION 2: LOKASI & PEKERJAAN --}}
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                                    {{-- Kiri: Lokasi (Plant) --}}
                                    <div>
                                        <label class="block text-xs font-bold text-[#1E3A5F] uppercase mb-1">Lokasi /
                                            Plant <span class="text-red-500">*</span></label>
                                        <select name="plant_id" x-model="form.plant_id" @change="filterMachines()"
                                            class="w-full border-slate-300 rounded-sm text-sm focus:border-[#1E3A5F] focus:ring-0 font-medium transition shadow-sm"
                                            required>
                                            <option value="">-- Pilih Plant --</option>
                                            @foreach ($plants as $plant)
                                                <option value="{{ $plant->id }}">{{ $plant->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    {{-- Kanan: Kategori Pekerjaan --}}
                                    <div>
                                        <label class="block text-xs font-bold text-[#1E3A5F] uppercase mb-1">Kategori
                                            Pekerjaan <span class="text-red-500">*</span></label>
                                        <select name="category" x-model="form.category"
                                            class="w-full border-slate-300 rounded-sm text-sm focus:border-[#1E3A5F] focus:ring-0 font-medium transition shadow-sm"
                                            required>
                                            <option value="">-- Pilih Kategori --</option>
                                            <option value="Modifikasi Mesin">Modifikasi Mesin</option>
                                            <option value="Pemasangan Mesin">Pemasangan Mesin</option>
                                            <option value="Pembongkaran Mesin">Pembongkaran Mesin</option>
                                            <option value="Relokasi Mesin">Relokasi Mesin</option>
                                            <option value="Perbaikan">Perbaikan</option>
                                            <option value="Pembuatan Alat Baru">Pembuatan Alat Baru</option>
                                            <option value="Rakit Steel Drum">Rakit Steel Drum</option>
                                        </select>
                                    </div>
                                </div>

                                {{-- SECTION 3: CONDITIONAL MACHINE SELECT --}}
                                <div x-show="needsMachine()" x-transition:enter="transition ease-out duration-300"
                                    x-transition:enter-start="opacity-0 transform scale-95"
                                    x-transition:enter-end="opacity-100 transform scale-100">
                                    <div class="bg-blue-50 p-4 rounded-sm border border-blue-100">
                                        <label class="block text-xs font-bold text-blue-800 uppercase mb-1">Pilih Mesin
                                            <span class="text-red-500">*</span></label>

                                        {{-- Logic jika Plant belum dipilih --}}
                                        <div x-show="!form.plant_id" class="text-xs text-blue-400 italic">Silakan
                                            pilih Plant terlebih dahulu untuk melihat daftar mesin.</div>

                                        {{-- Dropdown Mesin --}}
                                        <select name="machine_id" x-model="form.machine_id"
                                            class="w-full border-blue-300 rounded-sm text-sm focus:border-blue-500 focus:ring-0 font-medium mt-1"
                                            :required="needsMachine()" :disabled="!form.plant_id">
                                            <option value="">-- Pilih Mesin di Area Ini --</option>
                                            <template x-for="machine in filteredMachines" :key="machine.id">
                                                <option :value="machine.id" x-text="machine.name"></option>
                                            </template>
                                        </select>
                                    </div>
                                </div>

                                {{-- SECTION 4: DETAIL --}}
                                <div>
                                    <label class="block text-xs font-bold text-[#1E3A5F] uppercase mb-1">Deskripsi
                                        Pekerjaan <span class="text-red-500">*</span></label>
                                    <textarea name="description" rows="3"
                                        class="w-full border-slate-300 rounded-sm text-sm focus:border-[#1E3A5F] focus:ring-0 placeholder:text-slate-400"
                                        placeholder="Jelaskan detail pekerjaan yang dibutuhkan..." required></textarea>
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-[#1E3A5F] uppercase mb-1">Foto Bukti
                                        (Opsional)</label>
                                    <div class="flex items-center justify-center w-full">
                                        <label for="dropzone-file"
                                            class="flex flex-col items-center justify-center w-full h-32 border-2 border-slate-300 border-dashed rounded-lg cursor-pointer bg-slate-50 hover:bg-slate-100 transition">
                                            <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                                <svg class="w-8 h-8 mb-2 text-slate-400" aria-hidden="true"
                                                    xmlns="http://www.w3.org/2000/svg" fill="none"
                                                    viewBox="0 0 20 16">
                                                    <path stroke="currentColor" stroke-linecap="round"
                                                        stroke-linejoin="round" stroke-width="2"
                                                        d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2" />
                                                </svg>
                                                <p class="text-xs text-slate-500"><span class="font-bold">Klik untuk
                                                        upload</span> atau drag and drop</p>
                                                <p class="text-[10px] text-slate-400">SVG, PNG, JPG (MAX. 2MB)</p>
                                            </div>
                                            <input id="dropzone-file" name="photo" type="file"
                                                class="hidden" />
                                        </label>
                                    </div>
                                </div>

                            </div>

                            {{-- Footer --}}
                            <div class="bg-slate-50 px-6 py-4 flex justify-end gap-3 border-t border-slate-200">
                                <button type="button" @click="showCreateModal = false"
                                    class="px-5 py-2 bg-white border border-slate-300 text-slate-700 rounded-sm text-sm font-bold uppercase hover:bg-slate-50 transition shadow-sm">Batal</button>
                                <button type="submit"
                                    class="px-6 py-2 bg-[#1E3A5F] text-white rounded-sm text-sm font-bold uppercase hover:bg-[#162c46] shadow-md hover:shadow-lg transition transform active:scale-95">Kirim
                                    Laporan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </template>

        {{-- MODAL 2: EDIT / APPROVAL (ADMIN ONLY) --}}
        <template x-teleport="body">
            <div x-show="showEditModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
                <div class="fixed inset-0 bg-slate-900/80" @click="showEditModal = false"></div>
                <div class="flex min-h-full items-center justify-center p-4">
                    <div class="relative w-full max-w-lg bg-white rounded-sm shadow-xl">
                        <div class="bg-[#1E3A5F] px-6 py-4">
                            <h3 class="text-white font-bold uppercase">Update Ticket Status</h3>
                        </div>

                        <form x-bind:action="'/facilities/' + editForm.id + '/update-status'" method="POST"
                            class="p-6 space-y-4">
                            @csrf
                            @method('PUT')

                            {{-- Logic Approval --}}
                            <div x-show="ticket && ticket.status == 'pending'" class="flex gap-4">
                                <button type="submit" name="action" value="accept"
                                    class="flex-1 py-3 bg-[#1E3A5F] text-white font-bold uppercase rounded-sm hover:bg-[#162c46]">Accept</button>
                                <button type="submit" name="action" value="decline"
                                    class="flex-1 py-3 border-2 border-red-500 text-red-600 font-bold uppercase rounded-sm hover:bg-red-50">Decline</button>
                            </div>

                            {{-- Target Date Input (Show only if Accepting or updating Progress) --}}
                            <div x-show="ticket && (ticket.status == 'pending' || ticket.status == 'in_progress')">
                                <label class="block text-xs font-bold text-[#1E3A5F] uppercase mb-1">Target
                                    Completion</label>
                                <input type="text" name="target_date"
                                    class="w-full border-gray-300 rounded-sm date-picker" x-init="flatpickr($el, { minDate: 'today' })">
                            </div>

                            {{-- Logic Update Status (If already In Progress) --}}
                            <div x-show="ticket && ticket.status == 'in_progress'">
                                <label class="block text-xs font-bold text-[#1E3A5F] uppercase mb-1">Change
                                    Status</label>
                                <select name="status" class="w-full border-gray-300 rounded-sm">
                                    <option value="in_progress">In Progress</option>
                                    <option value="completed">Completed</option>
                                </select>
                                <button type="submit"
                                    class="mt-4 w-full bg-[#22C55E] text-white font-bold uppercase py-2 rounded-sm hover:bg-[#16a34a]">Save
                                    Changes</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </template>

    </div>
</x-app-layout>
