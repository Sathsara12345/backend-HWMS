<?php

namespace App\Http\Resources\WebManagement;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class PageSectionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                 => $this->id,
            'hotel_id'           => $this->hotel_id,
            'navigation_item_id' => $this->navigation_item_id,
            'section_name'       => $this->section_name,
            'data_source'        => $this->data_source,
            'settings'           => $this->settings,
            'order'              => $this->order,
            'is_visible'         => $this->is_visible,
            'image_url'          => $this->image_url,
            'video_url'          => $this->video_url,
            'banner_url'         => $this->banner_url,
            'poster_url'         => $this->poster_url,
            'background_url'     => $this->background_url,
            'media_urls'         => $this->resolveMediaUrls(),
            'navigation_item'    => new NavigationItemResource(
                                        $this->whenLoaded('navigationItem')
                                    ),
        ];
    }

    /**
     * Convert stored file columns into full public URLs.
     */
    private function resolveMediaUrls(): array
    {
        $mediaKeys = ['image', 'video', 'banner', 'poster', 'background'];
        $urls = [];

        foreach ($mediaKeys as $key) {
            $column = "{$key}_url";
            if (!empty($this->$column)) {
                $urls["{$key}_url"] = Storage::url($this->$column);
            }
        }

        return $urls;
    }
}