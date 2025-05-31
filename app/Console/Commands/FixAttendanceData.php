<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Absensi;

class FixAttendanceData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:fix-attendance-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix inconsistencies in attendance data';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting attendance data fix...');
        
        // 1. Get the total count of attendance records
        $totalCount = DB::table('absensis')->count();
        $this->info("Total attendance records: {$totalCount}");
        
        // 2. Display the distinct status values currently in use
        $statuses = DB::table('absensis')->select('status')->distinct()->pluck('status')->toArray();
        $this->info("Current distinct status values: " . implode(', ', $statuses));
        
        // 3. Update any 'alpa' values to 'alpha' (based on enum)
        $alpaCount = DB::table('absensis')->where('status', 'alpa')->count();
        
        if ($alpaCount > 0) {
            $this->info("Found {$alpaCount} records with 'alpa' status.");
            DB::statement('UPDATE absensis SET status = "alpha" WHERE status = "alpa"');
            $this->info("Updated {$alpaCount} records from 'alpa' to 'alpha'.");
        } else {
            $this->info("No records found with 'alpa' status.");
        }
        
        // 4. Convert all status values to lowercase for consistency
        $this->info("Normalizing case for all status values...");
        foreach (['Hadir', 'HADIR', 'Izin', 'IZIN', 'Sakit', 'SAKIT', 'Alpha', 'ALPHA'] as $status) {
            $count = DB::table('absensis')->where('status', $status)->count();
            if ($count > 0) {
                $lowerStatus = strtolower($status);
                DB::statement("UPDATE absensis SET status = '{$lowerStatus}' WHERE status = '{$status}'");
                $this->info("Updated {$count} records from '{$status}' to '{$lowerStatus}'.");
            }
        }
        
        // 5. Final check
        $newStatuses = DB::table('absensis')->select('status')->distinct()->pluck('status')->toArray();
        $this->info("After fix, distinct status values: " . implode(', ', $newStatuses));
        
        // 6. Verify the status counts
        $hadirCount = DB::table('absensis')->where('status', 'hadir')->count();
        $izinCount = DB::table('absensis')->where('status', 'izin')->count();
        $sakitCount = DB::table('absensis')->where('status', 'sakit')->count();
        $alphaCount = DB::table('absensis')->where('status', 'alpha')->count();
        
        $this->info("Final status counts:");
        $this->info("- hadir: {$hadirCount}");
        $this->info("- izin: {$izinCount}");
        $this->info("- sakit: {$sakitCount}");
        $this->info("- alpha: {$alphaCount}");
        $this->info("- Total: " . ($hadirCount + $izinCount + $sakitCount + $alphaCount));
        
        $this->info('Attendance data fix completed successfully.');
    }
}
