<?php

namespace Brunocfalcao\Trading\Commands;

use Brunocfalcao\Trading\Futures;
use Illuminate\Console\Command;

class AdjustStopLossCommand extends Command
{
    protected $signature = 'trading:adjust-stop-loss';

    protected $description = 'Adjusts necessary stop losses given Position TPs';

    public function handle()
    {
        $client = new Futures();

        // Get all open orders, to start with.
        $orders = $client->openOrders();

        $stopLossOrders = collect($orders)->reject(function (array $order) {
            return $order['type'] != 'STOP_MARKET';
        })->toArray();

        // Get all open (amount > 0) positions.
        $positions = collect($client->getPositions())->reject(function (array $position) {
            return $position['positionAmt'] == 0;
        });

        foreach ($stopLossOrders as $stopOrder) {
            $price = $client->markPrice($stopOrder['symbol'])['markPrice'];
            $position = $positions->where('symbol', $stopOrder['symbol'])->first();

            if ($this->lower($stopOrder, $position['breakEvenPrice'])) {
                // Nothing to do. The stop loss is below the break even price.
            } else {
                // The stop loss is above the break even price. Lets check TPs.
                $nextTPOrder = $this->nextTP($stopOrder, $orders);
            }
        }
    }

    protected function lower(array $stopOrder, $price)
    {
        if ($stopOrder['side'] == 'SELL') { // It's a long.
            return $stopOrder['stopPrice'] < $price;
        } else { // It's a short.
            return $stopOrder['stopPrice'] > $price;
        }
    }

    protected function nextTP(array $stopOrder, array $orders)
    {
        $orders = collect($orders)->where('symbol', $stopOrder['symbol']);

        dd($orders);
    }
}
