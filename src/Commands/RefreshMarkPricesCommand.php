<?php

namespace Brunocfalcao\Trading\Commands;

use Brunocfalcao\Trading\Models\Signal;
use Brunocfalcao\Trading\Websocket\FuturesWebsocket;
use Illuminate\Console\Command;

class RefreshMarkPricesCommand extends Command
{
    protected $signature = 'trading:refresh-mark-prices';

    protected $description = 'Updates all market mark prices (websocket)';

    public function handle()
    {
        $client = new FuturesWebsocket();

        $callbacks = [
            'message' => function ($conn, $msg) {
                $prices = collect(json_decode($msg, true));

                $usdtTokens = $prices->filter(function ($item) {
                    return substr($item['s'], -4) === 'USDT';
                })->values();

                foreach ($usdtTokens as $token) {
                    Signal::updateOrCreate(
                        ['pair' => $token['s']],
                        [
                            'last_price' => $token['p'],
                        ]
                    );
                }

                echo 'prices updated '.date('H:m:s').PHP_EOL;
            },
            'ping' => function ($conn, $msg) {
                echo 'received ping from server'.PHP_EOL;
            },
        ];

        $client->markPrices($callbacks);
    }
}
