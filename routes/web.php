<?php

// routes/web.php

use Brunocfalcao\Trading\Http\Controllers\TradingController;
use Illuminate\Support\Facades\Route;

Route::get('/', [TradingController::class, 'index'])->name('index');
Route::post('/refresh-file', [TradingController::class, 'refreshFile'])->name('refresh-file');
Route::post('/update-file', [TradingController::class, 'updateFile'])->name('update-file');
Route::get('/refresh-prices', [TradingController::class, 'getLatestPrices'])->name('refresh-prices');
Route::post('/run-command', [TradingController::class, 'runCommand'])->name('run-command');
Route::post('/adjust-stop-loss', [TradingController::class, 'adjustStopLoss'])->name('adjust-stop-loss');
