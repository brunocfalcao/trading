<?php

namespace Brunocfalcao\Trading\Commands;

use Brunocfalcao\Trading\Futures;
use Brunocfalcao\Trading\Models\Order;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RefreshOpenOrdersCommand extends Command
{
    protected $signature = 'trading:refresh-open-orders';

    protected $description = 'Refreshes open positions and computes stop losses';

    protected $order;

    protected $position;

    public function handle()
    {
        $client = new Futures();

        DB::table('orders')->truncate();

        foreach ($client->openOrders() as $order) {
            Order::create($order);
        }




        // Get all distinct symbols.
        $symbols = Order::select('symbol')->distinct()->pluck('symbol');

        foreach ($symbols as $symbol) {
            $orders = Order::where('symbol', $symbol)->get();

            /**
             * For each of the orders, we need to correctly populate
             * the cancelOnMarkPrice and the newStopLossMarkPrice.
             *
             * ( !!)Attention if it's a long, or a short! use the utility
             * methods ->less() and ->higher() ->equal().
             */
        }
    }
}
