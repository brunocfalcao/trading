<?php

namespace Brunocfalcao\Trading;

use Binance\APIClient;
use Brunocfalcao\Trading\Futures\Market;
use Brunocfalcao\Trading\Futures\Trade;

class Futures extends APIClient
{
    use Market;
    use Trade;

    public function __construct(array $args = [])
    {
        $args['baseURL'] = $args['baseURL'] ?? 'https://fapi.binance.com';
        $args['key'] = env('BINANCE_API_KEY');
        $args['secret'] = env('BINANCE_SECRET_KEY');
        parent::__construct($args);
    }
}
