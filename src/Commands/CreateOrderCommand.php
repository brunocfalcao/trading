<?php

namespace Brunocfalcao\Trading\Commands;

use Brunocfalcao\Trading\Futures;
use Illuminate\Console\Command;

class CreateOrderCommand extends Command
{
    protected $signature = 'trading:create-order';

    protected $description = 'Creates a new trading order';

    public function handle()
    {
        $client = new Futures();

        $payload = [
            //'timeInForce', 'GTD',
            'quantity' => '5',
            //'reduceOnly' => 'true',
            'price' => '6.1527',
        ];

        $client->newOrder('NEARUSDT', 'BUY', 'LIMIT', $payload);
    }
}
