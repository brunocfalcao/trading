<?php

namespace Brunocfalcao\Trading;

use Brunocfalcao\Trading\Commands\TestCommand;
use Illuminate\Support\ServiceProvider;

class TradingServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->commands([
            TestCommand::class,
        ]);
    }

    public function register()
    {
        //
    }
}
