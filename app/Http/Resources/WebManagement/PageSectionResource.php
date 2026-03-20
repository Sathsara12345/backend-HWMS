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
            'title'              => $this->title,
            'content'            => $this->content,
            'order'              => $this->order,
            'is_visible'         => $this->is_visible,
            'settings'           => $this->settings,

            // Media URLs — resolved from settings paths to full public URLs
            // Frontend can use these directly without building URLs manually
            'media'              => $this->resolveMediaUrls($this->settings ?? []),

            'navigation_item'    => new NavigationItemResource(
                                        $this->whenLoaded('navigationItem')
                                    ),
            'created_at'         => $this->created_at?->toISOString(),
            'updated_at'         => $this->updated_at?->toISOString(),
        ];
    }

    /**
     * Convert stored file paths in settings into full public URLs.
     * 
     * settings.video  = "hotels/1/hero/abc.mp4"
     * media.video_url = "http://yourdomain.com/storage/hotels/1/hero/abc.mp4"
     */
    private function resolveMediaUrls(array $settings): array
    {
        $mediaKeys = ['video', 'image', 'banner', 'poster', 'background'];
        $urls = [];

        foreach ($mediaKeys as $key) {
            if (!empty($settings[$key])) {
                $urls["{$key}_url"] = Storage::url($settings[$key]);
            }
        }

        return $urls;
    }
}