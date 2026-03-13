<?php

namespace App\Http\Controllers;

use App\Models\NavItem;
use App\Http\Responses\ApiResponse;
use App\Http\Requests\NavItemStoreRequest;
use App\Http\Requests\NavItemUpdateRequest;
use App\Http\Resources\NavItemResource;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class NavItemController extends Controller
{
    // ✅ Fetch nav items for the currently logged-in user (by their role)
    public function forCurrentUser()
    {
        $user     = auth('api')->user();
        $roleIds  = $user->roles->pluck('id');

        // Get top-level items only (parent_id null), children loaded via relationship
        $navItems = NavItem::whereNull('parent_id')
            ->where('is_active', true)
            ->whereHas('roles', fn($q) => $q->whereIn('roles.id', $roleIds))
            ->with([
                'children' => function ($q) use ($roleIds) {
                    $q->whereHas('roles', fn($q2) => $q2->whereIn('roles.id', $roleIds));
                }
            ])
            ->orderBy('section')
            ->orderBy('order')
            ->get();

        // Group by section to match your frontend NavSection structure
        $grouped = $navItems->groupBy('section')->map(function ($items, $section) {
            return [
                'title' => $section,
                'items' => NavItemResource::collection($items)
            ];
        })->values();

        return ApiResponse::success($grouped);
    }

    // ✅ List all nav items (super-admin management)
    public function index()
    {
        $items = NavItem::with('children', 'roles')->orderBy('section')->orderBy('order')->get();
        return ApiResponse::success(NavItemResource::collection($items));
    }

    // ✅ Create nav item
    public function store(NavItemStoreRequest $request)
    {
        $data = $request->validated();

        $item = NavItem::create($data);
        $item->roles()->sync($data['role_ids']);

        return ApiResponse::success(new NavItemResource($item->load('roles', 'children')), 'Nav item created', 201);
    }

    // ✅ Update nav item
    public function update(NavItemUpdateRequest $request, $id)
    {
        $item = NavItem::findOrFail($id);

        $data = $request->validated();

        $item->update($data);

        if (isset($data['role_ids'])) {
            $item->roles()->sync($data['role_ids']);
        }

        return ApiResponse::success(new NavItemResource($item->load('roles', 'children')), 'Nav item updated');
    }

    // ✅ Delete nav item
    public function destroy($id)
    {
        NavItem::findOrFail($id)->delete(); // cascade deletes children too
        return ApiResponse::success([], 'Nav item deleted');
    }
}