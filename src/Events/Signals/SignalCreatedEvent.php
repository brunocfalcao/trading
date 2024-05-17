<?php

namespace Brunocfalcao\Trading\Events\Signals;

use Brunocfalcao\Trading\Models\Signal;

class SignalEvent
{
    public Signal $signal;

    public function __construct(Signal $signal)
    {
        $this->signal = $signal;
    }
}
