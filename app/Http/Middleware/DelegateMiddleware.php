<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use App\Models\Delegate;

class DelegateMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::guard('api')->check()) {
            abort(401, 'Unauthenticated, please login first.');
        }
        if (!Auth::uer()->userable instanceof Delegate) {
            abort(403, 'Sorry, Access not allowed for you, '.Auth::uer()->name);
        }
        return $next($request);
    }
}
