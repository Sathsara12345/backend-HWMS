<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Hotel;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
        return MerchantResource::collection($admins)->additional([
            'success' => true,
            'message' => 'Merchants retrieved successfully'
        ]);
    }

    /**
     * Get single merchant
     */
    public function show($id)
    {
        $admin = User::role('admin')->with(['hotel', 'roles'])->findOrFail($id);
        return (new MerchantResource($admin))->additional([
            'success' => true
        ]);
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

            // 3. Create hotel record linked to this user
            $merchant->hotel()->create([
                'hotel_name' => $request->hotel_name,
                'domain'     => $request->domain,
                'email'      => $request->email,
                'phone'      => $request->phone,
                'status'     => 'active',
            ]);
        });

        return (new MerchantResource($merchant->load('hotel', 'roles')))->additional([
            'success' => true,
            'message' => 'Merchant created successfully'
        ])->response()->setStatusCode(201);
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

        return (new MerchantResource($admin->fresh()->load('hotel', 'roles')))->additional([
            'success' => true,
            'message' => 'Merchant updated successfully'
        ]);
    }

    /**
     * Delete merchant admin + hotel
     */
    public function destroy($id)
    {
        $admin = User::role('admin')->findOrFail($id);
        $admin->delete();

        return response()->json([
            'success' => true,
            'message' => 'Merchant deleted successfully'
        ]);
    }
}
