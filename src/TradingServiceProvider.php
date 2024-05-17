<?php

namespace Brunocfalcao\Trading;

use Brunocfalcao\Trading\Commands\CreateOrderCommand;
use Brunocfalcao\Trading\Commands\ExecuteCommand;
use Brunocfalcao\Trading\Commands\MarkPriceCommand;
use Brunocfalcao\Trading\Commands\NewSignalCommand;
use Brunocfalcao\Trading\Commands\TestCommand;
use Illuminate\Support\ServiceProvider;

class TradingServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        $this->commands([
            CreateOrderCommand::class,
            ExecuteCommand::class,
            MarkPriceCommand::class,
            NewSignalCommand::class,
            TestCommand::class,
        ]);
    }

    public function register()
    {
        //
    }
}
