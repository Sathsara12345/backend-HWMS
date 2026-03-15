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

            // 5. Seed common nav items
            $this->seedDefaultNavigation($hotel);

            // 6. Seed common main page sections
            $this->seedDefaultSections($hotel);
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

    /**
     * Seed default navigation items for a new hotel
     */
    private function seedDefaultNavigation($hotel)
    {
        $defaults = [
            ['label' => 'Home', 'url' => '/', 'order' => 1],
            ['label' => 'Rooms', 'url' => '/rooms', 'order' => 2],
            ['label' => 'Services', 'url' => '/services', 'order' => 3],
            ['label' => 'About Us', 'url' => '/about', 'order' => 4],
            ['label' => 'Contact', 'url' => '/contact', 'order' => 5],
        ];

        foreach ($defaults as $item) {
            $hotel->navigationItems()->create($item);
        }
    }

    /**
     * Seed default page sections for a new hotel
     */
    private function seedDefaultSections($hotel)
    {
        $defaults = [
            [
                'section_name' => 'Hero',
                'title' => 'Welcome to ' . $hotel->hotel_name,
                'content' => 'Luxury and comfort in the heart of the city.',
                'order' => 1,
            ],
            [
                'section_name' => 'About',
                'title' => 'Our Story',
                'content' => 'We have been providing world-class hospitality since...',
                'order' => 2,
            ],
            [
                'section_name' => 'Services',
                'title' => 'Our Premium Services',
                'content' => 'From spa treatments to gourmet dining, we offer it all.',
                'order' => 3,
            ],
        ];

        foreach ($defaults as $section) {
            $hotel->pageSections()->create($section);
        }
    }
}
