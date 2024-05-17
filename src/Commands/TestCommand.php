<?php

namespace Brunocfalcao\Trading\Commands;

use Brunocfalcao\Trading\Futures;
use Brunocfalcao\Trading\Websocket\FuturesWebsocket;
use Illuminate\Console\Command;

class TestCommand extends Command
{
    protected $signature = 'trading:test';

    protected $description = 'Command to test trading api commands';

    public function handle()
    {
        //$client = new \Binance\Spot();
        //$client = new Futures();

        //$orders = collect($client->allOrders('BLZUSDT'));

        //$price = $client->markPrice('BTCUSDT');

        //$response = $client->getOrder('ONGUSDT', ['orderId' => 930032891]);

        // $response = $client->exchangeInfo();

        //dd($price);

        /*
        $client = new FuturesWebsocket();

        $callbacks = [
            'message' => function ($conn, $msg) {
                echo $msg.PHP_EOL;
            },
            'ping' => function ($conn, $msg) {
                echo "received ping from server".PHP_EOL;
            }
        ];

        $client->markPrices($callbacks);

        $client = new \Binance\Spot([
            'key' => env('BINANCE_API_KEY'),
            'secret' => env('BINANCE_SECRET_KEY')
        ]);

        $this->publicRequest('GET', '/sapi/v1/blvt/tokenInfo', $options);

        $response = $client->allOrders('ONGUSDT');
        var_dump($response);
        */
    }
}
