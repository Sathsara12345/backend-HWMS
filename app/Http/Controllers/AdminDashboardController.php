<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Responses\ApiResponse;

class AdminDashboardController extends Controller
{
    /**
     * Get dashboard statistics
     */
    public function index()
    {
        // Dummy data for dashboard
        $stats = [
            'total_users' => 120,
            'total_bookings' => 450,
            'revenue' => '$12,500',
            'occupancy_rate' => '85%',
        ];

        return ApiResponse::success($stats, 'Admin dashboard statistics retrieved successfully.');
    }
}
