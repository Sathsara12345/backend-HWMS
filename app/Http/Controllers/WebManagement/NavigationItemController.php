<?php

namespace App\Http\Controllers\WebManagement;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Http\Requests\WebManagement\ReorderRequest;
use App\Http\Requests\WebManagement\StoreNavigationItemRequest;
use App\Http\Requests\WebManagement\UpdateNavigationItemRequest;
use App\Http\Resources\WebManagement\NavigationItemResource;
use App\Models\Hotel;
use App\Models\User;
use App\Models\NavigationItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;

class NavigationItemController extends Controller
{
    use AuthorizesRequests;
    // ─── GET /api/hotels/{hotel}/navigation ──────────────────
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

    // ─── POST /api/hotels/{hotel}/navigation ─────────────────
public function store(StoreNavigationItemRequest $request, User $merchant): NavigationItemResource
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


    // ─── GET /api/navigation/{navigationItem} ────────────────
    public function show(NavigationItem $navigationItem): NavigationItemResource
    {
        $this->authorize('view', $navigationItem);

        $navigationItem->load('pageSections');

        return new NavigationItemResource($navigationItem);
    }

    // ─── PUT /api/navigation/{navigationItem} ────────────────
    public function update(UpdateNavigationItemRequest $request, NavigationItem $navigationItem): NavigationItemResource
    {
        $this->authorize('update', $navigationItem);

        $navigationItem->update($request->validated());

        return new NavigationItemResource($navigationItem);
    }

    // ─── PATCH /api/navigation/{navigationItem}/toggle ───────
    public function toggle(NavigationItem $navigationItem): NavigationItemResource
    {
        $this->authorize('update', $navigationItem);

        $navigationItem->update(['is_active' => ! $navigationItem->is_active]);

        return new NavigationItemResource($navigationItem);
    }

    // ─── PATCH /api/hotels/{hotel}/navigation/reorder ────────
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

    // ─── DELETE /api/navigation/{navigationItem} ─────────────
    public function destroy(NavigationItem $navigationItem): JsonResponse
    {
        $this->authorize('delete', $navigationItem);

        // Sections with navigation_item_id will be set to NULL (nullOnDelete in migration)
        $navigationItem->delete();

        return response()->json(['message' => 'Navigation item deleted.'], 200);
    }
}
