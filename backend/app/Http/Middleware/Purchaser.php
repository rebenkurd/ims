<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class Purchaser
{
    public function handle(Request $request, Closure $next): Response
    {
        if(Auth::check() && Auth::user()->role_id == 3) {
            return $next($request);
        }

        return response()->json(['message' => 'You don\'t have permission to perform this action!'], 403);
    }
}
