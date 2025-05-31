<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MonitorSessions extends Command
{
    protected $signature = 'sessions:monitor {--interval=30 : Refresh interval in seconds}';
    protected $description = 'Monitor active sessions in real-time';

    public function handle()
    {
        $interval = (int) $this->option('interval');
        
        $this->info("🔍 Monitoring sessions every {$interval} seconds. Press Ctrl+C to stop.");
        $this->info("=".str_repeat("=", 70));
        
        while (true) {
            $this->displaySessionStats();
            sleep($interval);
        }
    }
    
    private function displaySessionStats()
    {
        $currentTime = Carbon::now();
        $sessionLifetime = config('session.lifetime');
        $expiredTime = $currentTime->copy()->subMinutes($sessionLifetime)->timestamp;
        
        // Get session statistics
        $totalSessions = DB::table('sessions')->count();
        $activeSessions = DB::table('sessions')
            ->where('last_activity', '>', $expiredTime)
            ->count();
        $authenticatedSessions = DB::table('sessions')
            ->whereNotNull('user_id')
            ->where('last_activity', '>', $expiredTime)
            ->count();
        $guestSessions = $activeSessions - $authenticatedSessions;
        
        // Get recent activity
        $recentSessions = DB::table('sessions')
            ->select('id', 'user_id', 'ip_address', 'last_activity', 'payload')
            ->orderBy('last_activity', 'desc')
            ->limit(5)
            ->get();
        
        // Clear screen (works on most terminals)
        $this->info("\033[2J\033[H");
        
        // Header
        $this->info("🔍 Session Monitor - " . $currentTime->format('Y-m-d H:i:s'));
        $this->info("=".str_repeat("=", 70));
        
        // Statistics
        $this->table(
            ['Metric', 'Count'],
            [
                ['Total Sessions', $totalSessions],
                ['Active Sessions', $activeSessions],
                ['Authenticated Sessions', $authenticatedSessions],
                ['Guest Sessions', $guestSessions],
                ['Session Lifetime', $sessionLifetime . ' minutes'],
            ]
        );
        
        // Recent activity
        if ($recentSessions->count() > 0) {
            $this->info("\n📊 Recent Session Activity:");
            $this->info("-".str_repeat("-", 70));
            
            $tableData = [];
            foreach ($recentSessions as $session) {
                $payload = json_decode(base64_decode($session->payload), true);
                $lastActivity = Carbon::createFromTimestamp($session->last_activity);
                $userInfo = $session->user_id ? "User #{$session->user_id}" : "Guest";
                
                // Try to extract guard info from payload
                $guard = 'Unknown';
                if (isset($payload['_auth_guard'])) {
                    $guard = $payload['_auth_guard'];
                } elseif (isset($payload['login_admin_59ba36addc2b2f9401580f014c7f58ea4e30989d'])) {
                    $guard = 'admin';
                } elseif (isset($payload['login_guru_59ba36addc2b2f9401580f014c7f58ea4e30989d'])) {
                    $guard = 'guru';
                } elseif (isset($payload['login_siswa_59ba36addc2b2f9401580f014c7f58ea4e30989d'])) {
                    $guard = 'siswa';
                } elseif (isset($payload['login_orangtua_59ba36addc2b2f9401580f014c7f58ea4e30989d'])) {
                    $guard = 'orangtua';
                }
                
                $tableData[] = [
                    substr($session->id, 0, 12) . '...',
                    $userInfo,
                    $guard,
                    $session->ip_address,
                    $lastActivity->diffForHumans(),
                ];
            }
            
            $this->table(
                ['Session ID', 'User', 'Guard', 'IP', 'Last Activity'],
                $tableData
            );
        }
        
        // Warning for potential issues
        if ($activeSessions > 100) {
            $this->warn("⚠️  High number of active sessions detected!");
        }
        
        if ($totalSessions > 1000) {
            $this->warn("⚠️  Consider running 'php artisan sessions:clean' to remove old sessions");
        }
        
        $this->info("\n💡 Commands: sessions:clean | sessions:monitor --interval=X");
    }
}
