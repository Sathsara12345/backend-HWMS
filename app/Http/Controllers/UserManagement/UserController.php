<?php

namespace App\Http\Controllers\UserManagement;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Responses\ApiResponse;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\UserManagement\UserRequest;
use App\Http\Resources\UserManagement\UserResource;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('roles')->get();
        return ApiResponse::success(UserResource::collection($users), 'Users retrieved successfully');
    }

    public function store(UserRequest $request)
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        if ($request->has('roles')) {
            $user->syncRoles($request->roles);
        }

        return ApiResponse::success(new UserResource($user->load('roles')), 'User created successfully', 201);
    }

    public function show(User $user)
    {
        return ApiResponse::success(new UserResource($user->load(['roles', 'permissions'])), 'User retrieved successfully');
    }

    public function update(UserRequest $request, User $user)
    {
        $user->name = $request->name;
        $user->email = $request->email;
        if ($request->password) {
            $user->password = Hash::make($request->password);
        }
        $user->save();

        if ($request->has('roles')) {
            $user->syncRoles($request->roles);
        }

        return ApiResponse::success(new UserResource($user->load('roles')), 'User updated successfully');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return ApiResponse::success(null, 'User deleted successfully');
    }
}
