<?php

namespace App\Exports;

use App\Models\Kelas;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class SiswaTemplateExport implements FromCollection, WithHeadings, WithTitle, WithStyles
{
    public function collection()
    {
        \Log::info('Generating student import template');
        return collect([
            // Example data
            [
                'nisn' => '12345678',
                'nama_siswa' => 'John Doe',
                'kelas' => '7A',
                'jenis_kelamin' => 'L',
                'tanggal_lahir' => '2010-01-01',
                'alamat' => 'Jl. Example No. 1',
                'password' => 'siswa123'
            ]
        ])->map(function ($row) {
            return array_values($row);
        });
    }

    public function headings(): array
    {                    
        return [
            'nisn',             // Simple column names for reliable import
            'nama_siswa',
            'kelas',
            'jenis_kelamin',
            'tanggal_lahir',
            'alamat',
            'password'
        ];
    }

    public function title(): string
    {
        return 'Data Siswa';
    }

    public function styles(Worksheet $sheet)
    {
        // Add helpful comments to cells
        $sheet->getComment('A1')->getText()->createTextRun('NISN: 8-20 digit (Wajib)');
        $sheet->getComment('B1')->getText()->createTextRun('Nama lengkap siswa (Wajib)');
        $sheet->getComment('C1')->getText()->createTextRun('Format: 7A, 8B, dll (Wajib)');
        $sheet->getComment('D1')->getText()->createTextRun('L = Laki-laki, P = Perempuan (Wajib)');
        $sheet->getComment('E1')->getText()->createTextRun('Format: YYYY-MM-DD');
        $sheet->getComment('F1')->getText()->createTextRun('Alamat lengkap (Opsional)');
        $sheet->getComment('G1')->getText()->createTextRun('Minimal 6 karakter (Wajib)');

        // Style headers
        $sheet->getStyle('A1:G1')->applyFromArray([
            'font' => ['bold' => true],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'color' => ['rgb' => 'CCFFCC']
            ],
            'borders' => [
                'bottom' => ['borderStyle' => Border::BORDER_THIN]
            ]
        ]);

        // Style example data
        $sheet->getStyle('A2:G2')->applyFromArray([
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'color' => ['rgb' => 'FFFFCC']
            ]
        ]);

        // Add data validation for gender
        $sheet->getDataValidation('D2:D1000')->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST)
            ->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_STOP)
            ->setAllowBlank(false)
            ->setShowInputMessage(true)
            ->setShowErrorMessage(true)
            ->setShowDropDown(true)
            ->setErrorTitle('Input Error')
            ->setError('Pilih "L" atau "P"')
            ->setPromptTitle('Jenis Kelamin')
            ->setPrompt('L = Laki-laki, P = Perempuan')
            ->setFormula1('"L,P"');

        // Set column widths
        $sheet->getColumnDimension('A')->setWidth(15);  // NISN
        $sheet->getColumnDimension('B')->setWidth(30);  // Nama
        $sheet->getColumnDimension('C')->setWidth(15);  // Kelas
        $sheet->getColumnDimension('D')->setWidth(15);  // Jenis Kelamin
        $sheet->getColumnDimension('E')->setWidth(20);  // Tanggal Lahir
        $sheet->getColumnDimension('F')->setWidth(40);  // Alamat
        $sheet->getColumnDimension('G')->setWidth(20);  // Password

        // Freeze the header
        $sheet->freezePane('A2');

        // Add instructions below example
        $row = 4;
        $instructions = [
            'PETUNJUK PENGISIAN:',
            '- Baris dengan latar belakang kuning adalah contoh data',
            '- Masukkan data siswa mulai dari baris 3',
            '- NISN harus berupa angka dengan panjang 8-20 digit',
            '- Kelas harus sesuai dengan yang tersedia (contoh: 7A)',
            '- Jenis kelamin harus L (Laki-laki) atau P (Perempuan)',
            '- Format tanggal lahir: YYYY-MM-DD (contoh: 2010-01-31)',
            '- Password minimal 6 karakter',
            '- Kolom yang wajib diisi: NISN, Nama Siswa, Kelas, Jenis Kelamin, dan Password',
            '- Arahkan kursor ke nama kolom untuk melihat petunjuk tambahan'
        ];

        foreach ($instructions as $instruction) {
            $sheet->setCellValue("A$row", $instruction);
            $sheet->mergeCells("A$row:G$row");
            $row++;
        }

        return [
            1 => ['font' => ['bold' => true]], // Header row
        ];
    }
}
