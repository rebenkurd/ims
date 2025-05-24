<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\AuthResource;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AuthenticatedSessionController extends Controller
{
    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request)
    {
        try{
            $request->authenticate();
            Log::debug($request->authenticate());
            $request->session()->regenerate();

            return response([
                'user' => new AuthResource(Auth::user()),
                'token' => $request->user()->createToken('authToken')->plainTextToken,
                'role_id' => Auth::user()->role_id,
            ]);
        }catch (\Exception $e) {
            return response([
                "message" =>  $e->getMessage()
            ], 500);
        }
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): Response
    {
        Auth::guard('web')->logout();
    $request->user()->tokens()->delete();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return response()->noContent();
    }
}
