<?php

namespace App\Http\Controllers\UserManagement;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Responses\ApiResponse;
use Spatie\Permission\Models\Permission;
use App\Http\Requests\UserManagement\PermissionRequest;
use App\Http\Resources\UserManagement\PermissionResource;

class PermissionController extends Controller
{
    public function index()
    {
        $permissions = Permission::all();
        return ApiResponse::success(PermissionResource::collection($permissions), 'Permissions retrieved successfully');
    }

    public function store(PermissionRequest $request)
    {
        $permission = Permission::create([
            'name' => $request->name,
            'guard_name' => 'api'
        ]);

        return ApiResponse::success(new PermissionResource($permission), 'Permission created successfully', 201);
    }

    public function show(Permission $permission)
    {
        return ApiResponse::success(new PermissionResource($permission), 'Permission retrieved successfully');
    }

    public function update(PermissionRequest $request, Permission $permission)
    {
        $permission->update(['name' => $request->name]);

        return ApiResponse::success(new PermissionResource($permission), 'Permission updated successfully');
    }

    public function destroy(Permission $permission)
    {
        $permission->delete();
        return ApiResponse::success(null, 'Permission deleted successfully');
    }
}
