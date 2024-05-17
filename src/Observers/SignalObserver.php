<?php

namespace Brunocfalcao\Trading\Observers;

use Brunocfalcao\Trading\Models\Signal;

class SignalObserver
{
    public function created(Signal $signal): void
    {
        // Trigger a signal created event, so we can start the order process.
    }
}
