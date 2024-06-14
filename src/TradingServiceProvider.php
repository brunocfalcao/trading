<?php

namespace Brunocfalcao\Trading;

use Illuminate\Support\Facades\Route;
use Brunocfalcao\Trading\Commands\TradeCommand;
use Brunocfalcao\Trading\Commands\AdjustStopLossCommand;
use Brunocfalcao\Trading\Commands\RefreshMarkPricesCommand;
use Brunocfalcao\Trading\Commands\UpdateExchangeInfoCommand;
use Brunocfalcao\Trading\Abstracts\TradingServiceProvider as AbstractTradingServiceProvider;

class TradingServiceProvider extends AbstractTradingServiceProvider
{
    public function boot()
    {
        $this->dir = __DIR__;

        $this->commands([
            AdjustStopLossCommand::class,
            TradeCommand::class,
            UpdateExchangeInfoCommand::class,
            RefreshMarkPricesCommand::class
        ]);

        $this->loadViewsFrom(__DIR__.'/../resources/views', 'trading');

        $this->loadRoutes();

        parent::boot();
    }

    protected function loadRoutes()
    {
        $routesPath = __DIR__.'/../routes/web.php';

        Route::group([], function () use ($routesPath) {
            include $routesPath;
        });
    }

    public function register()
    {
        //
    }
}
