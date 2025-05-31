<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PelajaranTemplateExport implements FromArray, WithHeadings, WithColumnFormatting, WithStyles
{
    public function array(): array
    {
        // Example data rows
        return [
            [
                'Bahasa Indonesia',
                'BIN',
                'Pelajaran Bahasa Indonesia untuk semua tingkatan'
            ],
            [
                'Matematika',
                'MTK',
                'Pelajaran Matematika dasar'
            ]
        ];
    }

    public function headings(): array
    {
        return [
            'nama_pelajaran',
            'kode_pelajaran',
            'deskripsi'
        ];
    }

    public function columnFormats(): array
    {
        return [
            'A' => NumberFormat::FORMAT_TEXT,
            'B' => NumberFormat::FORMAT_TEXT,
            'C' => NumberFormat::FORMAT_TEXT,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Style headers
        $sheet->getStyle('A1:C1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => '000000'],
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'CCCCCC']
            ]
        ]);

        // Add column comments/instructions
        $sheet->getComment('A1')->getText()->createTextRun('Nama mata pelajaran (Wajib)');
        $sheet->getComment('B1')->getText()->createTextRun('Kode mata pelajaran (Opsional)');
        $sheet->getComment('C1')->getText()->createTextRun('Deskripsi mata pelajaran (Opsional)');

        // Auto-size columns
        foreach (range('A', 'C') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        return [
            1 => ['font' => ['bold' => true]]
        ];
    }
}
