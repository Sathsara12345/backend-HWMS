<?php

namespace App\Http\Controllers;

use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Http\Request;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Responses\ApiResponse;

class AuthController extends Controller
{
    public function login(LoginRequest $request)
    {
        $credentials = $request->validated();

        if (!$token = auth('api')->attempt($credentials)) {
            return ApiResponse::error('Invalid credentials', 401);
        }
        $user = auth('api')->user();
        return ApiResponse::success([
            'user' => $user->load('roles'),
            'permissions' => $user->getAllPermissions()->pluck('name'),
            'token' => $token
        ], 'Login successful');
    }
}