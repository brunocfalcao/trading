<?php

namespace Brunocfalcao\Trading\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class PollTelegramMessages extends Command
{
    protected $signature = 'trading:poll-telegram-messages';
    protected $description = 'Poll Telegram for new updates';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $startTimestamp = now()->toDateTimeString();
        Log::info("Starting Telegram polling at $startTimestamp");

        try {
            $lastUpdateId = Cache::get('telegram_last_update_id', 0);
            $updates = $this->getUpdates($lastUpdateId);

            if ($updates['ok'] && count($updates['result']) > 0) {
                foreach ($updates['result'] as $update) {
                    $lastUpdateId = $update['update_id'] + 1;

                    if (isset($update['message'])) {
                        $message = $update['message'];
                        $chatId = $message['chat']['id'];
                        $text = $message['text'];

                        // Log message
                        $this->logMessage($chatId, $text);
                    }
                }

                Cache::put('telegram_last_update_id', $lastUpdateId);
            }
        } catch (\Exception $e) {
            Log::error("Error during Telegram polling: " . $e->getMessage());
        }

        $endTimestamp = now()->toDateTimeString();
        Log::info("End Telegram polling at $endTimestamp");
    }

    private function getUpdates($offset)
    {
        $url = 'https://api.telegram.org/bot' . env('TELEGRAM_BOT_TOKEN') . '/getUpdates';

        try {
            $response = Http::get($url, ['offset' => $offset]);
            $data = $response->json();

            if (isset($data['error_code']) && $data['error_code'] == 409) {
                // Webhook is active, delete it
                $this->deleteWebhook();
                // Retry getUpdates
                $response = Http::get($url, ['offset' => $offset]);
                $data = $response->json();
            }

            return $data;
        } catch (\Exception $e) {
            Log::error("Error fetching updates from Telegram: " . $e->getMessage());
            return ['ok' => false];
        }
    }

    private function deleteWebhook()
    {
        $url = 'https://api.telegram.org/bot' . env('TELEGRAM_BOT_TOKEN') . '/deleteWebhook';

        try {
            $response = Http::get($url);
            $data = $response->json();

            if (!$data['ok']) {
                Log::error("Error deleting webhook: " . $data['description']);
            } else {
                Log::info("Webhook deleted successfully");
            }
        } catch (\Exception $e) {
            Log::error("Error deleting webhook: " . $e->getMessage());
        }
    }

    private function logMessage($chatId, $text)
    {
        Log::info("Received message from chat_id $chatId: $text");
    }
}
