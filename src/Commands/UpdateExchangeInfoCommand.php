<?php

namespace Brunocfalcao\Trading\Commands;

use Brunocfalcao\Trading\Futures;
use Illuminate\Console\Command;

class UpdateExchangeInfoCommand extends Command
{
    protected $signature = 'trading:update-exchange-info';

    protected $description = 'Updates Exchange Information';

    public function handle()
    {
        $client = new Futures();

        $exchangeInfo = $client->exchangeInfo();

        dd(array_keys($exchangeInfo['symbols'][0]));
    }
}
