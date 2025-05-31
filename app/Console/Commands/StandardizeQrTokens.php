<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Siswa;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class StandardizeQrTokens extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'qr:standardize {--force : Force regeneration of all tokens} {--dry-run : Show what would be done without making changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Standardize all QR tokens to use consistent format (40 random chars + timestamp)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $force = $this->option('force');
        $dryRun = $this->option('dry-run');
        
        $this->info('🔍 Analyzing QR token consistency...');
        
        // Get all students
        $allStudents = Siswa::all();
        $this->info("Found {$allStudents->count()} total students");
        
        // Analyze current token formats
        $needsUpdate = collect();
        $consistent = collect();
        $noToken = collect();
        
        foreach ($allStudents as $siswa) {
            if (!$siswa->qr_token) {
                $noToken->push($siswa);
            } elseif ($this->isStandardFormat($siswa->qr_token)) {
                $consistent->push($siswa);
            } else {
                $needsUpdate->push($siswa);
            }
        }
        
        // Display analysis
        $this->table(
            ['Status', 'Count', 'Description'],
            [
                ['✅ Consistent', $consistent->count(), 'Already using standard format (40 chars + timestamp)'],
                ['⚠️  Needs Update', $needsUpdate->count(), 'Using old format or inconsistent length'],
                ['❌ No Token', $noToken->count(), 'Missing QR token entirely'],
            ]
        );
        
        if ($needsUpdate->isEmpty() && $noToken->isEmpty()) {
            $this->info('🎉 All QR tokens are already standardized!');
            return Command::SUCCESS;
        }
        
        if ($dryRun) {
            $this->warn('🔍 DRY RUN MODE - No changes will be made');
            $this->showUpdatePreview($needsUpdate, $noToken);
            return Command::SUCCESS;
        }
        
        // Confirm action unless force is used
        if (!$force) {
            $totalToUpdate = $needsUpdate->count() + $noToken->count();
            if (!$this->confirm("Update {$totalToUpdate} QR tokens?")) {
                $this->info('Operation cancelled.');
                return Command::SUCCESS;
            }
        }
        
        // Update tokens
        $updated = 0;
        $failed = 0;
        
        $this->info('🔄 Standardizing QR tokens...');
        $progressBar = $this->output->createProgressBar($needsUpdate->count() + $noToken->count());
        
        // Update inconsistent tokens
        foreach ($needsUpdate as $siswa) {
            try {
                $oldToken = $siswa->qr_token;
                $this->regenerateStandardToken($siswa);
                Log::info("QR token standardized", [
                    'student_id' => $siswa->id,
                    'old_token' => $oldToken,
                    'new_token' => $siswa->qr_token
                ]);
                $updated++;
            } catch (\Exception $e) {
                $failed++;
                Log::error("Failed to standardize QR token for student {$siswa->id}: " . $e->getMessage());
            }
            $progressBar->advance();
        }
        
        // Generate missing tokens
        foreach ($noToken as $siswa) {
            try {
                $this->regenerateStandardToken($siswa);
                Log::info("QR token generated", [
                    'student_id' => $siswa->id,
                    'new_token' => $siswa->qr_token
                ]);
                $updated++;
            } catch (\Exception $e) {
                $failed++;
                Log::error("Failed to generate QR token for student {$siswa->id}: " . $e->getMessage());
            }
            $progressBar->advance();
        }
        
        $progressBar->finish();
        $this->newLine(2);
        
        // Summary
        $this->info("✅ Successfully updated: {$updated}");
        if ($failed > 0) {
            $this->error("❌ Failed: {$failed}");
        }
        
        $this->info('🎉 QR token standardization complete!');
        
        return Command::SUCCESS;
    }
    
    /**
     * Check if token follows standard format
     */
    private function isStandardFormat(string $token): bool
    {
        // Standard format: 40 random chars + 10-digit timestamp = 50 chars total
        if (strlen($token) !== 50) {
            return false;
        }
        
        // Check if last 10 characters are digits (timestamp)
        $lastTen = substr($token, -10);
        return ctype_digit($lastTen);
    }
    
    /**
     * Regenerate token using standard format
     */
    private function regenerateStandardToken(Siswa $siswa): void
    {
        $siswa->update([
            'qr_token' => Str::random(40) . time(),
            'qr_generated_at' => now()
        ]);
    }
    
    /**
     * Show preview of what would be updated
     */
    private function showUpdatePreview($needsUpdate, $noToken): void
    {
        if ($needsUpdate->isNotEmpty()) {
            $this->warn("\nStudents with inconsistent tokens (sample):");
            foreach ($needsUpdate->take(5) as $siswa) {
                $len = strlen($siswa->qr_token);
                $this->line("  • {$siswa->nama_lengkap} (ID: {$siswa->id}) - Current length: {$len}");
            }
            if ($needsUpdate->count() > 5) {
                $this->line("  ... and " . ($needsUpdate->count() - 5) . " more");
            }
        }
        
        if ($noToken->isNotEmpty()) {
            $this->warn("\nStudents without QR tokens (sample):");
            foreach ($noToken->take(5) as $siswa) {
                $this->line("  • {$siswa->nama_lengkap} (ID: {$siswa->id})");
            }
            if ($noToken->count() > 5) {
                $this->line("  ... and " . ($noToken->count() - 5) . " more");
            }
        }
    }
}
