<?php

namespace Brunocfalcao\Trading\Websocket;

use Binance\Websocket;

class FuturesWebsocket extends Websocket
{
    public function __construct(array $args = [])
    {
        $args['baseURL'] = $args['baseURL'] ?? 'wss://fstream.binance.com';
        parent::__construct($args);
    }

    public function markPrices($callback)
    {
        $url = "{$this->baseURL}/ws/!markPrice@arr@1s";
        $this->handleCallBack($url, $callback);
    }

    public function markPrice(string $symbol, $callback)
    {
        $url = "{$this->baseURL}/ws/".strtolower($symbol).'@markPrice@1s';
        $this->handleCallBack($url, $callback);
    }
}
