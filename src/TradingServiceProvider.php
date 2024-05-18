<?php

namespace Brunocfalcao\Trading;

use Brunocfalcao\Trading\Abstracts\TradingServiceProvider as AbstractTradingServiceProvider;
use Brunocfalcao\Trading\Commands\CreateOrderCommand;
use Brunocfalcao\Trading\Commands\ExecuteCommand;
use Brunocfalcao\Trading\Commands\NewSignalCommand;
use Brunocfalcao\Trading\Commands\TestCommand;
use Brunocfalcao\Trading\Commands\TestWebhookCommand;
use Brunocfalcao\Trading\Commands\UpdatePricesCommand;
use Illuminate\Support\Facades\Route;

class TradingServiceProvider extends AbstractTradingServiceProvider
{
    public function boot()
    {
        $this->dir = __DIR__;

        $this->commands([
            CreateOrderCommand::class,
            ExecuteCommand::class,
            UpdatePricesCommand::class,
            NewSignalCommand::class,
            TestCommand::class,
            TestWebhookCommand::class,
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
