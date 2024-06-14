<?php

namespace Brunocfalcao\Trading\Commands;

use Illuminate\Console\Command;
use Brunocfalcao\Trading\Futures;
use Brunocfalcao\Trading\Models\Symbol;
use Brunocfalcao\Trading\Websocket\FuturesWebsocket;

class TradeCommand extends Command
{
    // Define the command signature with arguments for pairs, amount (in USDT), and optional price, and a test flag
    protected $signature = 'trade {pairs} {amount} {price?} {--test}';

    // Description of the command
    protected $description = 'Trades based on BTCUSDT price action fluctuations';

    // Properties to store trading pairs, prices, amount, stop loss percentage, and orders
    private $pairs = [];
    private $amount;
    private $price;
    private $stopLossPercentage = 0.1; // Default stop loss percentage
    private $orders = [];
    private $testMode = false;
    private $orderTriggered = [];
    private $allOpenOrdersDone = false;
    private $websocketClient;

    public function __construct()
    {
        parent::__construct();
    }

    // Main handle function for the command
    public function handle()
    {
        $this->initializeParameters();

        if (!$this->validateParameters()) {
            return;
        }

        $this->startWebsocket();
    }

    // Initialize command parameters
    private function initializeParameters()
    {
        $this->pairs = explode(',', str_replace(' ', '', $this->argument('pairs')));
        $this->amount = floatval($this->argument('amount'));
        $this->price = $this->argument('price') ? floatval($this->argument('price')) : null;
        $this->testMode = $this->option('test');
        $this->stopLossPercentage = config('trading.stop_loss_percentage', 0.1);

        // Initialize orderTriggered array
        foreach ($this->pairs as $pair) {
            $this->orderTriggered[$pair] = false;
        }
    }

    // Validate command parameters
    private function validateParameters()
    {
        if (empty($this->pairs) || !$this->amount) {
            $this->error('Missing required arguments: pairs or amount.');
            return false;
        }

        return true;
    }

    // Start the websocket connection and handle incoming messages
    private function startWebsocket()
    {
        $this->websocketClient = new FuturesWebsocket();
        $callbacks = [
            'message' => function ($conn, $msg) {
                if (!$this->allOpenOrdersDone) {
                    $this->processWebSocketMessage($msg);
                } else {
                    $conn->close();
                }
            },
            'ping' => function ($conn, $msg) {
                echo 'received ping from server' . PHP_EOL;
            },
        ];

        // Bind the current instance to the callbacks
        $callbacks = array_map(function ($callback) {
            return $callback->bindTo($this);
        }, $callbacks);

        // Start receiving market prices
        $this->websocketClient->markPrices($callbacks);
    }

    // Process the websocket message
    private function processWebSocketMessage($msg)
    {
        $this->evaluateDirection();
    }

    // Evaluate the direction based on the prices
    private function evaluateDirection()
    {
        $symbol = Symbol::firstWhere('pair', 'BTCUSDT');
        if (!$symbol) {
            return;
        }

        if ($symbol->last_price > $symbol->previous_price && $symbol->previous_price > $symbol->older_price) {
            $this->info("ANALYSIS: Direction confirmed: up");
            $side = 'LONG';
        } elseif ($symbol->last_price < $symbol->previous_price && $symbol->previous_price < $symbol->older_price) {
            $this->info("ANALYSIS: Direction confirmed: down");
            $side = 'SHORT';
        } else {
            $this->info("ANALYSIS: No clear direction. Waiting for the next price update. Prices: last_price={$symbol->last_price}, previous_price={$symbol->previous_price}, older_price={$symbol->older_price}");
            return;
        }

        if ($this->testMode) {
            $this->info("ACTION: Would open $side orders for pairs: " . implode(', ', $this->pairs));
            $this->updateTestModePrices($side);
        } else {
            $this->openOrders($side);
        }

        // Set flag to indicate all orders are done
        $this->allOpenOrdersDone = true;
    }

    // Update prices in test mode
    private function updateTestModePrices($side)
    {
        foreach ($this->pairs as $pair) {
            if ($this->orderTriggered[$pair]) {
                continue;
            }

            $symbol = Symbol::firstWhere('pair', $pair);

            if (!$symbol) {
                $this->error("Symbol not found for trading pair: $pair.");
                continue;
            }

            $pricePrecision = $symbol->price_precision;
            $entryPrice = $this->price ?? $symbol->last_price;

            $stopPrice = $this->calculateStopPrice($entryPrice, $side, $pricePrecision);

            // Update the stop loss price and entry price in the Symbol model
            $symbol->_stop_loss_price = $stopPrice;
            $symbol->_entry_price = $entryPrice;
            $symbol->save();

            $this->info("Updated _stop_loss_price to $stopPrice and _entry_price to $entryPrice for trading pair: $pair");

            $this->orderTriggered[$pair] = true;
        }
    }

    // Open orders based on the determined side
    private function openOrders($side)
    {
        foreach ($this->pairs as $pair) {
            if ($this->orderTriggered[$pair]) {
                continue;
            }

            $symbol = Symbol::firstWhere('pair', $pair);

            if (!$symbol) {
                $this->error("Symbol not found for trading pair: $pair.");
                continue;
            }

            $pricePrecision = $symbol->price_precision;
            $entryPrice = $this->price ?? $symbol->last_price;

            $this->createOrder($symbol, $pricePrecision, $side, $entryPrice);
        }
    }

    // Create a new order and set a stop loss if necessary
    private function createOrder($symbol, $pricePrecision, $side, $entryPrice)
    {
        $client = new Futures();
        $orderSide = $side === 'LONG' ? 'BUY' : 'SELL';

        try {
            $tokenQuantity = $this->amount / $entryPrice;
            $tokenQuantity = round($tokenQuantity, $symbol->quantity_precision);

            if ($tokenQuantity <= 0) {
                $this->error("Calculated quantity for trading pair: {$symbol->pair} is less than or equal to zero.");
                return;
            }

            $orderParams = [
                'quantity' => number_format($tokenQuantity, $symbol->quantity_precision, '.', ''),
                'newOrderRespType' => 'RESULT'
            ];

            if ($this->price) {
                $orderParams['price'] = $this->price;
                $orderParams['timeInForce'] = 'GTC'; // Good 'Til Canceled
            }

            $orderType = $this->price ? 'LIMIT' : 'MARKET';
            $orderResponse = $client->newOrder($symbol->pair, $orderSide, $orderType, $orderParams);

            $stopPrice = $this->calculateStopPrice($entryPrice, $side, $pricePrecision);

            // Update the stop loss price and entry price in the Symbol model
            $symbol->_stop_loss_price = $stopPrice;
            $symbol->_entry_price = $entryPrice;
            $symbol->save();

            $this->info("ACTION: {$orderType} order created for trading pair: {$symbol->pair} with amount: {$this->amount} USDT, side: $side, entry price: $entryPrice");

            // For MARKET orders, create an additional STOP_MARKET order
            if ($orderType === 'MARKET') {
                $this->createStopMarketOrder($client, $symbol, $stopPrice, $side);
            }

            $this->orders[] = $orderResponse;
            $this->orderTriggered[$symbol->pair] = true;
        } catch (\Exception $e) {
            $this->error("Failed to create order for trading pair: {$symbol->pair}. Error: " . $e->getMessage());
        }
    }

    // Create a STOP_MARKET order
    private function createStopMarketOrder($client, $symbol, $stopPrice, $side)
    {
        $stopOrderSide = $side === 'LONG' ? 'SELL' : 'BUY';

        try {
            $stopOrderParams = [
                'stopPrice' => $stopPrice,
                'closePosition' => 'true',
                'newOrderRespType' => 'RESULT'
            ];

            $stopOrderResponse = $client->newOrder($symbol->pair, $stopOrderSide, 'STOP_MARKET', $stopOrderParams);

            $this->info("ACTION: STOP_MARKET order created for trading pair: {$symbol->pair} with stop price: $stopPrice");

            $this->orders[] = $stopOrderResponse;
        } catch (\Exception $e) {
            $this->error("Failed to create STOP_MARKET order for trading pair: {$symbol->pair}. Error: " . $e->getMessage());
        }
    }

    // Calculate the stop price based on the mark price and side
    private function calculateStopPrice($markPrice, $side, $pricePrecision)
    {
        $stopLossValue = round($markPrice * ($this->stopLossPercentage / 100), $pricePrecision);
        return $side === 'LONG' ? round($markPrice - $stopLossValue, $pricePrecision) : round($markPrice + $stopLossValue, $pricePrecision);
    }
}
