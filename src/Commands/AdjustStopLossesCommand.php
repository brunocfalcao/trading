<?php

namespace Brunocfalcao\Trading\Commands;

use Brunocfalcao\Trading\Websocket\FuturesWebsocket;
use Illuminate\Console\Command;

class AdjustStopLossesCommand extends Command
{
    protected $signature = 'trading:adjust-stop-losses';

    protected $description = 'Adjusts stop losses';

    public function handle()
    {
        $client = new FuturesWebsocket();

        $callbacks = [
            'message' => function ($conn, $msg) {
                echo 'stop losses updated '.date('H:m:s').PHP_EOL;
            },
            'ping' => function ($conn, $msg) {
                echo 'received ping from server'.PHP_EOL;
            },
        ];

        $client->markPrices($callbacks, false);
    }
}
