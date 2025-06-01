<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\AdminNotificationService;

class CreateTestNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notification:test {message?} {--type=info : Notification type (info, success, warning, danger)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a test notification for all admin users';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // Use the service container to resolve the dependency
        $notificationService = app(\App\Services\AdminNotificationService::class);
        
        $message = $this->argument('message') ?? 'Ini adalah notifikasi test pada ' . now()->format('d M Y H:i:s');
        $type = $this->option('type');
        
        $this->info("Sending test notification: {$message}");
        
        $result = $notificationService->sendAdminNotification(
            $message,
            'Test Notification',
            $type
        );
        
        if ($result) {
            $this->info('Test notification sent successfully!');
            return Command::SUCCESS;
        } else {
            $this->error('Failed to send test notification.');
            return Command::FAILURE;
        }
    }
}
