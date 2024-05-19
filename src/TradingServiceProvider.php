<?php

namespace Brunocfalcao\Trading;

use Illuminate\Support\Facades\Route;
use Brunocfalcao\Trading\Commands\AdjustStopLossCommand;
use Brunocfalcao\Trading\Commands\RefreshMarkPricesCommand;
use Brunocfalcao\Trading\Commands\RefreshOpenOrdersCommand;
use Brunocfalcao\Trading\Commands\RefreshOpenPositionsCommand;
use Brunocfalcao\Trading\Abstracts\TradingServiceProvider as AbstractTradingServiceProvider;

class TradingServiceProvider extends AbstractTradingServiceProvider
{
    public function boot()
    {
        $this->dir = __DIR__;

        $this->commands([
            AdjustStopLossCommand::class,
            RefreshMarkPricesCommand::class,
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
