<?php

namespace App\Http\Controllers\UserManagement;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Http\Resources\UserManagement\RoleResource;
use App\Http\Resources\UserManagement\PermissionResource;
use App\Http\Requests\UserManagement\RoleRequest;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::with('permissions')->get();
        return RoleResource::collection($roles)->additional([
            'success' => true,
            'message' => 'Roles retrieved successfully'
        ]);
    }

    public function store(RoleRequest $request)
    {
        $role = Role::create([
            'name' => $request->name,
            'guard_name' => 'api'
        ]);

        return (new RoleResource($role))->additional([
            'success' => true,
            'message' => 'Role created successfully'
        ])->response()->setStatusCode(201);
    }

    public function show(Role $role)
    {
        return (new RoleResource($role->load('permissions')))->additional([
            'success' => true,
        ]);
    }

    public function update(RoleRequest $request, Role $role)
    {
        $role->update(['name' => $request->name]);

        return (new RoleResource($role))->additional([
            'success' => true,
            'message' => 'Role updated successfully'
        ]);
    }

    public function destroy(Role $role)
    {
        $role->delete();
        return response()->json([
            'success' => true,
            'message' => 'Role deleted successfully'
        ]);
    }

    public function getAllPermissions()
    {
        $permissions = Permission::all();
        return PermissionResource::collection($permissions)->additional([
            'success' => true,
            'message' => 'Permissions retrieved successfully'
        ]);
    }

    public function assignPermissions(Request $request, Role $role)
    {
        $request->validate([
            'permissions' => 'required|array',
        ]);

        $role->syncPermissions($request->permissions);

        return (new RoleResource($role->load('permissions')))->additional([
            'success' => true,
            'message' => 'Permissions assigned successfully'
        ]);
    }
}
