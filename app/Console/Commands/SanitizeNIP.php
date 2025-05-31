<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SanitizeNIP extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:sanitize-nip';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sanitizes empty NIP values to NULL in the gurus table';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Get count of records with empty NIP
        $emptyCount = DB::table('gurus')->where('nip', '')->count();
        $this->info("Found {$emptyCount} records with empty NIP values.");
        
        // Update all empty NIP values to NULL
        $updated = DB::table('gurus')->where('nip', '')->update(['nip' => null]);
        $this->info("Updated {$updated} records with NULL values.");
        
        $this->info("NIP sanitization completed successfully.");
    }
}
