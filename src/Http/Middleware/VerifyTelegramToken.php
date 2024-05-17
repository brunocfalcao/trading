<?php

namespace Brunocfalcao\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

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

        if ($providedToken !== $secretToken) {
            return response()->json(['status' => 'forbidden'], 403);
        }

        return $next($request);
    }
}