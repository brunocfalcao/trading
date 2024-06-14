<?php

namespace Brunocfalcao\Trading\Commands;

use Illuminate\Console\Command;
use Brunocfalcao\Trading\Models\Price;
use Brunocfalcao\Trading\Models\Symbol;
use Brunocfalcao\Trading\Websocket\FuturesWebsocket;

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
                    Symbol::updateOrCreate(
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
