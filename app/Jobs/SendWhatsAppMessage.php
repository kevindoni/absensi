<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Services\WhatsApp\BaileysWhatsAppService;

class SendWhatsAppMessage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $phone;
    public $message;
    public $mediaUrl;
    public $mediaType;
    public $caption;
    public $tries = 3;
    public $timeout = 60;

    /**
     * Create a new job instance.
     */
    public function __construct($phone, $message, $mediaUrl = null, $mediaType = 'text', $caption = '')
    {
        $this->phone = $phone;
        $this->message = $message;
        $this->mediaUrl = $mediaUrl;
        $this->mediaType = $mediaType;
        $this->caption = $caption;
    }

    /**
     * Execute the job.
     */
    public function handle(BaileysWhatsAppService $whatsappService)
    {
        try {
            // Check if WhatsApp service is enabled
            if (!$whatsappService->isEnabled()) {
                return;
            }

            // Check connection status
            $status = $whatsappService->getConnectionStatus();
            if (!$status['connected']) {
                // Release job back to queue with delay
                $this->release(60); // Retry after 1 minute
                return;
            }

            $result = null;

            // Send media or text message
            if ($this->mediaUrl && $this->mediaType !== 'text') {
                $result = $whatsappService->sendMedia(
                    $this->phone, 
                    $this->mediaUrl, 
                    $this->caption, 
                    $this->mediaType
                );
            } else {
                $result = $whatsappService->sendMessage($this->phone, $this->message);
            }

            if ($result['success']) {
                // Success - job completed
            } else {
                // Fail the job if it's not a connection issue
                if (!str_contains(strtolower($result['error'] ?? ''), 'not connected')) {
                    $this->fail(new \Exception($result['error'] ?? 'Failed to send WhatsApp message'));
                } else {
                    // If it's a connection issue, retry
                    $this->release(120); // Retry after 2 minutes
                }
            }

        } catch (\Exception $e) {
            $this->fail($e);
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception)
    {
        // Job failed - handle cleanup if needed
    }
}
