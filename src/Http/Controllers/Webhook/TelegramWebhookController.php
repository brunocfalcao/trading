<?php

namespace Brunocfalcao\Trading\Http\Controllers\Webhook;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TelegramWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $message = $request->input('message');

        if ($message) {
            $chatId = $message['chat']['id'];
            $text = $message['text'];

            // Define allowed chat IDs
            $allowedChatIds = [
                env('TELEGRAM_CHAT_ID_1'),
                env('TELEGRAM_CHAT_ID_2'),
            ];

            if (in_array($chatId, $allowedChatIds)) {
                Log::info("New message from chat $chatId: $text");
            } else {
                Log::warning("Message from unauthorized chat $chatId");
            }
        }

        return response()->json(['status' => 'success']);
    }
}
