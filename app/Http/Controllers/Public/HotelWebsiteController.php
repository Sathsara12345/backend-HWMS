<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Hotel;
use App\Models\NavigationItem;
use App\Models\PageSection;
use App\Models\Room;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class HotelWebsiteController extends Controller
{
    // ── GET /v1/public/hotel-website?domain=grandriver&page=home
    public function show(Request $request): JsonResponse
    {
        $hotel = Hotel::where('domain', $request->query('domain'))
                      ->where('status', 'active')
                      ->firstOrFail();

        $navItem = NavigationItem::where('hotel_id', $hotel->id)
                                 ->where('slug', $request->query('page', 'home'))
                                 ->where('is_active', true)
                                 ->firstOrFail();

        $sections = PageSection::forHotel($hotel->id)
            ->forNavItem($navItem->id)
            ->visible()
            ->ordered()
            ->with(['contents', 'template'])
            ->get()
            ->map(fn($section) => $this->resolveSection($section, $hotel->id));

        return response()->json([
            'hotel'    => [
                'id'     => $hotel->id,
                'name'   => $hotel->hotel_name,
                'domain' => $hotel->domain,
            ],
            'page'     => $request->query('page', 'home'),
            'seo'      => $this->buildSeo($navItem, $hotel),
            'nav_item' => [
                'id'    => $navItem->id,
                'label' => $navItem->label,
                'slug'  => $navItem->slug,
            ],
            'sections' => $sections,
        ]);
    }

    // ── GET /v1/public/hotel-website/nav?domain=grandriver ────
    public function navigation(Request $request): JsonResponse
    {
        $hotel = Hotel::where('domain', $request->query('domain'))
                      ->where('status', 'active')
                      ->firstOrFail();

        $nav = NavigationItem::where('hotel_id', $hotel->id)
                             ->where('is_active', true)
                             ->orderBy('order')
                             ->get(['id', 'label', 'slug', 'url', 'order']);

        return response()->json([
            'hotel'      => $hotel->hotel_name,
            'navigation' => $nav,
        ]);
    }

    // ── GET /v1/public/sitemap?domain=grandriver ──────────────
    // Returns all indexable pages for Google sitemap generation
    public function sitemap(Request $request): JsonResponse
    {
        $hotel = Hotel::where('domain', $request->query('domain'))
                      ->where('status', 'active')
                      ->firstOrFail();

        $pages = NavigationItem::where('hotel_id', $hotel->id)
                               ->where('is_active', true)
                               ->where('is_indexable', true)
                               ->orderBy('order')
                               ->get(['id', 'label', 'slug', 'url', 'updated_at']);

        return response()->json([
            'domain' => $hotel->domain,
            'pages'  => $pages->map(fn($item) => [
                'slug'       => $item->slug,
                'url'        => $item->url,
                'label'      => $item->label,
                'updated_at' => $item->updated_at?->toISOString(),
            ]),
        ]);
    }

    // ─── Private helpers ──────────────────────────────────────

    private function buildSeo(NavigationItem $navItem, Hotel $hotel): array
    {
        // Fallback chain:
        // og_title → meta_title → hotel name
        // og_description → meta_description → null
        // og_image → null

        return [
            // Standard meta tags
            'meta_title'       => $navItem->meta_title       ?? $hotel->hotel_name,
            'meta_description' => $navItem->meta_description ?? null,
            'meta_keywords'    => $navItem->meta_keywords     ?? null,
            'is_indexable'     => $navItem->is_indexable,
            'canonical_url'    => $navItem->canonical_url     ?? null,

            // Open Graph — for Facebook, WhatsApp, LinkedIn, Slack previews
            'og_title'         => $navItem->og_title          ?? $navItem->meta_title        ?? $hotel->hotel_name,
            'og_description'   => $navItem->og_description    ?? $navItem->meta_description  ?? null,
            'og_image'         => $navItem->og_image
                                    ? (str_starts_with(Storage::url($navItem->og_image), 'http') 
                                        ? Storage::url($navItem->og_image) 
                                        : config('app.url') . Storage::url($navItem->og_image))
                                    : null,
        ];
    }

    private function resolveSection(PageSection $section, int $hotelId): array
    {
        return [
            'id'          => $section->id,
            'type'        => $section->section_key ?? $section->section_name,
            'order'       => $section->order,
            'template'    => $section->template?->name,
            'data_source' => $section->data_source,
            'settings'    => $section->settings,
            'data'        => $section->data_source === 'static'
                                ? $this->buildStaticData($section)
                                : $this->buildDynamicData($section, $hotelId),
        ];
    }

    private function buildStaticData(PageSection $section): array
    {
        return $section->contents->mapWithKeys(function ($content) {
            $value = match ($content->type) {
                'json'           => json_decode($content->field_value, true),
                'number'         => is_numeric($content->field_value) ? (float) $content->field_value : null,
                'image', 'video' => $content->field_value ? (str_starts_with(Storage::url($content->field_value), 'http') ? Storage::url($content->field_value) : config('app.url') . Storage::url($content->field_value)) : null,
                default          => $content->field_value,
            };
            return [$content->field_key => $value];
        })->toArray();
    }

    private function buildDynamicData(PageSection $section, int $hotelId): mixed
    {
        return match ($section->data_source) {
            'rooms' => $this->getRooms($hotelId, $section->settings['limit'] ?? null),
            default => null,
        };
    }

    private function getRooms(int $hotelId, ?int $limit): array
    {
        return Room::forHotel($hotelId)
            ->available()
            ->orderBy('price')
            ->when($limit, fn($q) => $q->limit($limit))
            ->get()
            ->map(fn($room) => [
                'id'           => $room->id,
                'title'        => $room->title,
                'description'  => $room->description,
                'price'        => $room->price,
                'images'       => collect($room->images ?? [])
                                    ->map(fn($img) => str_starts_with(Storage::url($img), 'http') ? Storage::url($img) : config('app.url') . Storage::url($img))
                                    ->toArray(),
                'availability' => $room->availability,
            ])
            ->toArray();
    }
}
