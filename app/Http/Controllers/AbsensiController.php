<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Absensi;
use App\Models\AbsensiDetail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class AbsensiController extends Controller
{    /**
     * Record student attendance
     */
    public function recordAttendance($siswa, $jadwal, $status, $minutesLate = 0, $isValidAttendance = true)
    {
        Log::info('Recording attendance for student', [
            'student_id' => $siswa->id,
            'schedule_id' => $jadwal->id,
            'status' => $status,
            'minutes_late' => $minutesLate,
            'is_valid' => $isValidAttendance
        ]);
        
        try {
            // Begin transaction
            DB::beginTransaction();
            
            // Find or create attendance record for today
            $absensi = Absensi::firstOrCreate(
                [
                    'tanggal' => now()->toDateString(),
                    'jadwal_id' => $jadwal->id,
                    'siswa_id' => $siswa->id,
                ],
                [
                    'guru_id' => $jadwal->guru_id,
                    'status' => $status,
                    'minutes_late' => $minutesLate,
                    'is_valid_attendance' => $isValidAttendance,
                    'keterangan' => $this->getStatusKeterangan($status, $minutesLate),
                    'is_completed' => true
                ]
            );
            
            // Create attendance detail record
            $absensiDetail = AbsensiDetail::create([
                'absensi_id' => $absensi->id,
                'siswa_id' => $siswa->id,
                'status' => $status,
                'minutes_late' => $minutesLate,
                'is_valid_attendance' => $isValidAttendance,
                'keterangan' => $this->getStatusKeterangan($status, $minutesLate),
                'waktu_absen' => now()
            ]);
            
            DB::commit();
            
            Log::info('Attendance recorded successfully', [
                'attendance_id' => $absensi->id,
                'detail_id' => $absensiDetail->id
            ]);
            
            return [
                'success' => true,
                'absensi' => $absensi,
                'detail' => $absensiDetail
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to record attendance: ' . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Generate status description
     */
    private function getStatusKeterangan($status, $minutesLate)
    {
        if ($status === 'hadir' && $minutesLate == 0) {
            return 'Hadir tepat waktu';
        }
        
        if ($status === 'terlambat') {
            return "Terlambat $minutesLate menit";
        }
        
        return ucfirst($status);
    }
}
