<?php

use Illuminate\Support\Facades\Route;
use Brunocfalcao\Trading\Http\Middleware\VerifyTelegramToken;
use Brunocfalcao\Trading\Http\Controllers\Webhook\TelegramWebhookController;

Route::post(
    'webhooks/new-signal',
    [TelegramWebhookController::class, 'handle']
)->middleware(['api', VerifyTelegramToken::class]);

Route::get('webhooks/new-signal', function () {
    return response()->json(null, 200);
});
