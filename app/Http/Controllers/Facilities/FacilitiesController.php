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
        return $query->with(['user', 'facilityTech'])->latest();
    }

    // --- MAIN PAGE (TABLE & FORM) ---
    public function index(Request $request)
    {
        $query = $this->buildQuery($request);
        $workOrders = $query->paginate(10)->withQueryString();

        // Ambil Data Master Plant (Exclude Department lain)
        $plants = Plant::whereNotIn('name', [
            'SS',
            'PE',
            'QC FO',
            'HC',
            'GA',
            'FA',
            'IT',
            'Sales',
            'Marketing',
            'RM Office',
            'RM 1',
            'RM 2',
            'RM 3',
            'RM 5',
            'MT',
            'FH',
            'FO',
            'QR'
        ])->get();

        $machines = Machine::all();

        // [UPDATE] Ambil Teknisi Facility yang Aktif saja
        $technicians = FacilityTech::all();

        // Counters
        $statsQuery = WorkOrderFacilities::query();
        if (Auth::user()->role !== 'fh.admin') {
            $statsQuery->where('requester_id', Auth::user()->id);
        }
        $countTotal = (clone $statsQuery)->count();
        $countPending = (clone $statsQuery)->where('status', 'pending')->count();
        $countProgress = (clone $statsQuery)->where('status', 'in_progress')->count();
        $countDone = (clone $statsQuery)->where('status', 'completed')->count();

        return view('Division.Facilities.Index', compact(
            'workOrders',
            'plants',
            'machines',
            'technicians', // Data teknisi dikirim ke view
            'countTotal',
            'countPending',
            'countProgress',
            'countDone'
        ));
    }

    // --- DASHBOARD (ADMIN STATS) ---
    public function dashboard(Request $request)
    {
        if (!in_array(Auth::user()->role, ['fh.admin', 'super.admin'])) {
            abort(403);
        }

        $query = WorkOrderFacilities::where('status', '!=', 'cancelled');

        if ($request->filled('start_date') && $request->filled('end_date')) {
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

        // Gantt Chart
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

            $ganttData[] = [$start, $end];

            if ($wo->status == 'completed') $ganttColors[] = '#94a3b8';
            elseif ($wo->status == 'in_progress') $ganttColors[] = '#1E3A5F';
            else $ganttColors[] = '#64748B';
        }

        $minDate = $workOrders->min('created_at');
        $startDateFilename = $minDate ? $minDate->format('Y-m-d') : date('Y-m-d');
        $startDateHeader = $minDate ? $minDate->translatedFormat('d F Y') : date('d F Y');

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
            'ganttLabels',
            'ganttData',
            'ganttColors',
            'startDateFilename',
            'startDateHeader'
        ));
    }

    // --- STORE ---
    public function store(Request $request)
    {
        // 1. Perbaiki Validasi: Gunakan nama input yang dikirim dari View (plant_id, bukan plant)
        $request->validate([
            'plant_id' => 'required', // View mengirim 'plant_id'
            'description' => 'required',
            'category' => 'required',
            'photo' => 'image|max:5120'
        ]);

        $photoPath = $request->hasFile('photo') ? $request->file('photo')->store('wo_facilities', 'public') : null;

        // Auto Generate Ticket
        $dateCode = date('Ymd');
        $prefix = 'FAC-' . $dateCode . '-';
        $lastTicket = WorkOrderFacilities::where('ticket_num', 'like', $prefix . '%')->orderBy('id', 'desc')->first();
        $newSeq = $lastTicket ? ((int)substr($lastTicket->ticket_num, -3) + 1) : 1;
        $ticketNum = $prefix . sprintf('%03d', $newSeq);

        // Cari Nama Plant berdasarkan ID (Jika database Anda menyimpan Nama Plant di kolom 'plant')
        // Jika database Anda menyimpan ID, langsung pakai $request->plant_id
        $plantName = \App\Models\Engineering\Plant::find($request->plant_id)->name ?? '-';

        WorkOrderFacilities::create([
            'ticket_num' => $ticketNum,
            'requester_id' => Auth::id(),
            'requester_name' => Auth::user()->name,

            // Simpan Data Plant & Mesin
            'plant' => $plantName, // Simpan Namanya (sesuai struktur tabel lama)
            'machine_id' => $request->machine_id, // Simpan ID Mesin (sesuai migration baru)

            // Simpan Detail Lokasi & Waktu
            'location_details' => $request->location_detail ?? '-',
            'report_date' => $request->report_date ? Carbon::parse($request->report_date) : now(),
            'report_time' => $request->report_time,
            'shift' => $request->shift,

            'description' => $request->description,
            'category' => $request->category,
            'photo_path' => $photoPath,
            'status' => 'pending'
        ]);

        return redirect()->route('fh.index')->with('success', 'Request Created Successfully!');
    }

    // --- UPDATE STATUS (ACCEPT / ASSIGN TECH) ---
    public function updateStatus(Request $request, $id)
    {
        // [FIX] Gunakan nama Model yang benar (WorkOrderFacilities)
        $wo = WorkOrderFacilities::findOrFail($id);

        if ($request->action == 'accept') {
            // [UPDATE] Validasi Wajib Pilih Teknisi
            $request->validate([
                'facility_tech_id' => 'required|exists:facility_techs,id',
                'target_date' => 'required|date'
            ]);

            $wo->status = 'in_progress';
            $wo->target_completion_date = $request->target_date;

            // [UPDATE] Simpan Data Teknisi
            $wo->facility_tech_id = $request->facility_tech_id;

            $wo->processed_by = Auth::id();
            $wo->processed_by_name = Auth::user()->name;
        } elseif ($request->action == 'decline') {
            $wo->status = 'cancelled';
            $wo->processed_by = Auth::id(); // Opsional: catat siapa yang menolak

        } else {
            // General Update (Progress -> Completed)
            $wo->status = $request->status;

            if ($request->status == 'completed') {
                $wo->actual_completion_date = date('Y-m-d');
            }

            if ($request->filled('target_date')) {
                $wo->target_completion_date = $request->target_date;
            }

            // Jika admin ingin mengubah teknisi di tengah jalan (Opsional)
            if ($request->filled('facility_tech_id')) {
                $wo->facility_tech_id = $request->facility_tech_id;
            }
        }

        $wo->save();
        return redirect()->back()->with('success', 'Status Tiket Berhasil Diperbarui!');
    }
}
