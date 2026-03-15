<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Hotel;
use Illuminate\Support\Str;
use App\Models\PageSection;
use Illuminate\Http\Request;
use App\Models\NavigationItem;
use Illuminate\Support\Facades\DB;
use App\Http\Responses\ApiResponse;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\MerchantRequest;
use App\Http\Resources\MerchantResource;

class MerchantController extends Controller
{
    /**
     * List all merchant admins with their hotels
     */
    public function index()
    {
        $admins = User::role('admin')->with(['hotel', 'roles'])->get();
        return ApiResponse::success(MerchantResource::collection($admins), 'Merchants retrieved successfully');
    }

    /**
     * Get single merchant
     */
    public function show($id)
    {
        $admin = User::role('admin')->with(['hotel', 'roles'])->findOrFail($id);
        return ApiResponse::success(new MerchantResource($admin), 'Merchant retrieved successfully');
    }

    /**
     * Create merchant admin + hotel
     */
    public function store(MerchantRequest $request)
    {
        $merchant = null;
        
        DB::transaction(function () use ($request, &$merchant) {
            // 1. Create the user account
            $merchant = User::create([
                'name'     => $request->name,
                'email'    => $request->email,
                'password' => Hash::make($request->password),
            ]);

            // 2. Assign admin role
            $merchant->assignRole('admin');

            // 3. Assign common permissions
            $merchant->givePermissionTo('view-dashboard');

            // 4. Create hotel record linked to this user
            $hotel = $merchant->hotel()->create([
                'hotel_name' => $request->hotel_name,
                'domain'     => $request->domain,
                'email'      => $request->email,
                'phone'      => $request->phone,
                'status'     => 'active',
            ]);

            // 5. Seed common website structure defaults or custom products
            if ($request->has('navigation_items') && is_array($request->navigation_items)) {
                foreach ($request->navigation_items as $item) {
                    $hotel->navigationItems()->create($item);
                }
            }
            
            if ($request->has('page_sections') && is_array($request->page_sections)) {
                foreach ($request->page_sections as $section) {
                    $hotel->pageSections()->create($section);
                }
            }

            // Fallback to defaults if none provided
            if (!$request->has('navigation_items') && !$request->has('page_sections')) {
                $hotel->seedDefaults();
            }
        });

        return ApiResponse::success(new MerchantResource($merchant->load('hotel', 'roles')), 'Merchant created successfully', 201);
    }

    /**
     * Update merchant admin + hotel
     */
    public function update(MerchantRequest $request, $id)
    {
        $admin = User::role('admin')->with('hotel')->findOrFail($id);

        DB::transaction(function () use ($request, $admin) {
            // Update user fields
            $admin->update(array_filter([
                'name'     => $request->name,
                'email'    => $request->email,
                'password' => $request->password ? Hash::make($request->password) : null,
            ]));

            // Update hotel fields
            if ($admin->hotel) {
                $admin->hotel->update(array_filter([
                    'hotel_name' => $request->hotel_name,
                    'domain'     => $request->domain,
                    'email'      => $request->email,
                    'phone'      => $request->phone,
                    'status'     => $request->status,
                ]));

                // Optional: Update structure if provided
                if ($request->has('navigation_items') && is_array($request->navigation_items)) {
                    $admin->hotel->navigationItems()->delete();
                    foreach ($request->navigation_items as $item) {
                        $admin->hotel->navigationItems()->create($item);
                    }
                }

                if ($request->has('page_sections') && is_array($request->page_sections)) {
                    $admin->hotel->pageSections()->delete();
                    foreach ($request->page_sections as $section) {
                        $admin->hotel->pageSections()->create($section);
                    }
                }
            }
        });

        return ApiResponse::success(new MerchantResource($admin->fresh()->load('hotel', 'roles')), 'Merchant updated successfully');
    }

    /**
     * Delete merchant admin + hotel
     */
    public function destroy($id)
    {
        $admin = User::role('admin')->findOrFail($id);
        $admin->delete();

        return ApiResponse::success(null, 'Merchant deleted successfully');
    }
}
