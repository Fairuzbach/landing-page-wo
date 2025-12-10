<?php

namespace App\Http\Controllers\GeneralAffair;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\GeneralAffair\WorkOrderGeneralAffair;
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
        // dd($request->all());
        $query = $this->buildQuery($request);
        $workOrders = $query->paginate(10)->withQueryString();
        $pageIds = $workOrders->pluck('id')->toArray();
        $plants = Plant::all();
        return view('Division.GeneralAffair.GeneralAffair', compact('workOrders', 'plants', 'pageIds'));
    }

    //halaman form
    public function create()
    {
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

        WorkOrderGeneralAffair::create([
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

        return redirect()->route('ga.index')->with('success', 'Permintaan berhasil dibuat!');
    }

    public function updateStatus(Request $request, $id)
    {
        if (!in_array(auth()->user()->role, ['ga.admin'])) {
            abort(403, 'Anda tidak memiliki akses.');
        }
        $user = auth()->user();
        $ticket = WorkOrderGeneralAffair::findOrFail($id);
        if ($ticket->status == 'pending') {
            if ($request->action == 'decline') {
                $ticket->status = 'cancelled';
                $ticket->processed_by = $user->id;
                $ticket->processed_by_name = $user->name;
                $ticket->save();
                return redirect()->route('ga.index')->with('success', 'Permintaan telah di tolak.');
            }
            if ($request->action == 'accept') {
                $request->validate([
                    'category' => 'required',
                    'target_date' => 'required|date',
                ], [
                    'target_date.required' => 'Target Date wajib diisi untuk menerima tiket.',
                    'category.required'    => 'Kategori wajib dipilih.'
                ]);
                $ticket->status = 'in_progress';
                $ticket->category = $request->category;
                $ticket->target_completion_date = $request->target_date;
                $ticket->processed_by = $user->id;
                $ticket->processed_by_name = $user->name;
                $ticket->save();

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
        return redirect()->route('ga.index')->with('success', 'Status tiket berhasil diperbarui!');
    }

    public function export(Request $request)
    {
        // --- 1. LOGIKA QUERY (Fixed) ---
        if ($request->filled('selected_ids') && $request->selected_ids != '') {
            // A. Jika Export Selected (Checkbox)
            $ids = explode(',', $request->selected_ids);

            $query = WorkOrderGeneralAffair::with('user')
                ->whereIn('id', $ids)
                ->latest();
        } else {
            // B. Jika Export Filtered (Search/Date/Status)
            $query = $this->buildQuery($request);

            // Load relasi user untuk performa
            $query->with('user');
        }

        // (HAPUS baris '$query = ...' yang ada disini sebelumnya karena menimpa logika di atas)

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

            // Header Kolom CSV
            fputcsv($file, [
                'ID Tiket',
                'Pemohon',
                'Departemen',
                'Plant',
                'Parameter Permintaan',
                'Status',
                'Status Permintaan',
                'Tanggal Target',
                'Tanggal Selesai',
                'Tanggal Dibuat'
            ]);

            $query->chunk(100, function ($tickets) use ($file) {
                foreach ($tickets as $ticket) {

                    // LOGIKA NAMA PEMOHON:
                    // 1. Coba ambil dari relasi User (Live data)
                    // 2. Jika user dihapus/null, ambil dari kolom 'requester_name' (History data)
                    // 3. Jika keduanya null, tampilkan strip
                    $namaPemohon = $ticket->user->name ?? $ticket->requester_name ?? '-';

                    fputcsv($file, [
                        // ID Tiket Custom (woGA-...)
                        $ticket->ticket_num,

                        // Nama Pemohon
                        $namaPemohon,

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
