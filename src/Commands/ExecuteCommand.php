<?php

namespace Brunocfalcao\Trading\Commands;

use Illuminate\Console\Command;

class ExecuteCommand extends Command
{
    protected $signature = 'trading:execute';

    protected $description = 'Executes trading logic based on the mark prices updates (each second)';

    public function handle()
    {
    }
}
