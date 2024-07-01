<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use App\Models\Admin;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::guard('api')->check()) {
            return response()->json(['error' => 'Unauthenticated, please login first.'], 401);
        }
        if (!Auth::user()->userable instanceof Admin) {
            return response()->json(['error' => 'Sorry, Access not allowed for you, '.Auth::user()->name], 403);
        }
        return $next($request);
    }
    
}
