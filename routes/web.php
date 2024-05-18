<?php

use Brunocfalcao\Trading\Http\Controllers\Webhook\TelegramWebhookController;
use Brunocfalcao\Trading\Http\Middleware\VerifyTelegramToken;
use Illuminate\Support\Facades\Route;

Route::post(
    'webhooks/new-signal',
    [TelegramWebhookController::class, 'handle']
)->middleware(['api', VerifyTelegramToken::class]);

Route::get(
    'webhooks/new-signal',
    [TelegramWebhookController::class, 'handle']
);

Route::view('/', 'trading::active-signals');
