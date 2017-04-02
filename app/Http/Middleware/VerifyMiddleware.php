<?php

namespace App\Http\Middleware;

use Closure;

class VerifyMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($request->input("hub.mode") === "subscribe"
            && $request->input("hub.verify_token") === env("MESSENGER_VERIFY_TOKEN")) {
            return response($request->input("hub.challenge"), 200);
        }
        return $next($request);
    }
}
