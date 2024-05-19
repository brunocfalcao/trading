<?php

namespace Brunocfalcao\Trading\Commands;

use Brunocfalcao\Trading\Futures;
use Brunocfalcao\Trading\Models\Order;
use Brunocfalcao\Trading\Models\Position;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RefreshOpenPositionsCommand extends Command
{
    protected $signature = 'trading:refresh-open-positions';

    protected $description = 'Refreshes open positions';

    public function handle()
    {
        $client = new Futures();

        DB::table('orders')->truncate();

        foreach ($client->openOrders() as $order) {
            Order::create($order);
        }

        DB::table('positions')->truncate();

        $positions = $client->getPositions();

        $positions = collect($positions)->reject(function (array $position) {
            return $position['positionAmt'] == 0;
        })->toArray();

        foreach ($positions as $position) {
            Position::create($position);
        }

        foreach ($client->openOrders() as $order) {
            Order::create($order);
        }

        //$order = clone Order::find(3);

        //dd($client->getPositions());

        //dd(collect($client->getPositions())->where('symbol', 'HBARUSDT'));

        //$client->cancelOrder($order->symbol, ['orderId' => $order->orderId]);

        /*
        $client->newOrder($order->symbol, $order->side, $order->type, [
            'closePosition' => true,
            'stopPrice' => 0.00950
        ]);
        */
    }
}
