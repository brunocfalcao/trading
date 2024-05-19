<?php

namespace Brunocfalcao\Trading;

use Brunocfalcao\Trading\Abstracts\TradingServiceProvider as AbstractTradingServiceProvider;
use Brunocfalcao\Trading\Commands\AdjustStopLossCommand;
use Brunocfalcao\Trading\Commands\RefreshMarkPricesCommand;
use Illuminate\Support\Facades\Route;

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
