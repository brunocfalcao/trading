<?php

namespace Brunocfalcao\Trading\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class TestWebhookCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'trading:test-webhook';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test the Telegram webhook with a POST request';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $url = env('WEBHOOK_URL', 'https://nidavellir.trade/webhooks/new-signal');
        $secretToken = env('TELEGRAM_SECRET_TOKEN');

        $response = Http::withHeaders([
            'X-Telegram-Bot-Api-Secret-Token' => $secretToken,
        ])->post($url, [
            'message' => [
                'chat' => [
                    'id' => '123456789', // example chat ID
                ],
                'text' => 'Test message from Laravel command', // example message
            ],
        ]);

        if ($response->successful()) {
            $this->info('Webhook test successful! - ' . $response->body());
        } else {
            $this->error('Webhook test failed: '.$response->body());
        }

        return 0;
    }
}
