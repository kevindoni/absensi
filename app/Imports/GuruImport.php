<?php

namespace App\Imports;

use App\Models\Guru;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Validators\Failure;

class GuruImport implements ToModel, WithHeadingRow, WithValidation, SkipsEmptyRows, SkipsOnFailure
{
    use Importable;

    /**
     * @var array
     */
    public $failures = [];    /**
     * Normalisasi jenis kelamin
     */
    private function normalizeGender($value)
    {
        if (empty($value)) return 'L';
        $value = strtoupper(trim($value));
        // Strip any non-letter characters first
        $value = preg_replace('/[^A-Z]/', '', $value);
        return in_array($value, ['L', 'P']) ? $value : 'L';
    }

    /**
     * Membersihkan format nomor
     */
    private function cleanNumber($value)
    {
        if (empty($value)) {
            return null;
        }
        $clean = preg_replace('/[^0-9]/', '', $value);
        return !empty($clean) ? $clean : null;
    }

    /**
     * Mapping data from Excel to database format
     */    private function mapData(array $row)
    {
        // Handle NIP - can come from nip or nip_opsional
        $nip = isset($row['nip']) ? trim((string)$row['nip']) : null;
        if (!$nip && isset($row['nip_opsional'])) {
            $nip = !empty(trim($row['nip_opsional'])) && $row['nip_opsional'] !== '0' ? trim((string)$row['nip_opsional']) : null;
        }

        $username = isset($row['username']) && !empty(trim($row['username'])) 
            ? trim($row['username']) 
            : Str::slug($row['nama_lengkap'] ?? '') . '-' . Str::random(5);

        // Handle phone number from no_telp or no_telepon
        $phoneNumber = isset($row['no_telepon']) ? $row['no_telepon'] : (isset($row['no_telp']) ? $row['no_telp'] : null);

        // Handle gender from jenis_kelamin or jenis_kelamin_lp
        $gender = isset($row['jenis_kelamin_lp']) ? $row['jenis_kelamin_lp'] : (isset($row['jenis_kelamin']) ? $row['jenis_kelamin'] : 'L');

        return [            
            'username'      => $username,
            'nip'          => $nip,
            'nama_lengkap' => trim($row['nama_lengkap'] ?? ''),            
            'email'        => strtolower(trim($row['email'] ?? '')),
            'password'     => bcrypt($row['password'] ?? 'password123'),
            'jenis_kelamin'=> $this->normalizeGender($gender),
            'no_telp'      => $this->cleanNumber($phoneNumber),
            'alamat'       => $row['alamat'] ?? '-', // Provide default value for required field
        ];
    }    public function model(array $row)
    {
        try {
            // Map the data using the consolidated mapping function
            $data = $this->mapData($row);
            
            // Create and save the Guru model
            $guru = new Guru($data);
            $guru->save();
            
            return $guru;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function rules(): array
    {
        return [
            '*.username' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('gurus', 'username')
            ],            
            '*.nip' => [
                'nullable', // Changed from present to nullable
                'string',
                'max:20',
                Rule::unique('gurus', 'nip')->whereNotNull('nip')
            ],
            '*.nama_lengkap' => 'required|string|max:100',
            '*.email' => [
                'required',
                'email',
                'max:100',
                Rule::unique('gurus', 'email')
            ],            '*.jenis_kelamin' => 'nullable|string|in:L,P,l,p',
            '*.no_telp' => 'nullable|string|max:20',
            '*.alamat' => 'required|string|max:255'
        ];
    }

    /**
     * Custom validation messages
     */
    public function customValidationMessages()
    {
        return [
            'username.unique' => 'Username ":input" sudah digunakan',
            'email.unique' => 'Email ":input" sudah terdaftar',
            'nip.unique' => 'NIP ":input" sudah terdaftar',
            'jenis_kelamin.in' => 'Jenis kelamin harus L atau P',
        ];
    }

    /**
     * Handle failures
     */    public function onFailure(Failure ...$failures)
    {
        $this->failures = $failures;
    }
}