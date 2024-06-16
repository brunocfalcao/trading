<?php

namespace Brunocfalcao\Trading;

use Brunocfalcao\Trading\Abstracts\TradingServiceProvider as AbstractTradingServiceProvider;
use Brunocfalcao\Trading\Commands\AdjustStopLossesCommand;
use Brunocfalcao\Trading\Commands\PlaceOrdersCommand;
use Brunocfalcao\Trading\Commands\PlaceOrdersFileCommand;
use Brunocfalcao\Trading\Commands\RefreshMarkPricesCommand;
use Brunocfalcao\Trading\Commands\UpdateExchangeInfoCommand;
use Illuminate\Support\Facades\Route;

class TradingServiceProvider extends AbstractTradingServiceProvider
{
    public function boot()
    {
        $this->dir = __DIR__;

        $this->commands([
            AdjustStopLossesCommand::class,
            PlaceOrdersFileCommand::class,
            PlaceOrdersCommand::class,
            UpdateExchangeInfoCommand::class,
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
