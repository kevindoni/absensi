<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Absensi;

class FixAbsensiStatusCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'absensi:fix-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix inconsistencies between status and keterangan in absensi records';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Starting to check for inconsistencies in absensi records...');
        
        // Find records where status is 'alpha' but keterangan contains 'terlambat'
        $inconsistentRecords = Absensi::where('status', 'alpha')
            ->where('keterangan', 'like', '%terlambat%')
            ->get();
        
        $count = $inconsistentRecords->count();
        $this->info("Found {$count} inconsistent records.");
        
        if ($count > 0) {
            $this->info("Fixing records...");
            foreach ($inconsistentRecords as $record) {
                // Extract minutes late from keterangan
                preg_match('/Terlambat (\d+[\.\d+]*) menit/', $record->keterangan, $matches);
                $minutesLate = 0;
                if (isset($matches[1])) {
                    $minutesLate = floatval($matches[1]);
                }
                
                $record->status = 'terlambat';
                $record->minutes_late = $minutesLate;
                $record->save();
                
                $this->info("Fixed record ID {$record->id} for siswa ID {$record->siswa_id}");
            }
            
            $this->info("All inconsistent records have been fixed.");
        } else {
            $this->info("No inconsistencies found. No action needed.");
        }
        
        return Command::SUCCESS;
    }
}
