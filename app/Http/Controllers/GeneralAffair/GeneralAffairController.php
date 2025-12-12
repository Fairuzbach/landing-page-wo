<?php

namespace App\Http\Controllers\GeneralAffair;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\GeneralAffair\WorkOrderGeneralAffair;
use App\Models\GeneralAffair\WorkOrderGaHistory;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\Engineering\Plant;
use Symfony\Component\HttpFoundation\StreamedResponse;

class GeneralAffairController extends Controller
{
    // HAPUS dd($request->all()); yang tadi kamu buat untuk debugging

    private function buildQuery(Request $request)
    {
        $query = WorkOrderGeneralAffair::query();
        $user = Auth::user();
        if ($user->role !== 'ga.admin') {
            $query->where('requester_id', $user->id);
        }
        // 1. SEARCH LOGIC
        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('ticket_num', 'like', "%{$search}%")       // Cari No Tiket
                    ->orWhere('description', 'like', "%{$search}%")    // Cari Uraian/Deskripsi
                    ->orWhere('department', 'like', "%{$search}%")     // Cari Departemen
                    ->orWhere('plant', 'like', "%{$search}%")          // Cari Nama Plant (Jika kolomnya string)
                    ->orWhere('category', 'like', "%{$search}%");      // Cari Kategori (Low/High)
            });
        }

        // 2. FILTER STATUS
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // 3. FILTER RANGE TANGGAL (Opsional jika form date diisi)
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        // Load relasi user agar tidak berat (N+1 problem)
        return $query->with('user')->latest();
    }
    public function index(Request $request)
    {
        $query = $this->buildQuery($request);
        $workOrders = $query->with(['user', 'histories.user'])->paginate(10)->withQueryString();
        $pageIds = $workOrders->pluck('id')->toArray();
        $plants = Plant::all();

        // Hitung Counter Sederhana
        $user = Auth::user();
        $statsQuery = WorkOrderGeneralAffair::query();
        if ($user->role !== 'ga.admin') {
            $statsQuery->where('requester_id', $user->id);
        }

        $countTotal = (clone $statsQuery)->count();
        $countPending = (clone $statsQuery)->where('status', 'pending')->count();
        $countInProgress = (clone $statsQuery)->where('status', 'in_progress')->count();
        $countCompleted = (clone $statsQuery)->where('status', 'completed')->count();

        return view('Division.GeneralAffair.GeneralAffair', compact(
            'workOrders',
            'plants',
            'pageIds',
            'countTotal',
            'countPending',
            'countInProgress',
            'countCompleted'
        ));
    }

    public function dashboard()
    {
        if (Auth::user()->role !== 'ga.admin') {
            abort(403, 'Akses Ditolak.');
        }

        $statsQuery = WorkOrderGeneralAffair::query();

        // 1. Counter Utama (Tetap sama)
        $countTotal = (clone $statsQuery)->count();
        $countPending = (clone $statsQuery)->where('status', 'pending')->count();
        $countInProgress = (clone $statsQuery)->where('status', 'in_progress')->count();
        $countCompleted = (clone $statsQuery)->where('status', 'completed')->count();

        // --- CHART 1: SEMUA LOKASI (Bar Chart) ---
        // Mengambil data berdasarkan kolom 'plant' (yang sekarang kita anggap Lokasi)
        $locData = (clone $statsQuery)
            ->selectRaw("plant as location, count(*) as total")
            ->whereNotNull('plant')
            ->groupBy('plant')
            ->orderByDesc('total')
            ->get();
        $chartLocLabels = $locData->pluck('location')->toArray();
        $chartLocValues = $locData->pluck('total')->toArray();

        // --- CHART 2: SEMUA DEPARTMENT (Bar/Pie Chart) ---
        $deptData = (clone $statsQuery)
            ->selectRaw("department, count(*) as total")
            ->whereNotNull('department')
            ->groupBy('department')
            ->orderByDesc('total')
            ->get();
        $chartDeptLabels = $deptData->pluck('department')->toArray();
        $chartDeptValues = $deptData->pluck('total')->toArray();

        // --- CHART 3: PARAMETER PERMINTAAN (Pie Chart) ---
        $paramData = (clone $statsQuery)
            ->selectRaw("parameter_permintaan, count(*) as total")
            ->whereNotNull('parameter_permintaan')
            ->groupBy('parameter_permintaan')
            ->get();
        $chartParamLabels = $paramData->pluck('parameter_permintaan')->toArray();
        $chartParamValues = $paramData->pluck('total')->toArray();

        // --- CHART 4: BOBOT PEKERJAAN (Dulu Kategori) ---
        $bobotData = (clone $statsQuery)
            ->selectRaw('category, count(*) as total') // Kolom database tetap 'category'
            ->groupBy('category')
            ->pluck('total', 'category')->toArray();

        // Mapping label baru (Ringan, Sedang, Berat)
        // Asumsi di DB: LOW -> Ringan, MEDIUM -> Sedang, HIGH -> Berat
        $chartBobotLabels = ['Berat (High)', 'Sedang (Medium)', 'Ringan (Low)'];
        $chartBobotValues = [
            $bobotData['HIGH'] ?? 0,
            $bobotData['MEDIUM'] ?? 0,
            $bobotData['LOW'] ?? 0
        ];

        // --- GANTT CHART (Tetap sama) ---
        $timelineRaw = WorkOrderGeneralAffair::whereNotNull('target_completion_date')
            ->where('status', '!=', 'cancelled')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $ganttLabels = [];
        $ganttData = [];
        $ganttColors = [];

        foreach ($timelineRaw as $ticket) {
            $loc = $ticket->plant; // Lokasi
            $ganttLabels[] = $ticket->ticket_num . " (" . $loc . ")";

            $start = $ticket->created_at->format('Y-m-d');
            $end = $ticket->target_completion_date ?? date('Y-m-d');
            if ($end < $start) $end = $start;

            $ganttData[] = [$start, $end];

            if ($ticket->category == 'HIGH') $ganttColors[] = 'rgba(239, 68, 68, 0.7)';
            elseif ($ticket->category == 'MEDIUM') $ganttColors[] = 'rgba(245, 158, 11, 0.7)';
            else $ganttColors[] = 'rgba(34, 197, 94, 0.7)';
        }

        return view('Division.GeneralAffair.Dashboard', compact(
            'countTotal',
            'countPending',
            'countInProgress',
            'countCompleted',
            'chartLocLabels',
            'chartLocValues',
            'chartDeptLabels',
            'chartDeptValues',
            'chartParamLabels',
            'chartParamValues',
            'chartBobotLabels',
            'chartBobotValues',
            'ganttLabels',
            'ganttData',
            'ganttColors'
        ));
    }
    //halaman form
    public function create()
    {
        $plants = Plant::all();
        $workOrders = WorkOrderGeneralAffair::with('user')->latest()->paginate(10);
        return view('general-affair.index', compact('workOrders', 'plants'));
    }
    //validasi plant atau department
    public function store(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'plant_id' => 'required',
            'department' => 'required',
            'description' => 'required',
            'category' => 'required',
            'parameter_permintaan' => 'required',
            'photo' => 'required|image|max:5120'
        ]);
        $plantData = Plant::find($request->plant_id);
        $plantName = $plantData ? $plantData->name : 'Unknown Plant';

        $photoPath = $request->file('photo')->store('wo_ga', 'public');

        $dateCode = date('Ymd');
        $prefix = 'woGA-' . $dateCode . '-';
        $lastTicket = WorkOrderGeneralAffair::where('ticket_num', 'like', $prefix . '%')->orderBy('id', 'desc')->first();
        $newSequence  = $lastTicket ? ((int) substr($lastTicket->ticket_num, -3) + 1) : 1;
        $ticketNum = $prefix . sprintf('%03d', $newSequence);

        $ticket = WorkOrderGeneralAffair::create([
            'ticket_num' => $ticketNum,
            'requester_id' => Auth::id(),
            'requester_name' => Auth::user()->name,
            'plant' => $plantName,
            'department' => $request->department,
            'description' => $request->description,
            'category' => $request->category,
            'parameter_permintaan' => $request->parameter_permintaan,
            'status' => 'pending',
            'status_permintaan' => $request->status_permintaan,
            'photo_path' => $photoPath,
        ]);
        WorkOrderGaHistory::create([
            'work_order_id' => $ticket->id,
            'user_id' => Auth::id(),
            'action' => 'Created',
            'description' => 'Tiket baru berhasil dibuat.'
        ]);

        return redirect()->route('ga.index')->with('success', 'Permintaan berhasil dibuat!');
    }

    public function updateStatus(Request $request, $id)
    {
        if (!in_array(auth()->user()->role, ['ga.admin'])) {
            abort(403, 'Anda tidak memiliki akses.');
        }
        $user = auth()->user();
        $ticket = WorkOrderGeneralAffair::findOrFail($id);
        $oldStatus = $ticket->status;
        if ($ticket->status == 'pending') {
            if ($request->action == 'decline') {
                $ticket->status = 'cancelled';
                $ticket->processed_by = $user->id;
                $ticket->processed_by_name = $user->name;
                $ticket->save();

                WorkOrderGaHistory::create([
                    'work_order_id' => $ticket->id,
                    'user_id' => $user->id,
                    'action' => 'Declined',
                    'description' => 'Permintaan ditolak oleh ' . $user->name . '.',
                ]);

                return redirect()->route('ga.index')->with('error', 'Permintaan telah di tolak.');
            }
            if ($request->action == 'accept') {
                $request->validate([
                    'category' => 'required',
                    'target_date' => 'required|date',
                ]);
                $ticket->status = 'in_progress';
                $ticket->category = $request->category;
                $ticket->target_completion_date = $request->target_date;
                $ticket->processed_by = $user->id;
                $ticket->processed_by_name = $user->name;
                $ticket->save();

                WorkOrderGaHistory::create([
                    'work_order_id' => $ticket->id,
                    'user_id' => $user->id,
                    'action' => 'Accepted',
                    'description' => "Permintaan diterima. Target: {$request->target_date}. Kategori: {$request->category}.",
                ]);
                return redirect()->route('ga.index')->with('success', 'Permintaan berhasil diterima dan akan di proses.');
            }
        }
        $request->validate([
            'status' => 'required',
        ]);
        $ticket->status = $request->status;
        if ($request->status === 'completed') {
            $ticket->actual_completion_date = now()->toDateString();
        } elseif ($request->filled('target_date')) {
            $ticket->target_completion_date = $request->target_date;
        }

        $ticket->processed_by = $user->id;
        $ticket->processed_by_name = $user->name;
        $ticket->save();

        WorkOrderGaHistory::create([
            'work_order_id' => $ticket->id,
            'user_id' => $user->id,
            'action' => 'Status Updated',
            'description' => 'Status diubah dari ' . $oldStatus . ' menjadi ' . $request->status . '.',
        ]);

        return redirect()->route('ga.index')->with('success', 'Status tiket berhasil diperbarui!');
    }

    public function export(Request $request)
    {
        // 1. LOGIKA QUERY
        if ($request->filled('selected_ids') && $request->selected_ids != '') {
            // Ambil input, jika ada duplikat ambil yang terakhir atau unik
            $idsRaw = is_array($request->selected_ids) ? end($request->selected_ids) : $request->selected_ids;
            $ids = explode(',', $idsRaw);

            $query = WorkOrderGeneralAffair::with('user')
                ->whereIn('id', $ids)
                ->latest();
        } else {
            // Gunakan logika filter yang sama dengan index
            $query = $this->buildQuery($request);
            $query->with('user'); // Pastikan relation user di-load
        }

        $filename = 'request-orders-' . date('Y-m-d-H-i') . '.csv';

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        return response()->stream(function () use ($query) {
            $file = fopen('php://output', 'w');

            // --- PERBAIKAN UTAMA DISINI ---
            // Bersihkan output buffer agar file tidak corrupt
            if (ob_get_level()) {
                ob_end_clean();
            }

            // Tambahkan BOM agar Excel bisa baca karakter khusus (UTF-8)
            fputs($file, "\xEF\xBB\xBF");
            // ------------------------------

            // Header Kolom CSV
            fputcsv($file, [
                'ID Tiket',
                'Pemohon',
                'Divisi Pemohon',
                'Departemen',
                'Plant',
                'Parameter Permintaan',
                'Status',
                'Status Permintaan',
                'Tanggal Target',
                'Tanggal Selesai',
                'Tanggal Dibuat'
            ]);

            // Gunakan chunk agar memory tidak habis jika data ribuan
            $query->chunk(100, function ($tickets) use ($file) {
                foreach ($tickets as $ticket) {

                    // Handle jika user terhapus
                    $user = $ticket->user;
                    $namaPemohon = $user ? $user->name : ($ticket->requester_name ?? '-');
                    $divisiPemohon = $user ? ($user->divisi ?? '-') : '-'; // Pastikan kolom 'divisi' ada di tabel users

                    fputcsv($file, [
                        $ticket->ticket_num,
                        $namaPemohon,
                        $divisiPemohon,
                        $ticket->department,
                        $ticket->plant,
                        $ticket->parameter_permintaan ?? $ticket->category,
                        ucfirst($ticket->status),
                        $ticket->status_permintaan,
                        $ticket->target_completion_date,
                        $ticket->actual_completion_date,
                        $ticket->created_at->format('Y-m-d'),
                    ]);
                }
            });

            fclose($file);
        }, 200, $headers);
    }
}
