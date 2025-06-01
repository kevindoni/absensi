<?php

namespace App\Console\Commands;

use App\Models\Siswa;
use App\Models\OrangTua;
use App\Services\AttendanceNotificationService;
use Illuminate\Console\Command;

class TestWhatsAppAttendance extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:whatsapp-attendance {--dry-run : Only show what would be sent without actually sending}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test WhatsApp attendance notification functionality';

    /**
     * Execute the console command.
     */
    public function handle(AttendanceNotificationService $notificationService)
    {
        $this->info('Testing WhatsApp Attendance Notification Service...');
        
        // Check if there are any students with parents
        $siswa = Siswa::with('orangTua')->whereHas('orangTua')->first();
        
        if (!$siswa) {
            $this->error('No students with parent records found in the database.');
            return 1;
        }
        
        $this->info("Found student: {$siswa->nama} (ID: {$siswa->id})");
        
        $parent = $siswa->orangTua;
        if ($parent) {
            $this->info("Parent: {$parent->nama}");
            $this->info("Parent phone: {$parent->no_hp}");
        }
        
        $dryRun = $this->option('dry-run');
        
        if ($dryRun) {
            $this->warn('DRY RUN MODE - No messages will be sent');
        }
        
        // Test different attendance statuses
        $statuses = [
            'hadir' => 'Present',
            'terlambat' => 'Late', 
            'izin' => 'Excused',
            'sakit' => 'Sick',
            'alpha' => 'Absent'
        ];
        
        foreach ($statuses as $status => $description) {
            $this->info("\nTesting status: {$description} ({$status})");
            
            try {                if ($dryRun) {
                    // Generate message without sending
                    $message = $notificationService->generateTestAttendanceMessage($siswa, $status, now());
                    $this->line("Message that would be sent:");
                    $this->line($message);                } else {
                    // Create a mock absensi record for testing
                    $mockAbsensi = new \App\Models\Absensi([
                        'siswa_id' => $siswa->id,
                        'status' => $status,
                        'tanggal' => now()->format('Y-m-d'),
                        'keterangan' => $this->getMockKeterangan($status),
                        'minutes_late' => $status === 'terlambat' ? 15 : 0
                    ]);
                    
                    // Set the relationships manually
                    $mockAbsensi->setRelation('siswa', $siswa);
                    $mockAbsensi->created_at = now();
                    
                    // Create a mock jadwal
                    $mockJadwal = new \stdClass();
                    $mockJadwal->mataPelajaran = new \stdClass();
                    $mockJadwal->mataPelajaran->nama_pelajaran = 'Test Mata Pelajaran';
                    $mockAbsensi->setRelation('jadwal', $mockJadwal);
                    
                    // Actually send the notification
                    $result = $notificationService->sendAttendanceNotification($mockAbsensi);
                    if ($result) {
                        $this->info("✓ Notification sent successfully");
                    } else {
                        $this->error("✗ Failed to send notification");
                    }
                }
                
                // Add a small delay between messages to avoid rate limiting
                if (!$dryRun) {
                    sleep(2);
                }
                
            } catch (\Exception $e) {
                $this->error("Error: " . $e->getMessage());
            }
        }
        
        $this->info("\nTest completed!");        
        return 0;
    }
    
    /**
     * Get mock keterangan for testing
     */
    private function getMockKeterangan($status)
    {
        switch ($status) {
            case 'hadir':
                return 'Hadir tepat waktu';
            case 'terlambat':
                return 'Terlambat 15 menit';
            case 'alpha':
                return null;
            case 'izin':
                return 'Izin sakit kepala';
            case 'sakit':
                return 'Demam tinggi';
            default:
                return null;
        }
    }
}
