<?php

// app/Http/Controllers/AdminDashboardController.php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Hotel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Http\Responses\ApiResponse;
use App\Http\Requests\Admin\CreateAdminRequest;
use App\Http\Requests\Admin\UpdateAdminRequest;

class SuperAdminRegController extends Controller
{
    // List all admins
    public function index()
    {
        $admins = User::role('admin')->with('hotel')->get();
        return ApiResponse::success($admins);
    }

    // Get single admin
    public function show($id)
    {
        $admin = User::role('admin')->with('hotel')->findOrFail($id);
        return ApiResponse::success($admin);
    }

    // Create admin + hotel
    public function store(CreateAdminRequest $request)
    {
        $admin = null;
        
        DB::transaction(function () use ($request, &$admin) {
            // 1. Create the user account
            $admin = User::create([
                'name'     => $request->name,
                'email'    => $request->email,
                'password' => Hash::make($request->password),
            ]);

            // 2. Assign admin role
            $admin->assignRole('admin');

            // 3. Create hotel record linked to this user
            $admin->hotel()->create([
                'hotel_name' => $request->hotel_name,
                'domain'     => $request->domain,
                'email'      => $request->email,
                'phone'      => $request->phone,
                'status'     => 'active',
            ]);
        });

        return ApiResponse::success(
            $admin->load('hotel', 'roles'),
            'Admin created successfully',
            201
        );
    }

    // Update admin + hotel
    public function update(UpdateAdminRequest $request, $id)
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
            $admin->hotel->update(array_filter([
                'hotel_name' => $request->hotel_name,
                'domain'     => $request->domain,
                'email'      => $request->email,
                'phone'      => $request->phone,
                'status'     => $request->status,
            ]));
        });

        return ApiResponse::success(
            $admin->fresh()->load('hotel', 'roles'),
            'Admin updated successfully'
        );
    }

    // Delete admin + hotel (cascade handles hotel row)
    public function destroy($id)
    {
        $admin = User::role('admin')->findOrFail($id);
        $admin->delete();

        return ApiResponse::success([], 'Admin deleted successfully');
    }
}