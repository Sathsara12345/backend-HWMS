<?php

namespace App\Http\Controllers\UserManagement;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use App\Http\Resources\UserManagement\PermissionResource;
use App\Http\Requests\UserManagement\PermissionRequest;

class PermissionController extends Controller
{
    public function index()
    {
        $permissions = Permission::all();
        return PermissionResource::collection($permissions)->additional([
            'success' => true,
            'message' => 'Permissions retrieved successfully'
        ]);
    }

    public function store(PermissionRequest $request)
    {
        $permission = Permission::create([
            'name' => $request->name,
            'guard_name' => 'api'
        ]);

        return (new PermissionResource($permission))->additional([
            'success' => true,
            'message' => 'Permission created successfully'
        ])->response()->setStatusCode(201);
    }

    public function show(Permission $permission)
    {
        return (new PermissionResource($permission))->additional([
            'success' => true,
        ]);
    }

    public function update(PermissionRequest $request, Permission $permission)
    {
        $permission->update(['name' => $request->name]);

        return (new PermissionResource($permission))->additional([
            'success' => true,
            'message' => 'Permission updated successfully'
        ]);
    }

    public function destroy(Permission $permission)
    {
        $permission->delete();
        return response()->json([
            'success' => true,
            'message' => 'Permission deleted successfully'
        ]);
    }
}
