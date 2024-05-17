<?php

namespace Brunocfalcao\Trading\Commands;

use Brunocfalcao\Trading\Websocket\FuturesWebsocket;
use Illuminate\Console\Command;

class MarkPriceCommand extends Command
{
    protected $signature = 'trading:mark-price';

    protected $description = 'Updates a specific mark price (websocket)';

    public function handle()
    {
        $client = new FuturesWebsocket();

        $callbacks = [
            'message' => function ($conn, $msg) {
                dd(json_decode($msg, true));
                exit();
            },
            'ping' => function ($conn, $msg) {
                echo 'received ping from server'.PHP_EOL;
            },
        ];

        $client->markPrice('BTCUSDT', $callbacks);
    }
}
