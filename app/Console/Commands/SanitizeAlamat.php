<?php

namespace App\Console\Commands;

use App\Models\Guru;
use Illuminate\Console\Command;

class SanitizeAlamat extends Command
{
    protected $signature = 'app:sanitize-alamat';
    protected $description = 'Sanitize alamat values in the gurus table';

    public function handle(): int
    {
        $nullCount = Guru::whereNull('alamat')->count();
        $emptyCount = Guru::where('alamat', '')->count();
        
        $totalToFix = $nullCount + $emptyCount;
        
        if ($totalToFix > 0) {
            Guru::whereNull('alamat')->update(['alamat' => '-']);
            Guru::where('alamat', '')->update(['alamat' => '-']);
            
            $this->info("Fixed $totalToFix records with NULL or empty alamat values.");
        } else {
            $this->info("No records with NULL or empty alamat values found.");
        }
        
        return Command::SUCCESS;
    }
}
