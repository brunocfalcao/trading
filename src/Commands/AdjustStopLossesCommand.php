<?php

namespace Brunocfalcao\Trading\Commands;

use Illuminate\Console\Command;
use Brunocfalcao\Trading\Websocket\FuturesWebsocket;

class AdjustStopLossesCommand extends Command
{
    protected $signature = 'trading:adjust-stop-losses';

    protected $description = 'Adjusts stop losses';

    public function handle()
    {
        $client = new FuturesWebsocket();

        $callbacks = [
            'message' => function ($conn, $msg) {
                $prices = collect(json_decode($msg, true));

                $usdtTokens = $prices->filter(function ($item) {
                    return substr($item['s'], -4) === 'USDT';
                })->values();

                echo 'stop losses updated '.date('H:m:s').PHP_EOL;
            },
            'ping' => function ($conn, $msg) {
                echo 'received ping from server'.PHP_EOL;
            },
        ];

        $client->markPrices($callbacks, false);
    }
}
