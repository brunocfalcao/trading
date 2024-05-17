<?php

namespace Brunocfalcao\Trading\Events\Signals;

use Brunocfalcao\Trading\Models\Signal;

class SignalCreatedEvent
{
    public Signal $signal;

    public function __construct(Signal $signal)
    {
        $this->signal = $signal;
    }
}
