<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Absensi;
use App\Models\Siswa;
use App\Models\JadwalMengajar;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class GenerateTestAttendance extends Command
{
    protected $signature = 'test:generate-attendance';
    protected $description = 'Generates test attendance data for today with all status types';

    public function handle()
    {
        // First clear any existing attendance data for today to avoid unique constraint violations
        $today = Carbon::now()->format('Y-m-d');
        
        $this->info("Clearing existing attendance data for today ({$today})...");
        Absensi::where('tanggal', $today)->delete();
        $this->info("Existing attendance data cleared.");

        // Get all students
        $siswas = Siswa::take(20)->get();
        if ($siswas->isEmpty()) {
            $this->error('No students found in database!');
            return 1;
        }

        // Get a jadwal to use
        $jadwal = JadwalMengajar::first();
        if (!$jadwal) {
            $this->error('No schedules found in database!');
            return 1;
        }

        $this->info("Generating test attendance data for today...");

        // Status distribution: 10 hadir, 3 izin, 3 sakit, 4 alpha
        $statuses = [
            'hadir' => 10,
            'izin' => 3,
            'sakit' => 3,
            'alpha' => 4
        ];

        $createdCount = 0;
        $currentStatus = 'hadir';

        foreach ($siswas as $index => $siswa) {
            // Determine which status to use based on the index
            if ($index < $statuses['hadir']) {
                $currentStatus = 'hadir';
            } elseif ($index < $statuses['hadir'] + $statuses['izin']) {
                $currentStatus = 'izin';
            } elseif ($index < $statuses['hadir'] + $statuses['izin'] + $statuses['sakit']) {
                $currentStatus = 'sakit';
            } else {
                $currentStatus = 'alpha';
            }

            try {
                Absensi::create([
                    'siswa_id' => $siswa->id,
                    'jadwal_id' => $jadwal->id,
                    'guru_id' => $jadwal->guru_id,
                    'tanggal' => $today,
                    'status' => $currentStatus,
                    'keterangan' => "Test {$currentStatus} record",
                    'is_completed' => true
                ]);
                $createdCount++;
            } catch (\Exception $e) {
                $this->error("Error creating record for student ID {$siswa->id}: " . $e->getMessage());
            }
        }

        $this->info("Created {$createdCount} test attendance records for today.");
        
        // Show counts by status
        $this->info("Attendance counts by status:");
        $counts = DB::table('absensis')
                   ->select('status', DB::raw('count(*) as total'))
                   ->where('tanggal', $today)
                   ->groupBy('status')
                   ->get();

        foreach ($counts as $count) {
            $this->info("- " . ucfirst($count->status) . ": " . $count->total);
        }

        return 0;
    }
}
