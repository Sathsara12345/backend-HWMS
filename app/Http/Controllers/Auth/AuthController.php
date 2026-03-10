<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\Auth\LoginResource;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(LoginRequest $request)
    {
        $credentials = $request->validated();

        if (!$token = auth('api')->attempt($credentials)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials'
            ], 401);
        }

        $user = auth('api')->user();
        
        return (new LoginResource([
            'user' => $user->load('roles'),
            'permissions' => $user->getAllPermissions()->pluck('name'),
            'token' => $token
        ]))->additional([
            'success' => true,
            'message' => 'Login successful'
        ]);
    }
}
