<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class Role
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
public function handle($request, Closure $next, ...$roles)
{
    if (!Auth::check()) {
        abort(403);
    }

    if (!in_array(Auth::user()->role_id, $roles)) {
        abort(403);
    }

    return $next($request);
}
}
