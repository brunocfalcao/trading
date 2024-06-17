<?php

namespace Brunocfalcao\Trading\Http\Controllers;

use App\Http\Controllers\Controller;
use Brunocfalcao\Trading\Models\Signal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;

class TradingController extends Controller
{
    public function index()
    {
        $fileContent = Storage::get('trading/pairs.txt');
        $signal = Signal::firstWhere('pair', 'BTCUSDT'); // Fetch the specific Signal data

        return view('trading::index', [
            'fileContent' => $fileContent,
            'signal' => $signal,
            'commandOutput' => session('commandOutput'),
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
        $result = Artisan::call('trading:place-orders-file');

        info(json_encode($result));

        return response()->json(['output' => $result, 'error' => false], 200);
    }
}
