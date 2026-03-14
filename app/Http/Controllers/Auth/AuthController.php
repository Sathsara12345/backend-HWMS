<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Responses\ApiResponse;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\Auth\LoginResource;

class AuthController extends Controller
{
    public function login(LoginRequest $request)
    {
        $credentials = $request->validated();

        if (!$token = auth('api')->attempt($credentials)) {
            return ApiResponse::error('Invalid credentials', 401);
        }

        $user = auth('api')->user();
        
        $data = new LoginResource([
            'user' => $user->load('roles'),
            'permissions' => $user->getAllPermissions()->pluck('name'),
            'token' => $token
        ]);

        return ApiResponse::success($data, 'Login successful');
    }
}
