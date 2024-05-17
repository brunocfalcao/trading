<?php

use Brunocfalcao\Trading\Http\Controllers\TelegramNotification;
use Illuminate\Support\Facades\Route;

Route::post(
    '/webhooks/new-signal',
    [TelegramNotification::class, 'handle']
)->middleware('verify.telegram.token');
