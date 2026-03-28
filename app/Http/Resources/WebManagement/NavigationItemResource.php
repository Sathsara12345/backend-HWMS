<?php

namespace App\Http\Resources\WebManagement;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class NavigationItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            // ── Core ──────────────────────────────────────────
            'id'         => $this->id,
            'hotel_id'   => $this->hotel_id,
            'label'      => $this->label,
            'slug'       => $this->slug,
            'url'        => $this->url,
            'order'      => $this->order,
            'is_active'  => $this->is_active,

            // ── SEO ───────────────────────────────────────────
            'seo' => [
                'meta_title'       => $this->meta_title,
                'meta_description' => $this->meta_description,
                'meta_keywords'    => $this->meta_keywords,
                'og_title'         => $this->og_title,
                'og_description'   => $this->og_description,
                'og_image'         => $this->og_image
                                        ? Storage::url($this->og_image)
                                        : null,
                'canonical_url'    => $this->canonical_url,
                'is_indexable'     => $this->is_indexable,
            ],

            // ── Relations ─────────────────────────────────────
            'sections_count' => $this->whenCounted('pageSections'),
            'sections'       => PageSectionResource::collection(
                                    $this->whenLoaded('pageSections')
                                ),

            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
