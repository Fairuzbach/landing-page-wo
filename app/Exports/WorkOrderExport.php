<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping; // Tambahan untuk format data
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Carbon\Carbon;

class WorkOrderExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $data;

    // 1. TERIMA DATA DARI CONTROLLER (CONSTRUCTOR)
    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        return $this->data;
    }

    // 2. HEADER KOLOM
    public function headings(): array
    {
        return [
            'ID TIKET',
            'PEMOHON',
            'DIVISI PELAPOR',
            'LOKASI',
            'DEPARTEMEN',
            'PARAMETER',
            'STATUS',
            'STATUS PERMINTAAN',
            'TANGGAL DIBUAT',
            'TANGGAL TARGET',
            'TANGGAL SELESAI',
        ];
    }

    // 3. FORMAT DATA (MAPPING) - Logic formatting dipindah kesini
    public function map($ticket): array
    {
        // Handle User
        $user = $ticket->user;
        $namaPemohon = $user ? $user->name : ($ticket->requester_name ?? '-');
        $divisiPemohon = $user ? ($user->divisi ?? '-') : '-';

        // Format Tanggal Indonesia
        $tglTarget  = $ticket->target_completion_date ? Carbon::parse($ticket->target_completion_date)->locale('id')->isoFormat('DD MMMM YYYY') : '-';
        $tglSelesai = $ticket->actual_completion_date ? Carbon::parse($ticket->actual_completion_date)->locale('id')->isoFormat('DD MMMM YYYY') : '-';
        $tglDibuat  = $ticket->created_at ? Carbon::parse($ticket->created_at)->locale('id')->isoFormat('DD MMMM YYYY') : '-';

        return [
            $ticket->ticket_num,
            $namaPemohon,
            $divisiPemohon,
            $ticket->plant,
            $ticket->department,
            $ticket->parameter_permintaan ?? $ticket->category,
            strtoupper(str_replace('_', ' ', $ticket->status)), // Status Uppercase
            $ticket->status_permintaan,
            $tglDibuat,
            $tglTarget,
            $tglSelesai,
        ];
    }

    // 4. STYLING (WARNA KUNING & BORDER)
    public function styles(Worksheet $sheet)
    {
        $lastRow = $sheet->getHighestRow();

        return [
            // Style Header (Baris 1)
            1 => [
                'font' => ['bold' => true, 'color' => ['argb' => '000000']], // Teks Hitam Bold
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FFFF00'], // Background Kuning Caterpillar
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                ],
            ],

            // Style Seluruh Tabel (Border Tipis)
            'A1:K' . $lastRow => [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['argb' => '000000'],
                    ],
                ],
            ],
        ];
    }
}
