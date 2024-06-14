<?php

namespace Brunocfalcao\Trading\Commands;

use Brunocfalcao\Trading\Futures;
use Brunocfalcao\Trading\Models\Order;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RefreshOrdersCommand extends Command
{
    protected $signature = 'trading:refresh-orders';

    protected $description = 'Refreshes open positions and computes stop losses';

    protected $order;

    protected $position;

    public function handle()
    {
        $client = new Futures();

        DB::table('orders')->truncate();

        foreach ($client->openOrders() as $order) {
            dd($order->attributesToArray());

            Order::updateOrCreate(
                ['clientOrderId' => $order->clientOrderId],
                $order->attributesToArray()
            );
        }
    }
}
