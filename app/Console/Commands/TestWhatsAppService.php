<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\WhatsApp\BaileysWhatsAppService;
use App\Services\AdminNotificationService;

class TestWhatsAppService extends Command
{
    protected $signature = 'whatsapp:test';
    protected $description = 'Test WhatsApp service integration';

    protected $whatsappService;
    protected $notificationService;

    public function __construct(BaileysWhatsAppService $whatsappService, AdminNotificationService $notificationService)
    {
        parent::__construct();
        $this->whatsappService = $whatsappService;
        $this->notificationService = $notificationService;
    }

    public function handle()
    {
        $this->info('🔍 Testing WhatsApp Service Integration...');
        $this->newLine();        // Test 1: Check service health
        $this->info('1. Testing WhatsApp service health...');
        try {
            $gatewayUrl = $this->whatsappService->getGatewayUrl();
            $this->info("   Gateway URL: $gatewayUrl");
            
            $isConnected = $this->whatsappService->isConnected();
            $this->info("   Connection Status: " . ($isConnected ? '✅ Connected' : '❌ Not Connected'));
        } catch (\Exception $e) {
            $this->error("   ❌ Error: " . $e->getMessage());
        }
        $this->newLine();

        // Test 2: Check connection status
        $this->info('2. Testing connection status endpoint...');
        try {
            $status = $this->whatsappService->getConnectionStatus();
            $this->info("   Status: " . json_encode($status, JSON_PRETTY_PRINT));
        } catch (\Exception $e) {
            $this->error("   ❌ Error: " . $e->getMessage());
        }
        $this->newLine();

        // Test 3: Test admin numbers
        $this->info('3. Testing admin numbers configuration...');
        try {
            $adminNumbers = $this->whatsappService->getAdminNumbers();
            $this->info("   Admin Numbers: " . implode(', ', $adminNumbers));
        } catch (\Exception $e) {
            $this->error("   ❌ Error: " . $e->getMessage());
        }
        $this->newLine();

        // Test 4: Test message templates
        $this->info('4. Testing message templates...');
        try {
            $templates = $this->whatsappService->getMessageTemplates();
            $this->info("   Templates loaded: " . count($templates) . " templates");
            foreach ($templates as $key => $template) {
                $this->info("   - $key: " . substr($template, 0, 50) . '...');
            }
        } catch (\Exception $e) {
            $this->error("   ❌ Error: " . $e->getMessage());
        }
        $this->newLine();

        // Test 5: Try to get QR code (this will test the Node.js service connectivity)
        $this->info('5. Testing QR code generation...');
        try {
            $result = $this->whatsappService->getQRCode();
            if ($result['success'] && $result['qrCode']) {
                $this->info("   ✅ QR Code generated successfully");
                $this->info("   QR Code length: " . strlen($result['qrCode']) . " characters");
            } elseif ($result['success'] && !$result['qrCode']) {
                $this->warn("   ⚠️  No QR Code (device may already be connected)");
            } else {
                $this->error("   ❌ Failed to get QR Code: " . ($result['error'] ?? 'Unknown error'));
            }
        } catch (\Exception $e) {
            $this->error("   ❌ Error: " . $e->getMessage());
        }
        $this->newLine();

        // Test 6: Test phone number formatting
        $this->info('6. Testing phone number formatting...');
        $testNumbers = ['08123456789', '628123456789', '+628123456789', '8123456789'];
        foreach ($testNumbers as $number) {
            try {
                $formatted = $this->whatsappService->formatPhoneNumber($number);
                $this->info("   $number → $formatted");
            } catch (\Exception $e) {
                $this->error("   $number → Error: " . $e->getMessage());
            }
        }
        $this->newLine();        $this->info('✅ WhatsApp service test completed!');
        
        // Get final connection status
        try {
            $isConnected = $this->whatsappService->isConnected();
            if (!$isConnected) {
                $this->newLine();
                $this->warn('📱 To connect WhatsApp:');
                $this->warn('1. Login to admin panel: http://localhost:8000/admin/login');
                $this->warn('2. Go to WhatsApp Gateway page');
                $this->warn('3. Generate QR code and scan with your phone');
                $this->warn('4. Admin credentials: username=superadmin, password=superadmin');
            } else {
                $this->newLine();
                $this->info('🎉 WhatsApp is already connected and ready to use!');
            }
        } catch (\Exception $e) {
            // Ignore errors for final status check
        }

        return 0;
    }
}
