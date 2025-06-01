<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Services\WhatsApp\BaileysWhatsAppService;
use Illuminate\Support\Facades\Log;

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
                Log::info('WhatsApp service is disabled, skipping message send');
                return;
            }

            // Check connection status
            $status = $whatsappService->getConnectionStatus();
            if (!$status['connected']) {
                Log::warning('WhatsApp not connected, requeueing message', [
                    'phone' => $this->phone,
                    'status' => $status
                ]);
                
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
                Log::info('WhatsApp message sent successfully via job', [
                    'phone' => $this->phone,
                    'messageId' => $result['messageId'] ?? null,
                    'type' => $this->mediaType
                ]);
            } else {
                Log::error('WhatsApp message sending failed in job', [
                    'phone' => $this->phone,
                    'error' => $result['error'] ?? 'Unknown error',
                    'type' => $this->mediaType
                ]);
                
                // Fail the job if it's not a connection issue
                if (!str_contains(strtolower($result['error'] ?? ''), 'not connected')) {
                    $this->fail(new \Exception($result['error'] ?? 'Failed to send WhatsApp message'));
                } else {
                    // If it's a connection issue, retry
                    $this->release(120); // Retry after 2 minutes
                }
            }

        } catch (\Exception $e) {
            Log::error('WhatsApp job execution failed', [
                'phone' => $this->phone,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            $this->fail($e);
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception)
    {
        Log::error('WhatsApp message job failed permanently', [
            'phone' => $this->phone,
            'message' => $this->message,
            'error' => $exception->getMessage(),
            'attempts' => $this->attempts()
        ]);
    }
}
