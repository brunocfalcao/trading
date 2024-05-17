<?php

namespace Brunocfalcao\Trading\Commands;

use Brunocfalcao\Trading\Websocket\FuturesWebsocket;
use Illuminate\Console\Command;

class ExecuteCommand extends Command
{
    protected $signature = 'trading:execute';

    protected $description = 'Executes trading logic based on the mark prices updates (each second)';

    public function handle()
    {
        $client = new FuturesWebsocket();

        $callbacks = [
            'message' => function ($conn, $msg) {

                // Grab all mark prices on a structured array.
                $payload = json_decode($msg, true);

                $this->trade($payload);

                exit();
            },
            'ping' => function ($conn, $msg) {
                echo 'received ping from server'.PHP_EOL;
            },
        ];

        $client->markPrices($callbacks);
    }

    protected function trade()
    {
        /**
         * Everything happens on this logic here. Each time there is a new
         * mark price update, we need to revisit all of our orders and signals
         * and take decisions on what to do. The signal creation is not on
         * this command scope.
         */
    }
}
