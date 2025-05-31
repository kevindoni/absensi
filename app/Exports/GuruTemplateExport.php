<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class GuruTemplateExport implements FromCollection, WithHeadings, WithTitle
{
    public function collection()
    {
        return collect([
            [
                'Budi Santoso',       // nama lengkap
                '198501012010011001', // nip (opsional)
                'budisantoso',        // username
                'Password123',        // password
                'budi@email.com',     // email
                'L',                  // jenis kelamin (L/P) *wajib
                '081234567890',       // no telp (opsional)
                'Jl. Contoh No 123'   // alamat (opsional)
            ],
            [
                'Siti Aminah',        // nama lengkap
                '',                   // nip kosong
                'sitiaminah',         // username
                'Password123',        // password
                'siti@email.com',     // email
                'P',                  // jenis kelamin (L/P) *wajib
                '',                   // no telp kosong
                ''                    // alamat kosong
            ],
        ]);
    }

    public function headings(): array
    {
        return [
            'Nama Lengkap*',
            'NIP (Opsional)',
            'Username*',
            'Password*',
            'Email*',
            'Jenis Kelamin* (L/P)',
            'No. Telepon',
            'Alamat'
        ];
    }

    public function title(): string
    {
        return 'Template Import Guru';
    }
}
