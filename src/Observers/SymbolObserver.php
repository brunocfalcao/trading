<?php

namespace Brunocfalcao\Trading\Observers;

use Brunocfalcao\Trading\Models\Symbol;

class SymbolObserver
{
    public function saving(Symbol $symbol): void
    {
        // Update previous price, if needed.
        if ($symbol->isDirty('last_price')) {
            $symbol->older_price = $symbol->getOriginal('previous_price');
            $symbol->previous_price = $symbol->getOriginal('last_price');
        }
    }
}
