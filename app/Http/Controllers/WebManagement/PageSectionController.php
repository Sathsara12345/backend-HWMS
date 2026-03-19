<?php

namespace App\Http\Controllers\WebManagement;

use App\Http\Controllers\Controller;
use App\Http\Requests\WebManagement\ReorderRequest;
use App\Http\Requests\WebManagement\StorePageSectionRequest;
use App\Http\Requests\WebManagement\UpdatePageSectionRequest;
use App\Http\Resources\WebManagement\PageSectionResource;
use App\Models\Hotel;
use App\Models\User;
use App\Models\PageSection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;

class PageSectionController extends Controller
{
    // ─── GET /api/hotels/{hotel}/sections ────────────────────
    // Optional query params:
    //   ?nav_id=5           → sections for a specific nav item
    //   ?visible_only=true  → only is_visible = true
    //   ?with_nav=true      → eager-load navigationItem
    public function index(Request $request, User $merchant): AnonymousResourceCollection
    {
        $hotel = $merchant->hotel;
        abort_if(!$hotel, 404, 'No hotel found for this merchant.');
        
        $this->authorize('viewAny', [PageSection::class, $hotel->id]);

        $query = PageSection::forHotel($hotel->id)->ordered();

        if ($request->filled('nav_id')) {
            $query->forNavItem((int) $request->input('nav_id'));
        }

        if ($request->boolean('visible_only')) {
            $query->visible();
        }

        if ($request->boolean('with_nav')) {
            $query->with('navigationItem');
        }

        return PageSectionResource::collection($query->get());
    }

    // ─── POST /api/hotels/{hotel}/sections ───────────────────
    public function store(StorePageSectionRequest $request, User $merchant): PageSectionResource
    {
        $hotel = $merchant->hotel;
        abort_if(!$hotel, 404, 'No hotel found for this merchant.');

        $this->authorize('create', [PageSection::class, $hotel->id]);

        // Ensure the nav item (if given) belongs to the same hotel
        if ($request->filled('navigation_item_id')) {
            $this->validateNavBelongsToHotel($request->input('navigation_item_id'), $hotel->id);
        }

        $order = $request->input(
            'order',
            PageSection::forHotel($hotel->id)->max('order') + 1
        );

        $section = PageSection::create([
            ...$request->validated(),
            'hotel_id' => $hotel->id,
            'order'    => $order,
        ]);

        return new PageSectionResource($section->load('navigationItem'));
    }

    // ─── GET /api/sections/{pageSection} ─────────────────────
    public function show(PageSection $pageSection): PageSectionResource
    {
        $this->authorize('view', $pageSection);

        return new PageSectionResource($pageSection->load('navigationItem'));
    }

    // ─── PUT /api/sections/{pageSection} ─────────────────────
    public function update(UpdatePageSectionRequest $request, PageSection $pageSection): PageSectionResource
    {
        $this->authorize('update', $pageSection);

        if ($request->filled('navigation_item_id')) {
            $this->validateNavBelongsToHotel($request->input('navigation_item_id'), $pageSection->hotel_id);
        }

        $pageSection->update($request->validated());

        return new PageSectionResource($pageSection->load('navigationItem'));
    }

    // ─── PATCH /api/sections/{pageSection}/visibility ────────
    public function toggleVisibility(PageSection $pageSection): PageSectionResource
    {
        $this->authorize('update', $pageSection);

        $pageSection->update(['is_visible' => ! $pageSection->is_visible]);

        return new PageSectionResource($pageSection);
    }

    // ─── PATCH /api/hotels/{hotel}/sections/reorder ──────────
    public function reorder(ReorderRequest $request, User $merchant): JsonResponse
    {
        $hotel = $merchant->hotel;
        abort_if(!$hotel, 404, 'No hotel found for this merchant.');

        $this->authorize('viewAny', [PageSection::class, $hotel->id]);

        $items = collect($request->input('items'));
        $ids   = $items->pluck('id');

        $valid = PageSection::forHotel($hotel->id)->whereIn('id', $ids)->pluck('id');

        if ($valid->count() !== $ids->count()) {
            return response()->json(['message' => 'One or more sections do not belong to this hotel.'], 422);
        }

        DB::transaction(function () use ($items) {
            foreach ($items as $item) {
                PageSection::where('id', $item['id'])
                    ->update(['order' => $item['order']]);
            }
        });

        return response()->json(['message' => 'Sections reordered.']);
    }

    // ─── DELETE /api/sections/{pageSection} ──────────────────
    public function destroy(PageSection $pageSection): JsonResponse
    {
        $this->authorize('delete', $pageSection);

        $pageSection->delete();

        return response()->json(['message' => 'Section deleted.'], 200);
    }

    // ─── Helpers ─────────────────────────────────────────────

    private function validateNavBelongsToHotel(int $navId, int $hotelId): void
    {
        $exists = \App\Models\NavigationItem::where('id', $navId)
            ->where('hotel_id', $hotelId)
            ->exists();

        abort_unless($exists, 422, 'The navigation item does not belong to this hotel.');
    }
}
