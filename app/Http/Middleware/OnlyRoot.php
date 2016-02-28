<?php

namespace App\Http\Middleware;

use Closure;

class OnlyRoot
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
        if (!\Auth::user() || \Auth::user()->id != 1) {
            \App::abort(403, 'Unauthorized action.');
        }

        return $next($request);
    }
}
