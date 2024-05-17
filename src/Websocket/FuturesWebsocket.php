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

    //https://developers.binance.com/docs/derivatives/usds-margined-futures/websocket-market-streams/Mark-Price-Stream-for-All-market
    public function markPrices($callback)
    {
        $url = "{$this->baseURL}/ws/!markPrice@arr@1s";
        $this->handleCallBack($url, $callback);
    }

    //https://developers.binance.com/docs/derivatives/usds-margined-futures/websocket-market-streams/Mark-Price-Stream
    public function markPrice(string $symbol, $callback)
    {
        $url = "{$this->baseURL}/ws/".strtolower($symbol).'@markPrice@1s';
        $this->handleCallBack($url, $callback);
    }
}
