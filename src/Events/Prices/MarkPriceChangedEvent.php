<?php

namespace Brunocfalcao\Trading\Events\Prices;

use Brunocfalcao\Trading\Models\Price;

class MarkPriceChangedEvent
{
    public Price $price;

    public function __construct(Price $price)
    {
        $this->price = $price;
    }
}
