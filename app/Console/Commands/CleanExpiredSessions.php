<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CleanExpiredSessions extends Command
{
    protected $signature = 'sessions:clean';
    protected $description = 'Clean expired sessions from database';

    public function handle()
    {
        $lifetime = config('session.lifetime');
        $expiredTime = Carbon::now()->subMinutes($lifetime)->timestamp;
        
        $deletedSessions = DB::table('sessions')
            ->where('last_activity', '<', $expiredTime)
            ->delete();
            
        $this->info("Deleted {$deletedSessions} expired sessions.");
        
        // Also clean orphaned sessions (no user_id and older than 1 hour)
        $orphanedSessions = DB::table('sessions')
            ->whereNull('user_id')
            ->where('last_activity', '<', Carbon::now()->subHour()->timestamp)
            ->delete();
            
        $this->info("Deleted {$orphanedSessions} orphaned sessions.");
        
        return 0;
    }
}
