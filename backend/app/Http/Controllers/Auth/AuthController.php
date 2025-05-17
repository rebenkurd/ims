<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\AuthResource;
use App\Models\User;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function getUser(Request $request){
        return new AuthResource($request->user());
    }

}
