<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Setting;

class UpdateWhatsAppUrlCommand extends Command
{
    protected $signature = 'whatsapp:update-url';
    protected $description = 'Update the WhatsApp Gateway URL in settings';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $setting = Setting::where('key', 'whatsapp_gateway_url')->first();
        if ($setting) {
            $oldUrl = $setting->value;
            $setting->value = 'http://localhost:3001';
            $setting->save();
            $this->info("WhatsApp Gateway URL updated from '{$oldUrl}' to 'http://localhost:3001'.");
        } else {
            Setting::create([
                'key' => 'whatsapp_gateway_url',
                'value' => 'http://localhost:3001',
                'type' => 'text', // Assuming a type column exists, adjust if necessary
                'label' => 'WhatsApp Gateway URL', // Assuming a label column exists
                'group' => 'whatsapp' // Assuming a group column exists
            ]);
            $this->info("WhatsApp Gateway URL created and set to 'http://localhost:3001'.");
        }
        return 0;
    }
}
