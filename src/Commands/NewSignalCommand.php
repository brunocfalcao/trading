<?php

namespace Brunocfalcao\Trading\Commands;

use Brunocfalcao\LaravelHelpers\Traits\ForCommands\CanValidateArguments;
use Illuminate\Console\Command;

class NewSignalCommand extends Command
{
    use CanValidateArguments;

    protected $signature = 'trading:new-signal';

    protected $description = 'Adds a new trading signal (mostly for testing purposes)';

    public function handle()
    {
        $pair = $this->askWithRules('Pair (e.g.: BTCUSDT)', ['required']);

        $pair = strtoupper($pair);
    }
}
