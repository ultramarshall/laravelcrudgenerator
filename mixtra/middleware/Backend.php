<?php

namespace mixtra\middlewares;

use Closure;
use MITBooster;

class Backend
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
        $admin_path = config('mixtra.ADMIN_PATH') ?: 'admin';

        if (MITBooster::myId() == '') {
            $url = url($admin_path.'/login');

            return redirect($url)->with('message', 'You are not logged in !');
        }
        if (MITBooster::isLocked()) {
            $url = url($admin_path.'/lock-screen');

            return redirect($url);
        }

        return $next($request);
    }
}
