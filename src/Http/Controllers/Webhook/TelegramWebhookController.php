<?php

namespace Brunocfalcao\Trading\Http\Controllers\Webhook;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class TelegramWebhookController extends Controller
{
    public function handle(Request $request)
    {
        // Get the message from the request
        $message = $request->input('message');

        // Ensure message is not null
        if ($message) {
            $chatId = $message['chat']['id'];
            $text = $message['text'];

            // Log the message
            Log::info("Message received from Chat ID $chatId with message: $text");
        }

        // Return a 200 response to Telegram
        return response()->json(['status' => 'success']);
    }
}
