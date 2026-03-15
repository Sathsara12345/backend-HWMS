<?php

namespace App\Http\Resources\Public;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

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
                return [
                    'section_name' => $section->section_name,
                    'title'        => $section->title,
                    'content'      => $section->content,
                    'order'        => $section->order,
                    'settings'     => $section->settings,
                ];
            }),
        ];
    }
}
