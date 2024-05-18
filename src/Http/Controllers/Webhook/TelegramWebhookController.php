<?php

namespace Brunocfalcao\Trading\Http\Controllers\Webhook;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class TelegramWebhookController extends Controller
{
    public function handle(Request $request)
    {
        return response()->json(['status' => 'success']);

        // Get the raw POST data
        $content = file_get_contents("php://input");
        $update = json_decode($content, true);

        // Ensure update is not null
        if ($update && isset($update['message'])) {
            $chatId = $update['message']['chat']['id'];
            $text = $update['message']['text'];

            // Log the message
            Log::info("Message received from Chat ID $chatId with message: $text");
        } else {
            Log::warning('Received invalid update from Telegram: ' . $content);
        }

        // Return a 200 response to Telegram
        return response()->json(['status' => 'success']);
    }
}
