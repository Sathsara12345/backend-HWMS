<?php

namespace App\Http\Controllers\WebManagement;
 
use App\Models\Hotel;
use App\Models\User;
use App\Models\PageSection;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\WebManagement\ReorderRequest;
use App\Http\Requests\WebManagement\PageSectionRequest;
use App\Http\Resources\WebManagement\PageSectionResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
 
class PageSectionController extends Controller
{
    // ─── GET /v1/admin/merchants/{merchant}/sections ──────────────────────────
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

    // ─── POST /v1/admin/merchants/{merchant}/sections ─────────────────────────
    public function store(PageSectionRequest $request, User $merchant): PageSectionResource
    {
        $hotel = $merchant->hotel;
        abort_if(!$hotel, 404, 'No hotel found for this merchant.');

        $this->authorize('create', [PageSection::class, $hotel->id]);

        if ($request->filled('navigation_item_id')) {
            $this->validateNavBelongsToHotel($request->input('navigation_item_id'), $hotel->id);
        }

        $order = $request->input(
            'order',
            PageSection::forHotel($hotel->id)->max('order') + 1
        );

        // Extract only valid asset columns from current request state if persisting directly or keep empty
        // Media handled below natively
        $urls = $this->handleMediaUploads($request, $hotel->id, $request->input('section_name'));

        $section = PageSection::create(array_merge(
            $request->safe()->except(['media']),
            $urls,
            [
                'hotel_id' => $hotel->id,
                'order'    => $order,
            ]
        ));

        return new PageSectionResource($section->load('navigationItem'));
    }

    // ─── GET /v1/admin/sections/{pageSection} ─────────────────────────────────
    public function show(PageSection $pageSection): PageSectionResource
    {
        $this->authorize('view', $pageSection);

        return new PageSectionResource($pageSection->load('navigationItem'));
    }

    // ─── PUT /v1/admin/sections/{pageSection} ─────────────────────────────────
    // Same multipart support as store — send text + files together
    public function update(PageSectionRequest $request, PageSection $pageSection): PageSectionResource
    {
        $this->authorize('update', $pageSection);

        if ($request->filled('navigation_item_id')) {
            $this->validateNavBelongsToHotel($request->input('navigation_item_id'), $pageSection->hotel_id);
        }

        // Handle new media uploads — replaces old file for same key directly on storage disk
        $urls = $this->handleMediaUploads(
            $request,
            $pageSection->hotel_id,
            $pageSection->section_name,
            $pageSection
        );

        $pageSection->update(array_merge(
            $request->safe()->except(['media']),
            $urls
        ));

        return new PageSectionResource($pageSection->fresh()->load('navigationItem'));
    }

    // ─── POST /v1/admin/sections/{pageSection}/media ──────────────────────────
    public function uploadMedia(Request $request, PageSection $pageSection): JsonResponse
    {
        $this->authorize('update', $pageSection);

        $key = $request->input('key');
        $is_video = $key === 'video';
        $max_size = $is_video ? 10240 : 5120;

        $request->validate([
            'file' => ['required', 'file', "max:{$max_size}"],
            'key'  => ['required', 'string', 'max:50'],
        ]);

        if (!in_array($key, ['image', 'video', 'banner', 'poster', 'background'])) {
            return response()->json(['message' => "Invalid media key '{$key}'."], 422);
        }

        $column = "{$key}_url";

        // Delete old file for this key if it exists
        if (!empty($pageSection->$column)) {
            Storage::disk('public')->delete($pageSection->$column);
        }

        $hotel = $pageSection->hotel;
        $timestamp = now()->format('Y-m-d_His');
        $hotelSlug = Str::slug($hotel->hotel_name ?? 'hotel');
        $extension = $request->file('file')->getClientOriginalExtension();
        $filename = "{$hotelSlug}_{$key}_{$timestamp}.{$extension}";

        $path = $request->file('file')->storeAs(
            "hotels/{$pageSection->hotel_id}/{$pageSection->section_name}",
            $filename,
            'public'
        );

        $pageSection->update([$column => $path]);

        return response()->json([
            'message' => 'Media uploaded successfully.',
            'key'     => $key,
            'url'     => Storage::url($path),
            'section' => new PageSectionResource($pageSection->fresh()),
        ]);
    }

    // ─── DELETE /v1/admin/sections/{pageSection}/media/{key} ──────────────────
    public function deleteMedia(PageSection $pageSection, string $key): JsonResponse
    {
        $this->authorize('update', $pageSection);

        if (!in_array($key, ['image', 'video', 'banner', 'poster', 'background'])) {
            return response()->json(['message' => "Invalid media key '{$key}'."], 422);
        }

        $column = "{$key}_url";

        if (empty($pageSection->$column)) {
            return response()->json(['message' => "No media found for key '{$key}'."], 404);
        }

        Storage::disk('public')->delete($pageSection->$column);
        $pageSection->update([$column => null]);

        return response()->json([
            'message' => "'{$key}' removed successfully.",
            'section' => new PageSectionResource($pageSection->fresh()),
        ]);
    }

    // ─── PATCH /v1/admin/sections/{pageSection}/visibility ───────────────────
    public function toggleVisibility(PageSection $pageSection): PageSectionResource
    {
        $this->authorize('update', $pageSection);

        $pageSection->update(['is_visible' => !$pageSection->is_visible]);

        return new PageSectionResource($pageSection);
    }

    // ─── PATCH /v1/admin/merchants/{merchant}/sections/reorder ───────────────
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

    // ─── DELETE /v1/admin/sections/{pageSection} ─────────────────────────────
    public function destroy(PageSection $pageSection): JsonResponse
    {
        $this->authorize('delete', $pageSection);

        // Clean up all media files for this section
        $this->deleteAllSectionMedia($pageSection);

        $pageSection->delete();

        return response()->json(['message' => 'Section deleted.'], 200);
    }

    // ─── Private Helpers ──────────────────────────────────────────────────────
    private function handleMediaUploads(Request $request, int $hotelId, string $sectionName, ?PageSection $existingSection = null): array
    {
        $urls = [];

        if (!$request->hasFile('media')) {
            return $urls;
        }

        // Fetch hotel for naming if possible
        $hotel = Hotel::find($hotelId);
        $hotelSlug = Str::slug($hotel->hotel_name ?? 'hotel');
        $timestamp = now()->format('Y-m-d_His');

        foreach ($request->file('media') as $key => $file) {
            if (!$file->isValid()) continue;
            if (!in_array($key, ['image', 'video', 'banner', 'poster', 'background'])) continue;

            $column = "{$key}_url";

            // Delete old file if replacing
            if ($existingSection && !empty($existingSection->$column)) {
                Storage::disk('public')->delete($existingSection->$column);
            }

            $extension = $file->getClientOriginalExtension();
            $filename = "{$hotelSlug}_{$key}_{$timestamp}.{$extension}";

            $path = $file->storeAs("hotels/{$hotelId}/{$sectionName}", $filename, 'public');
            $urls[$column] = $path;
        }

        return $urls;
    }

    /**
     * Delete all stored media files when a section is deleted.
     */
    private function deleteAllSectionMedia(PageSection $pageSection): void
    {
        $columns = ['video_url', 'image_url', 'banner_url', 'poster_url', 'background_url'];

        foreach ($columns as $column) {
            if (!empty($pageSection->$column)) {
                Storage::disk('public')->delete($pageSection->$column);
            }
        }
    }

    /**
     * Ensure the navigation item belongs to the same hotel.
     */
    private function validateNavBelongsToHotel(int $navId, int $hotelId): void
    {
        $exists = \App\Models\NavigationItem::where('id', $navId)
            ->where('hotel_id', $hotelId)
            ->exists();

        abort_unless($exists, 422, 'The navigation item does not belong to this hotel.');
    }
}