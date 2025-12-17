<?php

namespace App\Http\Controllers\Facilities;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Facilities\WorkOrderFacilities; // Pastikan namespace ini benar sesuai folder Anda
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\Engineering\Plant;
use App\Models\Engineering\Machine;
use App\Models\FacilityTech; // Model Teknisi Facility
use Maatwebsite\Excel\Facades\Excel; // PENTING: Import Excel
use App\Exports\FacilitiesExport;      // PENTING: Import Class Export tadi

class FacilitiesController extends Controller
{
    private function buildQuery(Request $request)
    {
        $query = WorkOrderFacilities::query();
        $user = Auth::user();

        // Logic Admin: Hanya 'fh.admin' dan 'super.admin' yang bisa lihat semua
        if ($user->role !== 'fh.admin' && $user->role !== 'super.admin') {
            $query->where('requester_id', $user->id);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('ticket_num', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('plant', 'like', "%{$search}%");
            });
        }

        // Filters
        if ($request->filled('status')) $query->where('status', $request->status);
        if ($request->filled('category')) $query->where('category', $request->category);

        // Date Range
        if ($request->filled('start_date')) $query->whereDate('created_at', '>=', $request->start_date);
        if ($request->filled('end_date')) $query->whereDate('created_at', '<=', $request->end_date);

        // Eager Load 'facilityTech' jika relasi sudah dibuat di model WorkOrderFacilities
        return $query->with(['user', 'technicians', 'machine'])->latest();
    }

    // --- MAIN PAGE (TABLE & FORM) ---
    public function index(Request $request)
    {
        // 1. BASE QUERY
        $query = WorkOrderFacilities::query();
        $user = Auth::user();

        // 2. FILTER SEARCH
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('ticket_num', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('plant', 'like', "%{$search}%")
                    ->orWhere('requester_name', 'like', "%{$search}%");
            });
        }

        // 3. FILTER DROPDOWN (Category, Status, Plant)
        if ($request->filled('status')) $query->where('status', $request->status);
        if ($request->filled('category')) $query->where('category', $request->category);
        if ($request->filled('plant_id')) {
            $plantName = Plant::find($request->plant_id)->name ?? '';
            $query->where('plant', $plantName);
        }

        // Permission Check
        if ($user->role !== 'fh.admin' && $user->role !== 'super.admin') {
            $query->where('requester_id', $user->id);
        }

        // 4. LOGIKA EXPORT XLSX
        if ($request->has('export') && $request->export == 'true') {
            // Jika user memilih checkbox tertentu
            if ($request->filled('selected_ids')) {
                $ids = explode(',', $request->selected_ids);
                $exportData = WorkOrderFacilities::with(['technicians', 'machine'])->whereIn('id', $ids)->get();
            } else {
                // Jika tidak, export semua hasil filter saat ini
                $exportData = $query->with(['technicians', 'machine'])->get();
            }

            return Excel::download(new FacilitiesExport($exportData), 'facilities_report_' . date('Ymd_His') . '.xlsx');
        }

        // 5. GET DATA UTAMA
        $workOrders = $query->with(['user', 'technicians', 'machine'])
            ->latest()
            ->paginate(10)
            ->withQueryString(); // Agar filter tidak hilang saat ganti halaman

        // DATA PENDUKUNG VIEW
        $plants = Plant::whereNotIn('name', ['SS', 'PE', 'QC FO', 'HC', 'GA', 'FA', 'IT', 'Sales', 'Marketing', 'RM Office', 'RM 1', 'RM 2', 'RM 3', 'RM 5', 'MT', 'FH', 'FO', 'QR'])->get();
        $machines = Machine::all();
        $technicians = FacilityTech::all();
        $pageIds = $workOrders->pluck('id')->toArray();

        // COUNTERS
        $statsQuery = WorkOrderFacilities::query();
        if ($user->role !== 'fh.admin' && $user->role !== 'super.admin') {
            $statsQuery->where('requester_id', $user->id);
        }
        $countTotal = (clone $statsQuery)->count();
        $countPending = (clone $statsQuery)->where('status', 'pending')->count();
        $countProgress = (clone $statsQuery)->where('status', 'in_progress')->count();
        $countDone = (clone $statsQuery)->where('status', 'completed')->count();

        return view('Division.Facilities.Index', compact(
            'workOrders',
            'plants',
            'machines',
            'technicians',
            'countTotal',
            'countPending',
            'countProgress',
            'countDone',
            'pageIds'
        ));
    }

    // --- DASHBOARD (ADMIN STATS) ---
    public function dashboard(Request $request)
    {
        if (!in_array(Auth::user()->role, ['fh.admin', 'super.admin'])) {
            abort(403);
        }

        $query = WorkOrderFacilities::where('status', '!=', 'cancelled');

        // Allow filtering by month (format: YYYY-MM) for interactive dashboard
        $selectedMonth = null;
        if ($request->filled('month')) {
            $selectedMonth = $request->month;
            try {
                $start = Carbon::createFromFormat('Y-m', $selectedMonth)->startOfMonth()->format('Y-m-d');
                $end = Carbon::createFromFormat('Y-m', $selectedMonth)->endOfMonth()->format('Y-m-d');
                $query->whereDate('created_at', '>=', $start)
                    ->whereDate('created_at', '<=', $end);
            } catch (\Exception $e) {
                // ignore invalid month format
            }
        } elseif ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereDate('created_at', '>=', $request->start_date)
                ->whereDate('created_at', '<=', $request->end_date);
        } else {
            $query->take(50);
        }
        $workOrders = $query->latest()->get();

        // Counters
        $countTotal = $workOrders->count();
        $countPending = $workOrders->where('status', 'pending')->count();
        $countProgress = $workOrders->where('status', 'in_progress')->count();
        $countDone = $workOrders->where('status', 'completed')->count();

        // Charts Logic
        $catData = $workOrders->groupBy('category')->map->count();
        $chartCatLabels = $catData->keys();
        $chartCatValues = $catData->values();

        $statusData = $workOrders->groupBy('status')->map->count();
        $chartStatusLabels = $statusData->keys();
        $chartStatusValues = $statusData->values();

        // Plant chart
        $plantData = $workOrders->groupBy('plant')->map->count();
        $chartPlantLabels = $plantData->keys();
        $chartPlantValues = $plantData->values();

        // Completion percentage for selected period
        $periodTotal = $workOrders->count();
        $periodCompleted = $workOrders->where('status', 'completed')->count();
        $completionPct = $periodTotal ? round(($periodCompleted / $periodTotal) * 100, 1) : 0;

        // Gantt Chart - prepare timeline data
        $ganttData = [];
        $ganttLabels = [];
        $ganttColors = [];

        foreach ($workOrders as $wo) {
            $ganttLabels[] = $wo->ticket_num;
            $start = $wo->created_at ? $wo->created_at->format('Y-m-d') : date('Y-m-d');

            if ($wo->status == 'completed' && $wo->actual_completion_date) {
                $end = $wo->actual_completion_date;
            } else {
                $end = $wo->target_completion_date ?? date('Y-m-d');
            }
            if ($end < $start) $end = $start;
            if ($end == $start) $end = Carbon::parse($end)->addDay()->format('Y-m-d');

            // Calculate duration in days
            $startDate = Carbon::parse($start);
            $endDate = Carbon::parse($end);
            $duration = $endDate->diffInDays($startDate) + 1;

            $ganttData[] = [
                'ticket' => $wo->ticket_num,
                'status' => $wo->status,
                'start' => $start,
                'end' => $end,
                'duration' => max($duration, 1),
                'plant' => $wo->plant ?? '-',
                'machine_name' => $wo->machine_name ?? '-',
                'category' => $wo->category ?? '-'
            ];

            if ($wo->status == 'completed') $ganttColors[] = '#10B981'; // green
            elseif ($wo->status == 'in_progress') $ganttColors[] = '#2563EB'; // blue
            else $ganttColors[] = '#F59E0B'; // yellow
        }

        $minDate = $workOrders->min('created_at');
        $startDateFilename = $minDate ? $minDate->format('Y-m-d') : date('Y-m-d');
        $startDateHeader = $minDate ? $minDate->translatedFormat('d F Y') : date('d F Y');

        // Technician PIC chart: count how many WOs each tech is assigned to
        $techData = [];
        foreach ($workOrders as $wo) {
            if ($wo->technicians && $wo->technicians->count() > 0) {
                foreach ($wo->technicians as $tech) {
                    if (!isset($techData[$tech->name])) {
                        $techData[$tech->name] = 0;
                    }
                    $techData[$tech->name]++;
                }
            }
        }
        arsort($techData); // Sort descending by count
        $chartTechLabels = collect($techData)->keys();
        $chartTechValues = collect($techData)->values();

        return view('Division.Facilities.Dashboard', compact(
            'workOrders',
            'countTotal',
            'countPending',
            'countProgress',
            'countDone',
            'chartCatLabels',
            'chartCatValues',
            'chartStatusLabels',
            'chartStatusValues',
            'chartPlantLabels',
            'chartPlantValues',
            'chartTechLabels',
            'chartTechValues',
            'ganttLabels',
            'ganttData',
            'ganttColors',
            'startDateFilename',
            'startDateHeader',
            'completionPct',
            'selectedMonth'
        ));
    }

    // --- STORE ---
    public function store(Request $request)
    {
        // 1. Validasi Dasar
        $rules = [
            'requester_name' => 'required|string',
            'plant_id' => 'required',
            'description' => 'required',
            'category' => 'required',
            'photo' => 'image|max:5120'
        ];

        // 2. Validasi Tambahan Berdasarkan Kategori
        if ($request->category == 'Pemasangan Mesin') {
            // Jika pasang mesin baru, wajib isi nama mesin baru
            $rules['new_machine_name'] = 'required|string|max:255';
        }
        // [FIX] Tambahkan 'Pembuatan Alat Baru' ke array ini
        elseif (in_array($request->category, [
            'Modifikasi Mesin',
            'Pembongkaran Mesin',
            'Relokasi Mesin',
            'Perbaikan',
            'Pembuatan Alat Baru' // <-- DITAMBAHKAN
        ])) {
            // Jika kategori ini, wajib pilih mesin dari dropdown
            $rules['machine_id'] = 'required|exists:machines,id';
        }

        $request->validate($rules);

        $photoPath = $request->hasFile('photo') ? $request->file('photo')->store('wo_facilities', 'public') : null;

        // ... (Sisa kode ke bawah sama seperti sebelumnya) ...

        // 3. Generate Ticket Number
        $dateCode = date('Ymd');
        $prefix = 'FAC-' . $dateCode . '-';
        $lastTicket = WorkOrderFacilities::where('ticket_num', 'like', $prefix . '%')->orderBy('id', 'desc')->first();
        $newSeq = $lastTicket ? ((int)substr($lastTicket->ticket_num, -3) + 1) : 1;
        $ticketNum = $prefix . sprintf('%03d', $newSeq);

        $plantObj = \App\Models\Engineering\Plant::find($request->plant_id);
        $plantName = $plantObj ? $plantObj->name : '-';

        // 5. LOGIKA PENENTUAN MESIN
        $machineId = null;
        $machineName = null;

        if ($request->category == 'Pemasangan Mesin') {
            // SKENARIO A: MESIN BARU (Create Master Data)
            $newMachine = \App\Models\Engineering\Machine::create([
                'plant_id' => $request->plant_id,
                'name' => $request->new_machine_name,
                'code' => 'NEW-' . strtoupper(\Illuminate\Support\Str::random(5)),
            ]);
            $machineId = $newMachine->id;
            $machineName = $newMachine->name;
        } else {
            // SKENARIO B: MESIN LAMA (Dari Dropdown)
            if ($request->filled('machine_id')) {
                $m = \App\Models\Engineering\Machine::find($request->machine_id);
                $machineId = $m->id;
                $machineName = $m->name;
            }
        }

        WorkOrderFacilities::create([
            'ticket_num' => $ticketNum,
            'requester_id' => Auth::id(),
            'requester_name' => $request->requester_name,
            'plant' => $plantName,
            'machine_id' => $machineId,
            'machine_name' => $machineName,
            'location_details' => $request->location_detail ?? '-',
            'report_date' => $request->report_date ? Carbon::parse($request->report_date) : now(),
            'report_time' => $request->report_time,
            'shift' => $request->shift,
            'description' => $request->description,
            'category' => $request->category,
            'target_completion_date' => $request->target_completion_date,
            'photo_path' => $photoPath,
            'status' => 'pending'
        ]);

        return redirect()->route('fh.index')->with('success', 'Request Created Successfully!');
    }

    // --- UPDATE STATUS (ACCEPT / ASSIGN TECH) ---
    // --- UPDATE STATUS ---
    // --- UPDATE STATUS ---
    public function updateStatus(Request $request, $id)
    {
        $wo = WorkOrderFacilities::findOrFail($id);

        // 1. UPDATE STATUS UTAMA
        $wo->status = $request->status;

        // 2. SIMPAN TEKNISI (MULTIPLE)
        // Pastikan kita mengambil input sebagai array
        $ids = $request->input('facility_tech_ids', []);

        // Jika entah kenapa inputnya string "1,2", kita pecah
        if (!is_array($ids)) {
            $ids = explode(',', (string)$ids);
        }

        // Filter: Hapus nilai kosong/null & pastikan angka
        $ids = array_filter($ids, function ($value) {
            return is_numeric($value) && $value > 0;
        });

        // Debugging (Cek di Laravel Log jika masih error)
        \Log::info('Saving Techs for WO #' . $id, ['ids' => $ids]);

        // Simpan ke Pivot Table (Sync)
        $wo->technicians()->sync($ids);

        // 3. UPDATE TANGGAL (Auto-fill)
        if ($request->filled('start_date')) {
            $wo->start_date = $request->start_date;
        }

        if ($request->status == 'completed') {
            $wo->actual_completion_date = $wo->actual_completion_date ?? now();
        } elseif ($request->status != 'completed') {
            $wo->actual_completion_date = null;
        }

        // 4. CATAT PEMROSES
        if (!$wo->processed_by) {
            $wo->processed_by = Auth::id();
            $wo->processed_by_name = Auth::user()->name;
        }

        $wo->save();
        return redirect()->back()->with('success', 'Status updated successfully!');
    }
}
