<?php

namespace Brunocfalcao\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class VerifyTelegramToken
{
    /**
     * Handle an incoming request.
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $secretToken = env('TELEGRAM_SECRET_TOKEN');
        $providedToken = $request->header('X-Telegram-Bot-Api-Secret-Token');

        Log::info('-- Request forbidden from Telegram -- ');

        if ($providedToken !== $secretToken) {
            return response()->json(['status' => 'forbidden'], 403);
        }

        return $next($request);
    }
}
