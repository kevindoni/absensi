<?php

namespace App\Imports;

use App\Models\Pelajaran;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Validators\Failure;

class PelajaranImport implements ToModel, WithHeadingRow, WithValidation, SkipsEmptyRows, SkipsOnFailure
{
    use Importable;

    private $rowCount = 0;
    public $failures = [];

    public function model(array $row)
    {
        $this->rowCount++;

        return new Pelajaran([
            'nama_pelajaran' => $row['nama_pelajaran'],
            'kode_pelajaran' => $row['kode_pelajaran'] ?? null,
            'deskripsi' => $row['deskripsi'] ?? null,
        ]);
    }

    public function rules(): array
    {
        return [
            'nama_pelajaran' => 'required|string|max:255',
            'kode_pelajaran' => 'nullable|string|max:20|unique:pelajaran,kode_pelajaran',
            'deskripsi' => 'nullable|string',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'nama_pelajaran.required' => 'Nama pelajaran wajib diisi.',
            'nama_pelajaran.max' => 'Nama pelajaran maksimal 255 karakter.',
            'kode_pelajaran.max' => 'Kode pelajaran maksimal 20 karakter.',
            'kode_pelajaran.unique' => 'Kode pelajaran sudah digunakan.',
        ];
    }

    public function onFailure(Failure ...$failures)
    {
        $this->failures = array_merge($this->failures, $failures);
    }

    public function getRowCount()
    {
        return $this->rowCount;
    }
}
