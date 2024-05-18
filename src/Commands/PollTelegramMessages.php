<?php

namespace Brunocfalcao\Trading\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PollTelegramMessages extends Command
{
    protected $signature = 'trading:poll-telegram-messages';

    protected $description = 'Polls Telegram Messages';

    public function handle()
    {
        $lastUpdateId = Cache::get('telegram_last_update_id', 0);
        $updates = $this->getUpdates($lastUpdateId);

        if ($updates['ok'] && count($updates['result']) > 0) {
            foreach ($updates['result'] as $update) {
                $lastUpdateId = $update['update_id'] + 1;

                if (isset($update['message'])) {
                    $message = $update['message'];
                    $chatId = $message['chat']['id'];
                    $text = $message['text'];

                    // Store message in the database
                    $this->storeMessage($chatId, $text);
                }
            }

            Cache::put('telegram_last_update_id', $lastUpdateId);
        }
    }

    private function getUpdates($offset)
    {
        $url = 'https://api.telegram.org/bot'.env('TELEGRAM_BOT_TOKEN').'/getUpdates';
        $response = Http::get($url, ['offset' => $offset]);

        return $response->json();
    }

    private function storeMessage($chatId, $text)
    {
        Log::info('Chat ID: '.$chatId.' | Message: '.$text);
    }
}
