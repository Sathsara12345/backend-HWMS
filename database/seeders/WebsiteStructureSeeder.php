<?php

namespace Database\Seeders;

use App\Models\Hotel;
use Illuminate\Database\Seeder;

class WebsiteStructureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure all existing hotels have the default structure
        $hotels = Hotel::all();

        foreach ($hotels as $hotel) {
            $hotel->seedDefaults();
        }
    }
}
