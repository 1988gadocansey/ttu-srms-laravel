<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class Authenticate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
//        if (Auth::guard($guard)->guest()) {
//            if ($request->ajax() || $request->wantsJson()) {
//                return response('Unauthorized.', 401);
//            } else {
//                return redirect()->guest('login');
//            }
//        }
//
//        return $next($request);
          // This is returning false for web pages but true for ajax requests
        if (Auth::guard($guard)->guest()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response([
                    'error' => 'unauthorized',
                    'error_description' => 'Failed authentication.',
                    'data' => [],
                ], 401);
            } else {

                $request->session()->flash('warning', 'You\'re not authorized to view that!');

                return redirect()->guest('login');
            }
        }
        return $next($request);
    }
}
