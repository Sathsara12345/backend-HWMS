<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Hotel;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class MerchantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create a default Admin (Merchant) user
        $admin = User::firstOrCreate([
            'email' => 'admin@hotel.com'
        ], [
            'name' => 'Hotel Admin',
            'password' => Hash::make('password'),
        ]);

        if (!$admin->hasRole('admin')) {
            $admin->assignRole('admin');
        }

        // Give common permission
        $admin->givePermissionTo('view-dashboard');

        // Create a hotel for this admin if not exists
        if (!$admin->hotel) {
            $hotel = $admin->hotel()->create([
                'hotel_name' => 'Aurum Hotel',
                'domain'     => 'aurumhotel.com',
                'email'      => 'reservations@aurumhotel.com',
                'phone'      => '+1 (800) 287-6678',
                'status'     => 'active',
            ]);
            
            // Seed its website structure
            $hotel->seedDefaults();
        }
    }
}
