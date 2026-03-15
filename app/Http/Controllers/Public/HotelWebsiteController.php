<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Hotel;
use App\Http\Responses\ApiResponse;
use App\Http\Resources\Public\HotelWebsiteResource;
use Illuminate\Http\Request;

class HotelWebsiteController extends Controller
{
    /**
     * Get hotel website data by domain or hotel name
     */
    public function show(Request $request)
    {
        $domain = $request->query('domain');
        
        // Find hotel by domain or fallback to first one for testing if domain not provided
        $hotel = Hotel::where('domain', $domain)->first();
        
        if (!$hotel && !$domain) {
            $hotel = Hotel::first();
        }

        if (!$hotel) {
            return ApiResponse::error('Hotel not found', 404);
        }

        return ApiResponse::success(new HotelWebsiteResource($hotel), 'Hotel website data retrieved successfully');
    }
}
