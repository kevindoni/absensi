<?php

namespace App\Imports;

use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\AcademicYear;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SiswaImport implements ToModel, WithHeadingRow, WithValidation, WithCustomCsvSettings
{
    private $successCount = 0;
    private $errorCount = 0;
    private $currentRow = 0;
    private $academicYear;
    private $batchSize = 100;
    private $currentBatch = 1;
    private $kelasCache = [];

    public function __construct($academicYear = null)
    {
        $this->academicYear = $academicYear ?? AcademicYear::where('is_active', true)->first();
        \Log::info('Initializing student import process', [
            'academic_year' => $this->academicYear ? $this->academicYear->year : 'No active year'
        ]);

        // Cache all classes for the academic year
        if ($this->academicYear) {
            $this->kelasCache = Kelas::where('academic_year_id', $this->academicYear->id)
                                   ->pluck('id', 'nama_kelas')
                                   ->toArray();
        }
    }

    public function getSuccessCount()
    {
        return $this->successCount;
    }

    public function getErrorCount()
    {
        return $this->errorCount;
    }

    public function model(array $row)
    {
        $this->currentRow++;
        
        try {
            \Log::debug("Processing row #{$this->currentRow}", [
                'nisn' => $row['nisn'] ?? 'N/A',
                'nama' => $row['nama_siswa'] ?? 'N/A',
                'kelas' => $row['kelas'] ?? 'N/A',
                'jenis_kelamin' => $row['jenis_kelamin'] ?? 'N/A'
            ]);
            
            // Get active academic year
            $academicYear = $this->academicYear ?? AcademicYear::where('is_active', true)->first();
            if (!$academicYear) {
                throw new \Exception('Tidak ada tahun akademik yang aktif');
            }

            // Find the class
            $kelasName = $this->normalizeKelasName($row['kelas']);
            $kelas = Kelas::where('nama_kelas', $kelasName)
                         ->where('academic_year_id', $academicYear->id)
                         ->first();

            if (!$kelas) {
                throw new \Exception("Kelas '{$row['kelas']}' tidak ditemukan dalam tahun ajaran aktif");
            }

            // Convert birth date from Excel
            $tanggalLahir = null;
            if (!empty($row['tanggal_lahir'])) {
                if (is_numeric($row['tanggal_lahir'])) {
                    $tanggalLahir = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject((int)$row['tanggal_lahir']);
                } else {
                    $tanggalLahir = \Carbon\Carbon::parse($row['tanggal_lahir']);
                }
            }

            // Create student record
            $siswa = new Siswa([
                'nisn' => (string)$row['nisn'],
                'nama_lengkap' => trim($row['nama_siswa']),
                'kelas_id' => $kelas->id,
                'academic_year_id' => $academicYear->id,
                'password' => Hash::make($row['password']),
                'jenis_kelamin' => strtoupper($row['jenis_kelamin']),
                'tanggal_lahir' => $tanggalLahir,
                'alamat' => trim($row['alamat'] ?? ''),
                'qr_token' => Str::random(40) . time(),
            ]);

            $this->successCount++;
            
            \Log::debug("Successfully processed student", [
                'row' => $this->currentRow,
                'nisn' => $row['nisn'],
                'nama' => $row['nama_siswa'],
                'kelas' => $kelasName,
                'academic_year' => $academicYear->year,
                'jenis_kelamin' => strtoupper($row['jenis_kelamin'])
            ]);

            if ($this->successCount % $this->batchSize === 0) {
                \Log::info("Batch #{$this->currentBatch} completed", [
                    'success_count' => $this->successCount,
                    'error_count' => $this->errorCount,
                    'last_nisn' => $row['nisn']
                ]);
                $this->currentBatch++;
            }

            return $siswa;

        } catch (\Exception $e) {
            $this->errorCount++;
            \Log::error("Error processing row #{$this->currentRow}", [
                'row_data' => $row,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Normalize class name format
     */
    private function normalizeKelasName($kelasName)
    {
        // Remove any spaces between number and letter
        $normalized = preg_replace('/\s+/', '', trim($kelasName));
        
        // Ensure consistent format (e.g., "7A" instead of "7-A")
        $normalized = str_replace('-', '', $normalized);
        
        return strtoupper($normalized);
    }

    /**
     * Normalize column headers to handle variations in the header names
     */
    public function chunkSize(): int
    {
        return 100;
    }

    /**
     * Skip the description row
     */
    public function startRow(): int
    {
        return 3; // Start from row 3 (after description and header rows)
    }

    /**
     * Define which row is the header row
     */
    public function headingRow(): int
    {
        return 1;
    }

    /**
     * Map Excel columns to the expected format
     */
    public function map($row): array
    {
        // Clean and normalize the data
        $data = array_map(function ($value) {
            return is_string($value) ? trim($value) : $value;
        }, $row);

        // Skip empty rows
        if (empty(array_filter($data))) {
            return [];
        }

        return [
            'nisn' => $data['nisn'] ?? null,
            'nama_siswa' => $data['nama_siswa'] ?? null,
            'kelas' => isset($data['kelas']) ? $this->normalizeKelasName($data['kelas']) : null,
            'jenis_kelamin' => isset($data['jenis_kelamin']) ? strtoupper($data['jenis_kelamin']) : null,
            'tanggal_lahir' => $data['tanggal_lahir'] ?? null,
            'alamat' => $data['alamat'] ?? null,
            'password' => $data['password'] ?? null,
        ];
    }

    public function rules(): array
    {
        return [
            'nisn' => [
                'required',
                function ($attribute, $value, $fail) {
                    $stringValue = (string)$value;
                    if (strlen($stringValue) < 8 || strlen($stringValue) > 20) {
                        $fail("NISN harus memiliki panjang antara 8 sampai 20 karakter.");
                    }
                    if (Siswa::where('nisn', $stringValue)->exists()) {
                        $fail("NISN $stringValue sudah terdaftar dalam sistem.");
                    }
                }
            ],
            'nama_siswa' => 'required|string|max:255',
            'kelas' => [
                'required',
                function ($attribute, $value, $fail) use (&$row) {
                    if (!$this->academicYear) {
                        $fail("Tidak ada tahun akademik yang aktif.");
                        return;
                    }

                    $normalizedKelas = $this->normalizeKelasName($value);
                    if (!array_key_exists($normalizedKelas, $this->kelasCache)) {
                        $availableClasses = implode(', ', array_keys($this->kelasCache));
                        $fail("Kelas '$value' tidak ditemukan dalam tahun ajaran aktif. Kelas yang tersedia: $availableClasses");
                    }
                }
            ],
            'password' => [
                'required',
                function ($attribute, $value, $fail) {
                    // Convert to string if numeric
                    $stringValue = is_numeric($value) ? (string)$value : $value;
                    
                    if (!is_string($stringValue)) {
                        $fail("Password harus berupa teks.");
                        return;
                    }

                    if (strlen($stringValue) < 6) {
                        $fail("Password minimal harus 6 karakter.");
                    }
                }
            ],
            'jenis_kelamin' => [
                'required',
                function ($attribute, $value, $fail) {
                    $gender = strtoupper((string)$value);
                    if (!in_array($gender, ['L', 'P'])) {
                        $fail("Jenis kelamin harus 'L' atau 'P'.");
                    }
                }
            ],
            'tanggal_lahir' => 'nullable|date',
            'alamat' => 'nullable|string'
        ];
    }

    /**
     * @return array
     */
    public function customValidationMessages()
    {
        return [
            'nisn.required' => 'NISN wajib diisi.',
            'nama_siswa.required' => 'Nama siswa wajib diisi.',
            'nama_siswa.string' => 'Nama siswa harus berupa teks.',
            'nama_siswa.max' => 'Nama siswa maksimal 255 karakter.',
            'kelas.required' => 'Kelas wajib diisi.',
            'password.required' => 'Password wajib diisi.',
            'jenis_kelamin.required' => 'Jenis kelamin wajib diisi.',
            'tanggal_lahir.date' => 'Format tanggal lahir tidak valid.',
        ];
    }

    public function getCsvSettings(): array
    {
        return [
            'delimiter' => ',',
            'enclosure' => '"',
            'input_encoding' => 'UTF-8'
        ];
    }
}
