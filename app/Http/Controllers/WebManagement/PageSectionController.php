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
use Illuminate\Support\Facades\Storage;

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
    // Accepts text fields + optional media files in one request (multipart/form-data)
    //
    // Form fields:
    //   navigation_item_id  (optional)
    //   section_name        (required)
    //   title               (optional)
    //   content             (optional)
    //   order               (optional)
    //   is_visible          (optional, default true)
    //   settings            (optional, JSON string)
    //
    // File fields (all optional):
    //   media[video]        → stored as settings.video
    //   media[image]        → stored as settings.image
    //   media[banner]       → stored as settings.banner
    //   media[*]            → any key you need
    public function store(StorePageSectionRequest $request, User $merchant): PageSectionResource
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

        // Parse settings — frontend may send as JSON string or array
        $settings = $this->parseSettings($request->input('settings'));

        // Upload any media files sent along with the creation request
        $settings = $this->handleMediaUploads($request, $settings, $hotel->id, $request->input('section_name'));

        $section = PageSection::create([
            ...$request->validated(),
            'hotel_id' => $hotel->id,
            'order'    => $order,
            'settings' => $settings,
        ]);

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
    public function update(UpdatePageSectionRequest $request, PageSection $pageSection): PageSectionResource
    {
        $this->authorize('update', $pageSection);

        if ($request->filled('navigation_item_id')) {
            $this->validateNavBelongsToHotel($request->input('navigation_item_id'), $pageSection->hotel_id);
        }

        // Start from existing settings so we don't wipe keys not being updated
        $settings = $pageSection->settings ?? [];

        // Merge any new settings fields from the request
        if ($request->filled('settings')) {
            $incoming = $this->parseSettings($request->input('settings'));
            $settings = array_merge($settings, $incoming);
        }

        // Handle new media uploads — replaces old file for same key
        $settings = $this->handleMediaUploads(
            $request,
            $settings,
            $pageSection->hotel_id,
            $pageSection->section_name
        );

        $pageSection->update([
            ...$request->validated(),
            'settings' => $settings,
        ]);

        return new PageSectionResource($pageSection->fresh()->load('navigationItem'));
    }

    // ─── POST /v1/admin/sections/{pageSection}/media ──────────────────────────
    // Upload or replace a single media file on an existing section
    //
    // Body: multipart/form-data
    //   file  (required) — the file to upload
    //   key   (required) — e.g. "video", "image", "banner"
    public function uploadMedia(Request $request, PageSection $pageSection): JsonResponse
    {
        $this->authorize('update', $pageSection);

        $request->validate([
            'file' => ['required', 'file', 'max:102400'], // 100MB
            'key'  => ['required', 'string', 'max:50'],
        ]);

        $key      = $request->input('key');
        $settings = $pageSection->settings ?? [];

        // Delete old file for this key if it exists
        if (!empty($settings[$key])) {
            Storage::disk('public')->delete($settings[$key]);
        }

        $path = $request->file('file')->store(
            "hotels/{$pageSection->hotel_id}/{$pageSection->section_name}",
            'public'
        );

        $settings[$key] = $path;
        $pageSection->update(['settings' => $settings]);

        return response()->json([
            'message' => 'Media uploaded successfully.',
            'key'     => $key,
            'url'     => Storage::url($path),
            'section' => new PageSectionResource($pageSection->fresh()),
        ]);
    }

    // ─── DELETE /v1/admin/sections/{pageSection}/media/{key} ──────────────────
    // Remove a specific media file from a section
    public function deleteMedia(PageSection $pageSection, string $key): JsonResponse
    {
        $this->authorize('update', $pageSection);

        $settings = $pageSection->settings ?? [];

        if (empty($settings[$key])) {
            return response()->json(['message' => "No media found for key '{$key}'."], 404);
        }

        Storage::disk('public')->delete($settings[$key]);

        unset($settings[$key]);
        $pageSection->update(['settings' => $settings]);

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

    /**
     * Handle media[] file uploads from a request.
     * Expects files under media[video], media[image], media[banner], etc.
     * Replaces existing file for the same key.
     */
    private function handleMediaUploads(Request $request, array $settings, int $hotelId, string $sectionName): array
    {
        if (!$request->hasFile('media')) {
            return $settings;
        }

        foreach ($request->file('media') as $key => $file) {
            if (!$file->isValid()) continue;

            // Delete old file if replacing
            if (!empty($settings[$key])) {
                Storage::disk('public')->delete($settings[$key]);
            }

            $path = $file->store("hotels/{$hotelId}/{$sectionName}", 'public');
            $settings[$key] = $path;
        }

        return $settings;
    }

    /**
     * Parse settings — accepts JSON string or array.
     */
    private function parseSettings(mixed $settings): array
    {
        if (is_array($settings)) return $settings;
        if (is_string($settings)) return json_decode($settings, true) ?? [];
        return [];
    }

    /**
     * Delete all stored media files when a section is deleted.
     */
    private function deleteAllSectionMedia(PageSection $pageSection): void
    {
        $settings = $pageSection->settings ?? [];
        $mediaKeys = ['video', 'image', 'banner', 'poster', 'background'];

        foreach ($mediaKeys as $key) {
            if (!empty($settings[$key])) {
                Storage::disk('public')->delete($settings[$key]);
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