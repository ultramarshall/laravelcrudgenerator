<?php

namespace mixtra\middlewares;

use Closure;
use MITBooster;

class MITAuthAPI
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {


        MITBooster::authAPI();

        return $next($request);
    }
}
