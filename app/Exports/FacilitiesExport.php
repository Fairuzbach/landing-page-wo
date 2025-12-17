<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class FacilitiesExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles, WithTitle
{
    protected $workOrders;

    public function __construct($workOrders)
    {
        $this->workOrders = $workOrders;
    }

    public function collection()
    {
        return $this->workOrders;
    }

    public function headings(): array
    {
        return [
            'Ticket No',
            'Date',
            'Requester',
            'Plant',
            'Machine',
            'Category',
            'Description',
            'Status',
            'Technicians (PIC)',
            'Start Date',
            'Completion Date'
        ];
    }

    public function map($wo): array
    {
        // Gabungkan nama teknisi jadi satu string dipisah koma
        $techNames = $wo->technicians->pluck('name')->implode(', ');

        return [
            $wo->ticket_num,
            $wo->report_date,
            $wo->requester_name,
            $wo->plant,
            $wo->machine->name ?? '-',
            $wo->category,
            $wo->description,
            strtoupper(str_replace('_', ' ', $wo->status)),
            $techNames ?: '-', // Jika kosong isi strip
            $wo->start_date,
            $wo->actual_completion_date
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Define border style
        $borderStyle = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ];

        // Header styling
        $headerStyle = array_merge($borderStyle, [
            'font' => [
                'bold' => true,
                'size' => 12,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '1E3A5F'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
        ]);

        // Data row styling
        $dataStyle = array_merge($borderStyle, [
            'font' => [
                'size' => 11,
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_TOP,
                'wrapText' => true,
            ],
        ]);

        // Get the highest row with data
        $highestRow = $sheet->getHighestRow();

        // Apply header style to first row
        $sheet->getStyle('1:1')->applyFromArray($headerStyle);

        // Apply alternating row colors for data rows
        for ($row = 2; $row <= $highestRow; $row++) {
            if ($row % 2 == 0) {
                // Even rows - light gray background
                $sheet->getStyle($row . ':' . $row)->applyFromArray(array_merge($dataStyle, [
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'F3F4F6'],
                    ],
                ]));
            } else {
                // Odd rows - white background
                $sheet->getStyle($row . ':' . $row)->applyFromArray($dataStyle);
            }
        }

        // Set row height for header
        $sheet->getRowDimension(1)->setRowHeight(25);

        // Set column widths
        $sheet->getColumnDimension('A')->setWidth(15);
        $sheet->getColumnDimension('B')->setWidth(12);
        $sheet->getColumnDimension('C')->setWidth(15);
        $sheet->getColumnDimension('D')->setWidth(12);
        $sheet->getColumnDimension('E')->setWidth(15);
        $sheet->getColumnDimension('F')->setWidth(12);
        $sheet->getColumnDimension('G')->setWidth(25);
        $sheet->getColumnDimension('H')->setWidth(15);
        $sheet->getColumnDimension('I')->setWidth(18);
        $sheet->getColumnDimension('J')->setWidth(12);
        $sheet->getColumnDimension('K')->setWidth(12);

        return [];
    }

    public function title(): string
    {
        return 'Facilities Work Orders';
    }
}
