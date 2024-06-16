<?php

namespace Brunocfalcao\Trading\Observers;

use Brunocfalcao\Trading\Models\Signal;

class SignalObserver
{
    public function saving(Signal $signal): void
    {
        // Update previous price, if needed.
        if ($signal->isDirty('last_price')) {
            $signal->older_price = $signal->getOriginal('previous_price');
            $signal->previous_price = $signal->getOriginal('last_price');
        }
    }
}
