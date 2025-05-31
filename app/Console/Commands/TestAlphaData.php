<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Absensi;
use App\Models\Siswa;
use App\Models\JadwalMengajar;
use Carbon\Carbon;

class TestAlphaData extends Command
{
    protected $signature = 'test:alpha-data';
    protected $description = 'Creates test records with alpha/alpa status for testing';

    public function handle()
    {
        // Get a sample student
        $siswa = Siswa::first();
        if (!$siswa) {
            $this->error('No students found in database!');
            return 1;
        }

        // Get a sample schedule
        $jadwal = JadwalMengajar::first();
        if (!$jadwal) {
            $this->error('No schedules found in database!');
            return 1;
        }

        $today = Carbon::now()->format('Y-m-d');

        // Create an 'alpha' attendance record
        Absensi::create([
            'siswa_id' => $siswa->id,
            'jadwal_id' => $jadwal->id,
            'guru_id' => $jadwal->guru_id,
            'tanggal' => $today,
            'status' => 'alpha',
            'keterangan' => 'Test alpha record',
            'is_completed' => true
        ]);
        
        $this->info("Created test record with 'alpha' status");

        // Check the status in the database after creation
        $record = Absensi::latest()->first();
        $this->info("Status in database: {$record->status}");

        return 0;
    }
}
