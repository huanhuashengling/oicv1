<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DistrictAuthMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // dd($route . " --  ". $guard);
        // dd(Auth::guard("teacher")->guest());
        if (Auth::guard("district")->guest()) {
            // if ($request->ajax() || $request->wantsJson()) {
                // return response('Unauthorized.', 401);
            // } else {
                return redirect()->guest('district/login');
            // }
        }
        return $next($request);
    }
}
