<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class Role
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!Auth::check()) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $userRoleId = Auth::user()->role_id;
        $allowedRoles = array_map('intval', $roles);

        if (in_array($userRoleId, $allowedRoles)) {
            return $next($request);
        }

        return response()->json(['message' => 'You don\'t have permission to perform this action!'], 403);
    }
}
