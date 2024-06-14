<?php

namespace Brunocfalcao\Trading\Commands;

use Brunocfalcao\Trading\Futures;
use Brunocfalcao\Trading\Models\Symbol;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Console\Command;

class AdjustStopLossesCommand extends Command
{
    protected $signature = 'trading:adjust-stop-loss {pairs} {--percentage=} {--absolute=}';

    protected $description = 'Adjusts stop losses for specified trading pairs based on the given percentage or absolute value';

    public function handle()
    {
        $pairs = $this->argument('pairs');
        $percentage = $this->option('percentage');
        $absolute = $this->option('absolute');

        if (! $percentage && ! $absolute) {
            $this->error('You must provide either --percentage or --absolute option.');

            return;
        }

        // Handle multiple pairs
        $pairsArray = explode(',', $pairs);

        foreach ($pairsArray as $pair) {
            try {
                $this->adjustStopLoss($pair, $percentage, $absolute);
            } catch (\Exception $e) {
                $this->error("Error adjusting stop loss for pair {$pair}: ".$e->getMessage());
            }
        }
    }

    protected function adjustStopLoss($pair, $percentage, $absolute)
    {
        try {
            $orders = $this->getOpenOrders($pair);

            if ($orders->isEmpty()) {
                $this->error("There are no active orders for pair {$pair}");

                return;
            }

            $lastPrice = $this->getLastPrice($pair);
            $symbol = $this->getSymbol($pair);
            $pricePrecision = $symbol->price_precision;

            $existingStopMarketOrder = $orders->firstWhere('type', 'STOP_MARKET');
            $entryOrder = $orders->firstWhere('type', 'MARKET'); // Assuming the entry order is of type 'MARKET'

            if ($entryOrder) {
                $entryPrice = $entryOrder['avgPrice'];
                $positionSide = $entryOrder['side']; // 'BUY' for long, 'SELL' for short

                if ($percentage) {
                    $stopLossPrice = $this->calculateStopLossPrice($entryPrice, $lastPrice, $percentage, $positionSide);
                } else {
                    $stopLossPrice = $absolute;
                }

                // Adjust stop loss price based on price precision
                $stopLossPrice = number_format($stopLossPrice, $pricePrecision, '.', '');

                if ($existingStopMarketOrder) {
                    $this->modifyStopOrder($existingStopMarketOrder, $stopLossPrice);
                } else {
                    $this->createStopOrder($pair, $stopLossPrice, $entryOrder['origQty']);
                }
            } else {
                $this->error("No market entry order found for pair {$pair}");
            }
        } catch (ClientException $e) {
            $this->error("Client error adjusting stop loss for pair {$pair}: ".$e->getMessage());
        } catch (\Exception $e) {
            $this->error("Error adjusting stop loss for pair {$pair}: ".$e->getMessage());
        }
    }

    protected function getOpenOrders($pair)
    {
        try {
            // Get all open orders for the given pair using Futures client
            $client = new Futures();
            $orders = collect($client->openOrders(['symbol' => $pair]));

            return $orders;
        } catch (ClientException $e) {
            $this->error('Error fetching open orders: '.$e->getMessage());

            return collect();
        }
    }

    protected function getLastPrice($pair)
    {
        try {
            // Get the last price for the given pair from the Symbol model
            $symbol = Symbol::where('pair', $pair)->first();

            return $symbol->last_price;
        } catch (\Exception $e) {
            $this->error('Error fetching last price: '.$e->getMessage());

            return null;
        }
    }

    protected function getSymbol($pair)
    {
        try {
            // Get the symbol details for the given pair from the Symbol model
            return Symbol::where('pair', $pair)->first();
        } catch (\Exception $e) {
            $this->error('Error fetching symbol details: '.$e->getMessage());

            return null;
        }
    }

    protected function calculateStopLossPrice($entryPrice, $lastPrice, $percentage, $positionSide)
    {
        // Calculate the stop loss price based on entry price, last price, percentage, and position side
        if ($positionSide == 'BUY') {
            $stopLossPrice = $entryPrice + (($lastPrice - $entryPrice) * ($percentage / 100));
        } else {
            $stopLossPrice = $entryPrice - (($entryPrice - $lastPrice) * ($percentage / 100));
        }

        return $stopLossPrice;
    }

    protected function modifyStopOrder($order, $stopLossPrice)
    {
        try {
            // Placeholder: Modify the existing stop market order
            $this->info("Modifying stop market order {$order['orderId']} to {$stopLossPrice}");
        } catch (ClientException $e) {
            $this->error('Error modifying stop market order: '.$e->getMessage());
        }
    }

    protected function createStopOrder($pair, $stopLossPrice, $quantity)
    {
        try {
            // Create a new stop market order using the Futures client
            $client = new Futures();
            $stopOrderSide = 'SELL'; // Assuming the stop order side is 'SELL'
            $stopOrderParams = [
                'stopPrice' => $stopLossPrice,
                'closePosition' => true,
                'workingType' => 'MARK_PRICE',
                'priceProtect' => 'TRUE',
            ];

            $stopOrderResponse = $client->newOrder($pair, $stopOrderSide, 'STOP_MARKET', $stopOrderParams);

            $this->info("Created new stop market order for pair {$pair} at {$stopLossPrice} with quantity {$quantity}");
        } catch (ClientException $e) {
            $this->error('Error creating stop market order: '.$e->getMessage());
        }
    }
}
