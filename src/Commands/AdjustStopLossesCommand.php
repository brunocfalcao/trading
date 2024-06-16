<?php

namespace Brunocfalcao\Trading\Commands;

use Brunocfalcao\Trading\Futures;
use Brunocfalcao\Trading\Models\Signal;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Console\Command;

class AdjustStopLossesCommand extends Command
{
    protected $signature = 'trading:adjust-stop-loss {pairs} {--perc=}';

    protected $description = 'Adjusts stop losses for specified trading pairs based on entry price or given percentage';

    public function handle()
    {
        $pairs = $this->argument('pairs');
        $percentageOrEntry = $this->option('perc');

        if ($percentageOrEntry === null) {
            $this->error('You must provide the --perc option with a percentage.');

            return;
        }

        if ($pairs === '*') {
            // Handle all pairs with STOP_MARKET orders
            try {
                $allPairs = $this->getAllPairsWithStopMarketOrders();
                foreach ($allPairs as $pair) {
                    $this->adjustStopLoss($pair, $percentageOrEntry);
                }
            } catch (\Exception $e) {
                $this->error('Error adjusting stop loss for all pairs: '.$e->getMessage());
            }
        } else {
            // Handle specified pairs
            $pairsArray = explode(',', $pairs);
            foreach ($pairsArray as $pair) {
                try {
                    $this->adjustStopLoss($pair, $percentageOrEntry, true);
                } catch (\Exception $e) {
                    $this->error("Error adjusting stop loss for pair {$pair}: ".$e->getMessage());
                }
            }
        }
    }

    protected function adjustStopLoss($pair, $percentageOrEntry, $createIfNotFound = false)
    {
        try {
            $orders = $this->getOpenOrders($pair);

            $symbol = $this->getSignal($pair);
            $entryPrice = $symbol->_entry_price;
            $lastPrice = $symbol->last_price;
            $pricePrecision = $symbol->price_precision;
            $positionSide = $symbol->_last_order_position_side; // Fetch the position side (LONG or SHORT)

            $existingStopMarketOrder = $orders->firstWhere('type', 'STOP_MARKET');
            $percentage = (float) $percentageOrEntry;
            $stopLossPrice = $this->calculateStopLossPrice($entryPrice, $percentage, $positionSide);

            // Adjust stop loss price based on price precision
            $stopLossPrice = number_format($stopLossPrice, $pricePrecision, '.', '');

            if ($existingStopMarketOrder) {
                // Cancel the existing stop market order
                $this->cancelStopOrder($existingStopMarketOrder['symbol'], $existingStopMarketOrder['orderId']);
            } elseif (! $createIfNotFound) {
                $this->error("No STOP_MARKET order found for pair {$pair}");

                return;
            }

            // Create a new stop market order
            $side = ($positionSide === 'LONG') ? 'SELL' : 'BUY';
            $this->createStopOrder($pair, $side, $stopLossPrice);
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

    protected function getAllPairsWithStopMarketOrders()
    {
        try {
            // Get all open orders using Futures client
            $client = new Futures();
            $allOrders = collect($client->openOrders());

            // Filter orders to get unique pairs with STOP_MARKET orders
            $stopMarketPairs = $allOrders->filter(function ($order) {
                return $order['type'] === 'STOP_MARKET';
            })->pluck('symbol')->unique();

            return $stopMarketPairs;
        } catch (ClientException $e) {
            $this->error('Error fetching all open orders: '.$e->getMessage());

            return collect();
        }
    }

    protected function getSignal($pair)
    {
        try {
            // Get the symbol details for the given pair from the Signal model
            return Signal::where('pair', $pair)->first();
        } catch (\Exception $e) {
            $this->error('Error fetching symbol details: '.$e->getMessage());

            return null;
        }
    }

    protected function calculateStopLossPrice($entryPrice, $percentage, $positionSide)
    {
        if ($positionSide == 'LONG') {
            return $entryPrice * (1 - abs($percentage) / 100);
        } else {
            return $entryPrice * (1 + abs($percentage) / 100);
        }
    }

    protected function cancelStopOrder($symbol, $orderId)
    {
        try {
            // Cancel the existing stop market order
            $client = new Futures();
            $client->cancelOrder($symbol, ['orderId' => $orderId]);

            $this->info("Cancelled stop market order {$orderId} for symbol {$symbol}");
        } catch (ClientException $e) {
            $this->error('Error cancelling stop market order: '.$e->getMessage());
        }
    }

    protected function createStopOrder($symbol, $side, $stopLossPrice)
    {
        try {
            // Create a new stop market order
            $client = new Futures();
            $client->newOrder($symbol, $side, 'STOP_MARKET', [
                'stopPrice' => $stopLossPrice,
                'closePosition' => true,
                'workingType' => 'MARK_PRICE',
                'priceProtect' => 'TRUE',
            ]);

            $this->info("Created new stop market order for symbol {$symbol} with stop price {$stopLossPrice}");
        } catch (ClientException $e) {
            $this->error('Error creating stop market order: '.$e->getMessage());
        }
    }
}
