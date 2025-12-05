<?php

namespace App\Http\Controllers\Engineering;

use App\Models\Engineering\WorkOrderEngineering;
use App\Models\Engineering\Plant;
use App\Models\Engineering\EngineerTech;
use App\Models\Engineering\ImprovementStatus;
use App\Models\Engineering\ParameterImprovement;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class WorkOrderEngineeringController extends Controller
{

    public function index(Request $request)
    {
        $query = WorkOrderEngineering::latest();

        // 1. SEARCH
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('ticket_num', 'like', '%' . $search . '%')
                    ->orWhere('machine_name', 'like', '%' . $search . '%')->orWhere('plant', 'like', '%' . $search . '%');
            });
        }

        // 2. FILTER STATUS (Disesuaikan dengan improvement_status)
        if ($request->filled('improvement_status')) {
            $query->where('improvement_status', $request->improvement_status);
        }
        // Fallback untuk jaga-jaga jika ada link lama yang pakai work_status
        elseif ($request->filled('work_status')) {
            $query->where('improvement_status', $request->work_status);
        }

        // 3. PAGINATION
        $workOrders = $query->with('requester')
            ->paginate(10)
            ->withQueryString();

        // Data Pendukung
        $plants = Plant::with('machines')->get();
        $technicians = EngineerTech::all();
        $improvementStatuses = ImprovementStatus::all();
        $improvementParameters = ParameterImprovement::all();

        return view('Division.Engineering.Engineering', compact(
            'workOrders',
            'plants',
            'technicians',
            'improvementParameters',
            'improvementStatuses'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'report_date' => 'required|date',
            'report_time' => 'required',
            'plant' => 'required|string',
            'machine_name' => 'required|string',
            'damaged_part' => 'required|string',
            'improvement_parameters' => 'required|string',
            'kerusakan_detail' => 'required|string',
            'priority' => 'nullable',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('work_orders', 'public');
        }

        $dateCode = date('Ymd');
        $prefix = 'engIO-' . $dateCode . '-';
        $lastWorkOrder = WorkOrderEngineering::where('ticket_num', 'like', $prefix . '%')->orderBy('id', 'desc')->first();

        if ($lastWorkOrder) {
            $lastNumber = (int) substr($lastWorkOrder->ticket_num, -2);
            $newSequence = $lastNumber + 1;
        } else {
            $newSequence = 0;
        }
        $ticketNum = $prefix . sprintf('%03d', $newSequence);

        WorkOrderEngineering::create([
            'requester_id' => auth()->id(),
            'ticket_num' => $ticketNum,
            'report_date' => $request->report_date,
            'report_time' => $request->report_time,
            'plant' => $request->plant,
            'machine_name' => $request->machine_name,
            'damaged_part' => $request->damaged_part,
            'improvement_parameters' => $request->improvement_parameters,
            'kerusakan' => $request->damaged_part,
            'kerusakan_detail' => $request->kerusakan_detail,
            'priority' => $request->priority ?? 'medium',

            // Set Default Status
            'improvement_status' => 'pending',

            'photo_path' => $photoPath,
        ]);

        return redirect()->route('engineering.wo.index')->with('success', 'Laporan berhasil dibuat!');
    }

    public function update(Request $request, WorkOrderEngineering $workOrder)
    {
        // PERBAIKAN DI SINI: Sesuaikan validasi dengan input view (improvement_status)
        $request->validate([
            'improvement_status' => 'required|in:pending,in_progress,completed,cancelled',
            'finished_date' => 'nullable|date',
            'start_time' => 'required',
            'end_time' => 'nullable',
            'engineer_tech' => 'nullable|string|max:255',
            'maintenance_note' => 'nullable|string',
            'repair_solution' => 'required|string',
            'sparepart' => 'nullable|string',
        ]);

        $workOrder->update([
            // Sesuaikan mapping input ke database
            'improvement_status' => $request->improvement_status,

            'finished_date' => $request->finished_date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'engineer_tech' => $request->engineer_tech,
            'maintenance_note' => $request->maintenance_note,
            'repair_solution' => $request->repair_solution,
            'sparepart' => $request->sparepart,
        ]);

        return redirect()->route('engineering.wo.index')->with('success', 'Status laporan #' . $workOrder->ticket_num . ' berhasil diperbarui!');
    }

    public function export(Request $request)
    {
        if ($request->filled('ticket_ids')) {
            $ids = explode(',', $request->ticket_ids);
            $data = WorkOrderEngineering::with('requester')
                ->whereIn('id', $ids)
                ->orderBy('report_date', 'asc')
                ->get();
            $fileName = 'Laporan_engIO_Selected_' . date('Ymd_His') . '.csv';
        } else {
            $request->validate([
                'start_date' => 'required|date',
                'end_date'   => 'required|date|after_or_equal:start_date',
            ]);

            $startDate = $request->start_date;
            $endDate = $request->end_date;

            $data = WorkOrderEngineering::with('requester')
                ->whereBetween('report_date', [$startDate, $endDate])
                ->orderBy('report_date', 'asc')
                ->orderBy('report_time', 'asc')
                ->get();

            $fileName = 'Laporan_engIO_' . $startDate . '_sd_' . $endDate . '.csv';
        }

        $headers = array(
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        );

        $columns = [
            'No Tiket',
            'Tanggal Lapor',
            'Jam',
            'ID Pelapor',
            'Nama Pelapor',
            'Divisi Pelapor',
            'Plant',
            'Mesin',
            'Request',
            'Prioritas',
            'Status Improvement',
            'Engineer Tech',
            'Uraian Improvement',
            'Sparepart',
            'Tanggal Selesai'
        ];

        $callback = function () use ($data, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            foreach ($data as $row) {
                fputcsv($file, [
                    $row->ticket_num,
                    \Carbon\Carbon::parse($row->report_date)->format('Y-m-d'),
                    \Carbon\Carbon::parse($row->report_time)->format('H:i'),
                    $row->requester_id,
                    $row->requester->name ?? 'NO NAME',
                    $row->requester->divisi,
                    $row->plant,
                    $row->machine_name,
                    $row->damaged_part,
                    $row->priority,

                    // Pastikan export mengambil kolom improvement_status
                    $row->improvement_status,

                    $row->engineer_tech,
                    $row->repair_solution,
                    $row->sparepart,
                    $row->finished_date ? \Carbon\Carbon::parse($row->finished_date)->format('Y-m-d') : '',
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
