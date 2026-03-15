<?php

namespace App\Http\Controllers\UserManagement;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Http\Responses\ApiResponse;
use Spatie\Permission\Models\Permission;
use App\Http\Requests\UserManagement\RoleRequest;
use App\Http\Resources\UserManagement\RoleResource;
use App\Http\Resources\UserManagement\PermissionResource;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::with('permissions')->get();
        return ApiResponse::success(RoleResource::collection($roles), 'Roles retrieved successfully');
    }

    public function store(RoleRequest $request)
    {
        $role = Role::create([
            'name' => $request->name,
            'guard_name' => 'api'
        ]);

        return ApiResponse::success(new RoleResource($role), 'Role created successfully', 201);
    }

    public function show(Role $role)
    {
        return ApiResponse::success(new RoleResource($role->load('permissions')), 'Role retrieved successfully');
    }

    public function update(RoleRequest $request, Role $role)
    {
        $role->update(['name' => $request->name]);

        return ApiResponse::success(new RoleResource($role), 'Role updated successfully');
    }

    public function destroy(Role $role)
    {
        $role->delete();
        return ApiResponse::success(null, 'Role deleted successfully');
    }

    public function getAllPermissions()
    {
        $permissions = Permission::all();
        return ApiResponse::success(PermissionResource::collection($permissions), 'Permissions retrieved successfully');
    }

    /**
     * Assign common permissions to a role
     */
    public function assignPermissions(Request $request, Role $role)
    {
        $request->validate([
            'permissions' => 'present|array',
        ]);

        // Sync permissions directly to the role
        $role->syncPermissions($request->permissions);

        return ApiResponse::success(new RoleResource($role->load('permissions')), 'Common permissions updated for ' . $role->name . ' role');
    }

    /**
     * Assign user-specific permissions
     */
    public function assignPermissionsToUser(Request $request, $userId)
    {
        $request->validate([
            'permissions' => 'present|array',
        ]);

        $user = User::find($userId);
        if (!$user) {
            return ApiResponse::error('User not found', 404);
        }

        // Sync permissions directly to the user
        $user->syncPermissions($request->permissions);

        return ApiResponse::success([
            'user_id' => $user->id,
            'name' => $user->name,
            'direct_permissions' => PermissionResource::collection($user->permissions),
            'all_permissions' => PermissionResource::collection($user->getAllPermissions()),
        ], 'User-specific permissions assigned successfully');
    }
}
