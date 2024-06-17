<?php

namespace Brunocfalcao\Trading\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Symfony\Component\Process\Process;
use Brunocfalcao\Trading\Models\Signal;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Exception\ProcessFailedException;

class TradingController extends Controller
{
    public function index()
    {
        $fileContent = Storage::get('trading/pairs.txt');
        $signal = Signal::firstWhere('pair', 'BTCUSDT'); // Fetch the specific Signal data

        return view('trading::index', [
            'fileContent' => $fileContent,
            'signal' => $signal,
            'commandOutput' => session('commandOutput')
        ]);
    }

    public function refreshFile()
    {
        return redirect()->route('index');
    }

    public function updateFile(Request $request)
    {
        Storage::put('trading/pairs.txt', $request->input('fileContent'));
        return redirect()->route('index');
    }

    public function getLatestPrices()
    {
        $signal = Signal::firstWhere('pair', 'BTCUSDT'); // Fetch the specific Signal data
        return response()->json($signal);
    }

    public function runCommand()
    {
        $process = new Process(['php', 'artisan', 'trading:place-orders-file']);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        $output = $process->getOutput();
        return redirect()->route('index')->with('commandOutput', $output);
    }
}
