<?php

namespace Brunocfalcao\Trading\Observers;

use Brunocfalcao\Trading\Models\Price;

class PriceObserver
{
    public function saving(Price $price): void
    {
        // Update previous price, if needed.
        if ($price->isDirty('mark_price')) {
            $price->previous_price = $price->getOriginal('mark_price');

            //If there are active others with this pair, trigger a trade event.
        }
    }
}
