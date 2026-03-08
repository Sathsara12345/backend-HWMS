<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use App\Http\Responses\ApiResponse;

class PermissionController extends Controller
{
    public function index()
    {
        $permissions = Permission::all();
        return ApiResponse::success($permissions, 'Permissions retrieved successfully');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:permissions,name',
        ]);

        $permission = Permission::create([
            'name' => $request->name,
            'guard_name' => 'api'
        ]);

        return ApiResponse::success($permission, 'Permission created successfully', 201);
    }

    public function update(Request $request, Permission $permission)
    {
        $request->validate([
            'name' => 'required|unique:permissions,name,' . $permission->id,
        ]);

        $permission->update(['name' => $request->name]);

        return ApiResponse::success($permission, 'Permission updated successfully');
    }

    public function destroy(Permission $permission)
    {
        $permission->delete();
        return ApiResponse::success(null, 'Permission deleted successfully');
    }
}
