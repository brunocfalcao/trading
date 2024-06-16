<?php

namespace Brunocfalcao\Trading\Commands;

use Brunocfalcao\Trading\Futures;
use Brunocfalcao\Trading\Models\Signal;
use Brunocfalcao\Trading\Websocket\FuturesWebsocket;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class PlaceOrdersFileCommand extends Command
{
    protected $signature = 'trading:place-orders-file {--test}';

    protected $description = 'Trades based on BTCUSDT price action fluctuations from file input';

    private $pairs = [];

    private $amount;

    private $stopLossPercentage = 10; // Stop loss percentage (10%)

    private $orders = [];

    private $testMode = false;

    private $orderTriggered = [];

    private $websocketClient;

    private $override = [];

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $this->initializeParameters();

        if (! $this->validateParameters()) {
            return;
        }

        // Process override orders if any
        if (! empty($this->override)) {
            foreach ($this->override as $override) {
                $this->info("ACTION: Override detected. Placing {$override['side']} orders immediately for pairs: ".implode(', ', $override['pairs'])." with amount: {$override['amount']}");
                $this->placeOverrideOrders($override['side'], $override['pairs'], $override['amount']);
            }
        }

        // Always start the websocket to handle LONG/SHORT
        $this->startWebsocket();
    }

    private function initializeParameters()
    {
        $this->testMode = $this->option('test');
        $this->stopLossPercentage = config('trading.stop_loss_percentage', 10);

        $directoryPath = storage_path('app/trading');
        $filePath = storage_path('app/trading/pairs.txt');

        // Ensure the directory exists
        if (! File::exists($directoryPath)) {
            File::makeDirectory($directoryPath, 0755, true);
        }

        // Ensure the file exists
        if (! File::exists($filePath)) {
            Storage::put('trading/pairs.txt', '');
            $this->error('File pairs.txt did not exist and has been created. Please fill it with the appropriate trading data.');
            exit;
        }

        $fileContent = Storage::get('trading/pairs.txt');
        $lines = explode(PHP_EOL, trim($fileContent));

        foreach ($lines as $line) {
            $line = trim($line);
            if (strpos($line, 'OVERRIDE:') === 0) {
                $this->parseOverrideLine($line);
            } else {
                $this->parseTradingLine($line);
            }
        }

        foreach ($this->pairs as $side => $data) {
            foreach ($data['pairs'] as $pair) {
                $this->orderTriggered[$pair] = false;
            }
        }

        foreach ($this->override as $override) {
            foreach ($override['pairs'] as $pair) {
                $this->orderTriggered[$pair] = false;
            }
        }
    }

    private function validateParameters()
    {
        if (empty($this->pairs) && empty($this->override)) {
            $this->error('No valid trading pairs or overrides found in file.');

            return false;
        }

        return true;
    }

    private function startWebsocket()
    {
        $this->websocketClient = new FuturesWebsocket();
        $callbacks = [
            'message' => function ($conn, $msg) {
                $this->processWebSocketMessage($msg);
            },
            'ping' => function ($conn, $msg) {
                echo 'received ping from server'.PHP_EOL;
            },
        ];

        $callbacks = array_map(function ($callback) {
            return $callback->bindTo($this);
        }, $callbacks);

        $this->websocketClient->markPrices($callbacks);
    }

    private function processWebSocketMessage($msg)
    {
        $this->evaluateDirection();
    }

    private function evaluateDirection()
    {
        $signal = Signal::firstWhere('pair', 'BTCUSDT');
        if (! $signal) {
            return;
        }

        $side = null;
        if ($signal->last_price > $signal->previous_price && $signal->previous_price > $signal->older_price) {
            $this->info('ANALYSIS: Direction confirmed: up');
            $side = 'LONG';
        } elseif ($signal->last_price < $signal->previous_price && $signal->previous_price < $signal->older_price) {
            $this->info('ANALYSIS: Direction confirmed: down');
            $side = 'SHORT';
        } else {
            $this->info('ANALYSIS: No clear direction. Waiting for the next price update.');

            return;
        }

        // Only process orders for the determined side
        if (! isset($this->pairs[$side])) {
            $this->error("Computation for $side, but there are no pairs for $side");

            return;
        }

        if ($this->testMode) {
            $this->info("ACTION: Would open $side orders for pairs: ".implode(', ', $this->pairs[$side]['pairs']));
            $this->updateTestModePrices($side);
        } else {
            $this->openOrders($side);
        }

        // Exit gracefully
        exit;
    }

    private function placeOverrideOrders($side, $pairs, $amount)
    {
        if ($this->testMode) {
            $this->info("ACTION: Would open $side orders for pairs: ".implode(', ', $pairs));
            $this->updateTestModePrices($side, $pairs, $amount);
        } else {
            $this->openOrders($side, $pairs, $amount);
        }
    }

    private function updateTestModePrices($side, $pairs = null, $amount = null)
    {
        $pairs = $pairs ?? $this->pairs[$side]['pairs'];
        $amount = $amount ?? $this->pairs[$side]['amount'];

        foreach ($pairs as $pair) {
            if ($this->orderTriggered[$pair]) {
                continue;
            }

            $signal = Signal::firstWhere('pair', $pair);

            if (! $signal) {
                $this->error("Symbol not found for trading pair: $pair.");

                continue;
            }

            $pricePrecision = $signal->price_precision;
            $entryPrice = $signal->last_price;

            $stopPrice = $this->calculateStopPrice($entryPrice, $side === 'LONG' ? 'BUY' : 'SELL', $pricePrecision);

            $signal->_stop_loss_price = $stopPrice;
            $signal->_entry_price = $entryPrice;
            $signal->_last_order_position_side = $side;
            $signal->save();

            $this->info("Updated _stop_loss_price to $stopPrice and _entry_price to $entryPrice for trading pair: $pair");

            $this->orderTriggered[$pair] = true;
        }
    }

    private function openOrders($side, $pairs = null, $amount = null)
    {
        $pairs = $pairs ?? $this->pairs[$side]['pairs'];
        $amount = $amount ?? $this->pairs[$side]['amount'];

        foreach ($pairs as $pair) {
            if ($this->orderTriggered[$pair]) {
                continue;
            }

            $signal = Signal::firstWhere('pair', $pair);

            if (! $signal) {
                $this->error("Symbol not found for trading pair: $pair.");

                continue;
            }

            $pricePrecision = $signal->price_precision;
            $entryPrice = $signal->last_price;

            $this->createOrder($signal, $pricePrecision, $side, $entryPrice, $amount);
        }
    }

    private function createOrder($signal, $pricePrecision, $side, $entryPrice, $amount)
    {
        $client = new Futures();
        $orderSide = $side === 'LONG' ? 'BUY' : 'SELL';

        try {
            $tokenQuantity = $amount / $entryPrice;
            $tokenQuantity = round($tokenQuantity, $signal->quantity_precision);

            if ($tokenQuantity <= 0) {
                $this->error("Calculated quantity for trading pair: {$signal->pair} is less than or equal to zero.");

                return;
            }

            $orderParams = [
                'quantity' => number_format($tokenQuantity, $signal->quantity_precision, '.', ''),
                'newOrderRespType' => 'RESULT',
            ];

            $orderType = 'MARKET';
            $orderResponse = $client->newOrder($signal->pair, $orderSide, $orderType, $orderParams);

            $stopPrice = $this->calculateStopPrice($entryPrice, $side, $pricePrecision);

            $signal->_stop_loss_price = $stopPrice;
            $signal->_entry_price = $entryPrice;
            $signal->_last_market_client_order_id = $orderResponse['clientOrderId'];
            $signal->_last_order_side = $orderResponse['side'];
            $signal->_last_order_price = $orderResponse['price'];
            $signal->_last_order_quantity = $orderResponse['executedQty'];
            $signal->_last_order_position_side = $side;
            $signal->save();

            $this->info("ACTION: {$orderType} order created for trading pair: {$signal->pair} with amount: $amount USDT, side: $side, entry price: $entryPrice");

            $this->createStopMarketOrder($client, $signal, $stopPrice, $side, $tokenQuantity);

            $this->orders[] = $orderResponse;
            $this->orderTriggered[$signal->pair] = true;
        } catch (\Exception $e) {
            $this->error("Failed to create order for trading pair: {$signal->pair}. Error: ".$e->getMessage());
        }
    }

    private function createStopMarketOrder($client, $signal, $stopPrice, $side, $quantity)
    {
        $stopOrderSide = $side === 'LONG' ? 'SELL' : 'BUY';

        try {
            $stopOrderParams = [
                'stopPrice' => $stopPrice,
                'closePosition' => 'true',
                'newOrderRespType' => 'RESULT',
            ];

            $stopOrderResponse = $client->newOrder($signal->pair, $stopOrderSide, 'STOP_MARKET', $stopOrderParams);

            $this->info("ACTION: STOP_MARKET order created for trading pair: {$signal->pair} with stop price: $stopPrice");

            $signal->_last_stop_market_client_order_id = $stopOrderResponse['clientOrderId'];
            $signal->_last_order_side = $stopOrderSide;
            $signal->_last_order_price = $stopPrice;
            $signal->_last_order_quantity = $quantity;
            $signal->_last_order_position_side = $side;
            $signal->save();

            $this->orders[] = $stopOrderResponse;
        } catch (\Exception $e) {
            $this->error("Failed to create STOP_MARKET order for trading pair: {$signal->pair}. Error: ".$e->getMessage());
        }
    }

    private function calculateStopPrice($entryPrice, $side, $pricePrecision)
    {
        $stopLossValue = $entryPrice * ($this->stopLossPercentage / 100);

        return $side === 'LONG' ? round($entryPrice - $stopLossValue, $pricePrecision) : round($entryPrice + $stopLossValue, $pricePrecision);
    }

    private function parseOverrideLine($line)
    {
        $line = str_replace('OVERRIDE:', '', $line);
        [$side, $pairsStr, $amount] = explode('-', $line);
        $pairs = explode(',', str_replace(' ', '', $pairsStr));
        $amount = floatval($amount);

        $this->override[] = [
            'side' => strtoupper($side),
            'pairs' => $pairs,
            'amount' => $amount,
        ];
    }

    private function parseTradingLine($line)
    {
        [$side, $pairsStr, $amount] = explode('-', $line);
        $pairs = explode(',', str_replace(' ', '', $pairsStr));
        $amount = floatval($amount);

        if (! isset($this->pairs[strtoupper($side)])) {
            $this->pairs[strtoupper($side)] = [
                'pairs' => [],
                'amount' => $amount,
            ];
        }

        $this->pairs[strtoupper($side)]['pairs'] = array_merge($this->pairs[strtoupper($side)]['pairs'], $pairs);
    }
}
