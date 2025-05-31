<?php

namespace App\Imports;

use App\Models\Kelas;
use App\Models\AcademicYear;
use App\Models\Guru;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Validators\Failure;
use Illuminate\Validation\ValidationException;
use Log;

class KelasImport implements ToModel, WithHeadingRow, WithValidation, SkipsEmptyRows, SkipsOnFailure
{
    use Importable;

    protected $academicYearId;
    protected $failures = [];

    public function __construct($academicYearId = null)
    {
        if (!$academicYearId) {
            $activeYear = AcademicYear::where('is_active', true)->first();
            if (!$activeYear) {
                throw ValidationException::withMessages([
                    'academic_year' => 'Tidak dapat import data. Pastikan ada tahun ajaran yang aktif.'
                ]);
            }
            $this->academicYearId = $activeYear->id;
        } else {
            $this->academicYearId = $academicYearId;
        }
    }

    public function model(array $row)
    {
        try {
            $waliKelasId = null;
            if (!empty($row['nip_wali_kelas'])) {
                $guru = Guru::where('nip', $row['nip_wali_kelas'])->first();
                if ($guru) {
                    $waliKelasId = $guru->id;
                }
            }

            // Skip if this kelas already exists in this academic year
            $existingKelas = Kelas::where('nama_kelas', $row['nama_kelas'])
                ->where('academic_year_id', $this->academicYearId)
                ->first();

            if ($existingKelas) {
                Log::info("Kelas {$row['nama_kelas']} already exists for this academic year. Skipping.");
                return null;
            }            
            return new Kelas([
                'academic_year_id' => $this->academicYearId,
                'nama_kelas' => $row['nama_kelas'],
                'tingkat' => (string) $row['tingkat'], // Cast tingkat to string
                'jurusan' => $row['jurusan'] ?? null,
                'wali_kelas_id' => $waliKelasId,
                'is_active' => true
            ]);
        } catch (\Exception $e) {
            Log::error("Error importing kelas: " . $e->getMessage(), [
                'row' => $row,
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    public function rules(): array
    {
        return [
            'nama_kelas' => [
                'required',
                'string',
                'max:50',
            ],            
            'tingkat' => ['required', 'regex:/^[0-9]+$/'], // Allow numeric values
            'jurusan' => 'nullable|string|max:50',
            'nip_wali_kelas' => 'nullable|exists:gurus,nip'
        ];
    }

    public function onFailure(Failure ...$failures)
    {
        $this->failures = $failures;
        
        foreach ($failures as $failure) {
            Log::warning('Row import failed:', [
                'row' => $failure->row(),
                'attribute' => $failure->attribute(),
                'errors' => $failure->errors()
            ]);
        }
    }
}
