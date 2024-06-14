<?php

namespace Brunocfalcao\Trading\Commands;

use Illuminate\Console\Command;
use Brunocfalcao\Trading\Futures;

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
