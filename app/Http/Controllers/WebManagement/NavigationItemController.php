<?php

namespace App\Http\Controllers\WebManagement;

use App\Models\User;
use App\Models\NavigationItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use App\Http\Requests\WebManagement\ReorderRequest;
use App\Http\Requests\WebManagement\NavigationItemRequest;
use App\Http\Requests\WebManagement\UploadNavOgImageRequest;
use App\Http\Resources\WebManagement\NavigationItemResource;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class NavigationItemController extends Controller
{
    use AuthorizesRequests;

    // ── GET /v1/admin/merchants/{merchant}/navigation ─────────
    public function index(User $merchant): AnonymousResourceCollection
    {
        $hotel = $merchant->hotel;
        abort_if(!$hotel, 404, 'No hotel found for this merchant.');

        $this->authorize('viewAny', [NavigationItem::class, (int) $hotel->id]);

        $items = NavigationItem::forHotel($hotel->id)
            ->withCount('pageSections')
            ->ordered()
            ->get();

        return NavigationItemResource::collection($items);
    }

    // ── POST /v1/admin/merchants/{merchant}/navigation ────────
    public function store(NavigationItemRequest $request, User $merchant): NavigationItemResource
    {
        $hotel = $merchant->hotel;
        abort_if(!$hotel, 404, 'No hotel found for this merchant.');

        $this->authorize('create', [NavigationItem::class, (int) $hotel->id]);

        $order = $request->input(
            'order',
            NavigationItem::forHotel($hotel->id)->max('order') + 1
        );

        $item = NavigationItem::create([
            ...$request->validated(),
            'hotel_id' => $hotel->id,
            'order'    => $order,
        ]);

        return new NavigationItemResource($item);
    }

    // ── GET /v1/admin/navigation/{navigationItem} ─────────────
    public function show(NavigationItem $navigationItem): NavigationItemResource
    {
        $this->authorize('view', $navigationItem);

        $navigationItem->load('pageSections');

        return new NavigationItemResource($navigationItem);
    }

    // ── PUT /v1/admin/navigation/{navigationItem} ─────────────
    public function update(NavigationItemRequest $request, NavigationItem $navigationItem): NavigationItemResource
    {
        $this->authorize('update', $navigationItem);

        $navigationItem->update($request->validated());

        return new NavigationItemResource($navigationItem);
    }

    // ── PATCH /v1/admin/navigation/{navigationItem}/toggle ────
    public function toggle(NavigationItem $navigationItem): NavigationItemResource
    {
        $this->authorize('update', $navigationItem);

        $navigationItem->update(['is_active' => !$navigationItem->is_active]);

        return new NavigationItemResource($navigationItem);
    }

    // ── POST /v1/admin/navigation/{navigationItem}/og-image ───
    // Upload the OG image for social sharing previews
    // Body: multipart/form-data → og_image (file, 1200×627px recommended)
    public function uploadOgImage(UploadNavOgImageRequest $request, NavigationItem $navigationItem): NavigationItemResource
    {
        $this->authorize('update', $navigationItem);

        // Delete old OG image from disk if it exists
        if ($navigationItem->og_image) {
            Storage::disk('public')->delete($navigationItem->og_image);
        }

        $path = $request->file('og_image')->store(
            "hotels/{$navigationItem->hotel_id}/og",
            'public'
        );

        $navigationItem->update(['og_image' => $path]);

        return new NavigationItemResource($navigationItem);
    }

    // ── DELETE /v1/admin/navigation/{navigationItem}/og-image ─
    public function deleteOgImage(NavigationItem $navigationItem): JsonResponse
    {
        $this->authorize('update', $navigationItem);

        if (!$navigationItem->og_image) {
            return response()->json(['message' => 'No OG image to delete.'], 404);
        }

        Storage::disk('public')->delete($navigationItem->og_image);
        $navigationItem->update(['og_image' => null]);

        return response()->json(['message' => 'OG image removed successfully.']);
    }

    // ── PATCH /v1/admin/merchants/{merchant}/navigation/reorder
    public function reorder(ReorderRequest $request, User $merchant): JsonResponse
    {
        $hotel = $merchant->hotel;
        abort_if(!$hotel, 404, 'No hotel found for this merchant.');

        $this->authorize('viewAny', [NavigationItem::class, (int) $hotel->id]);

        $items = collect($request->input('items'));
        $ids   = $items->pluck('id');

        $valid = NavigationItem::forHotel($hotel->id)->whereIn('id', $ids)->pluck('id');

        if ($valid->count() !== $ids->count()) {
            return response()->json(['message' => 'One or more items do not belong to this hotel.'], 422);
        }

        DB::transaction(function () use ($items) {
            foreach ($items as $item) {
                NavigationItem::where('id', $item['id'])
                    ->update(['order' => $item['order']]);
            }
        });

        return response()->json(['message' => 'Navigation items reordered.']);
    }

    // ── DELETE /v1/admin/navigation/{navigationItem} ──────────
    public function destroy(NavigationItem $navigationItem): JsonResponse
    {
        $this->authorize('delete', $navigationItem);

        // Clean up OG image from disk
        if ($navigationItem->og_image) {
            Storage::disk('public')->delete($navigationItem->og_image);
        }

        // Sections will have navigation_item_id set to NULL (nullOnDelete in migration)
        $navigationItem->delete();

        return response()->json(['message' => 'Navigation item deleted.']);
    }
}
