<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Http\Responses\ApiResponse;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::with('permissions')->get();
        return ApiResponse::success($roles, 'Roles retrieved successfully');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:roles,name',
        ]);

        $role = Role::create([
            'name' => $request->name,
            'guard_name' => 'api'
        ]);

        return ApiResponse::success($role, 'Role created successfully', 201);
    }

    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name' => 'required|unique:roles,name,' . $role->id,
        ]);

        $role->update(['name' => $request->name]);

        return ApiResponse::success($role, 'Role updated successfully');
    }

    public function destroy(Role $role)
    {
        $role->delete();
        return ApiResponse::success(null, 'Role deleted successfully');
    }

    public function getAllPermissions()
    {
        $permissions = Permission::all();
        return ApiResponse::success($permissions, 'Permissions retrieved successfully');
    }

    public function assignPermissions(Request $request, Role $role)
    {
        $request->validate([
            'permissions' => 'required|array',
        ]);

        $role->syncPermissions($request->permissions);

        return ApiResponse::success($role->load('permissions'), 'Permissions assigned successfully');
    }
}
