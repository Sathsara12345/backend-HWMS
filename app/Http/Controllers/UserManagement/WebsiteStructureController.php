<?php

namespace App\Http\Controllers\UserManagement;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Hotel;
use Illuminate\Http\Request;
use App\Http\Responses\ApiResponse;

class WebsiteStructureController extends Controller
{
    /**
     * Get navigation items for a merchant's hotel
     */
    public function getNavigation($merchantId)
    {
        $merchant = User::role('admin')->with('hotel')->findOrFail($merchantId);
        $hotel = $merchant->hotel;

        if (!$hotel) {
            return ApiResponse::error('Hotel not found for this merchant', 404);
        }

        $navItems = $hotel->navigationItems()->orderBy('order')->get();
        return ApiResponse::success($navItems, 'Navigation items retrieved successfully');
    }

    /**
     * Update navigation items
     */
    public function updateNavigation(Request $request, $merchantId)
    {
        $merchant = User::role('admin')->with('hotel')->findOrFail($merchantId);
        $hotel = $merchant->hotel;

        if (!$hotel) {
            return ApiResponse::error('Hotel not found for this merchant', 404);
        }

        $request->validate([
            'items' => 'required|array',
            'items.*.label' => 'required|string',
            'items.*.url' => 'nullable|string',
            'items.*.order' => 'required|integer',
            'items.*.is_active' => 'boolean',
        ]);

        $hotel->navigationItems()->delete();
        
        foreach ($request->items as $item) {
            $hotel->navigationItems()->create([
                'label' => $item['label'],
                'url' => $item['url'] ?? '#',
                'order' => $item['order'],
                'is_active' => $item['is_active'] ?? true,
            ]);
        }

        return ApiResponse::success($hotel->navigationItems()->orderBy('order')->get(), 'Navigation updated successfully');
    }

    /**
     * Get page sections for a merchant's hotel
     */
    public function getSections($merchantId)
    {
        $merchant = User::role('admin')->with('hotel')->findOrFail($merchantId);
        $hotel = $merchant->hotel;

        if (!$hotel) {
            return ApiResponse::error('Hotel not found for this merchant', 404);
        }

        $sections = $hotel->pageSections()->orderBy('order')->get();
        return ApiResponse::success($sections, 'Page sections retrieved successfully');
    }

    /**
     * Update page sections
     */
    public function updateSections(Request $request, $merchantId)
    {
        $merchant = User::role('admin')->with('hotel')->findOrFail($merchantId);
        $hotel = $merchant->hotel;

        if (!$hotel) {
            return ApiResponse::error('Hotel not found for this merchant', 404);
        }

        $request->validate([
            'sections' => 'required|array',
            'sections.*.section_name' => 'required|string',
            'sections.*.title' => 'nullable|string',
            'sections.*.content' => 'nullable|string',
            'sections.*.order' => 'required|integer',
            'sections.*.is_visible' => 'boolean',
        ]);

        foreach ($request->sections as $sectionData) {
            $hotel->pageSections()->updateOrCreate(
                ['section_name' => $sectionData['section_name']],
                [
                    'title' => $sectionData['title'] ?? null,
                    'content' => $sectionData['content'] ?? null,
                    'order' => $sectionData['order'],
                    'is_visible' => $sectionData['is_visible'] ?? true,
                ]
            );
        }

        return ApiResponse::success($hotel->pageSections()->orderBy('order')->get(), 'Sections updated successfully');
    }
}
