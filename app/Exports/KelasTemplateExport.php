<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Collection;

class KelasTemplateExport implements FromCollection, WithHeadings, WithMapping
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        // Return sample data as template
        return new Collection([
            (object)[
                'nama_kelas' => '7A',
                'tingkat' => '7',
                'jurusan' => '',
                'nip_wali_kelas' => '',
            ],
            (object)[
                'nama_kelas' => '8A',
                'tingkat' => '8',
                'jurusan' => 'IPA',
                'nip_wali_kelas' => '',
            ]
        ]);
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'nama_kelas',
            'tingkat',
            'jurusan',
            'nip_wali_kelas',
        ];
    }

    /**
     * @param mixed $row
     * @return array
     */
    public function map($row): array
    {
        return [
            $row->nama_kelas,
            $row->tingkat,
            $row->jurusan,
            $row->nip_wali_kelas,
        ];
    }
}
