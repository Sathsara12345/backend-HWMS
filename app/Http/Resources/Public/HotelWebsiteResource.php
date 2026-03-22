<?php

namespace App\Http\Resources\Public;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class HotelWebsiteResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'hotel_name'       => $this->hotel_name,
            'email'            => $this->email,
            'phone'            => $this->phone,
            'domain'           => $this->domain,
            'navigation_items' => $this->navigationItems()->where('is_active', true)->orderBy('order')->get()->map(function($item) {
                return [
                    'label' => $item->label,
                    'url'   => $item->url,
                    'order' => $item->order,
                ];
            }),
            'page_sections'    => $this->pageSections()->where('is_visible', true)->orderBy('order')->get()->map(function($section) {
                $mediaKeys = ['image', 'video', 'banner', 'poster', 'background'];
                $mediaUrls = [];
                
                foreach ($mediaKeys as $key) {
                    $column = "{$key}_url";
                    if (!empty($section->$column)) {
                        $mediaUrls["{$key}_url"] = Storage::url($section->$column);
                    }
                }

                return [
                    'section_name'   => $section->section_name,
                    'title'          => $section->title,
                    'sub_title'      => $section->sub_title,
                    'content'        => $section->content,
                    'order'          => $section->order,
                    'image_url'      => $section->image_url,
                    'video_url'      => $section->video_url,
                    'banner_url'     => $section->banner_url,
                    'poster_url'     => $section->poster_url,
                    'background_url' => $section->background_url,
                    'media_urls'     => $mediaUrls,
                ];
            }),
        ];
    }
}
