<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Absensi;
use App\Models\Siswa;
use App\Models\JadwalMengajar;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AddAlphaAttendance extends Command
{
    protected $signature = 'test:add-alpha';
    protected $description = 'Adds attendance records with Alpha status for today';

    public function handle()
    {
        $today = Carbon::now()->format('Y-m-d');
        
        // Get one student
        $siswa = Siswa::first();
        if (!$siswa) {
            $this->error('No students found in database!');
            return 1;
        }

        // Get one jadwal
        $jadwal = JadwalMengajar::first();
        if (!$jadwal) {
            $this->error('No schedules found in database!');
            return 1;
        }

        // Delete any existing attendance for this student and jadwal today
        Absensi::where('siswa_id', $siswa->id)
               ->where('jadwal_id', $jadwal->id)
               ->where('tanggal', $today)
               ->delete();

        // Create a new alpha attendance record
        try {
            Absensi::create([
                'siswa_id' => $siswa->id,
                'jadwal_id' => $jadwal->id,
                'guru_id' => $jadwal->guru_id,
                'tanggal' => $today,
                'status' => 'alpha', // Use 'alpha' since this is what we expect based on the migration
                'keterangan' => "Test alpha attendance",
                'is_completed' => true
            ]);
            $this->info("Created alpha attendance record for student ID {$siswa->id}");
        } catch (\Exception $e) {
            $this->error("Error creating record: " . $e->getMessage());
            return 1;
        }

        // Show counts by status for today
        $this->info("Current attendance counts for today:");
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
