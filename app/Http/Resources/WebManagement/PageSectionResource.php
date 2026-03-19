<?php

namespace App\Http\Resources\WebManagement;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

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
            'navigation_item'    => new NavigationItemResource(
                                        $this->whenLoaded('navigationItem')
                                    ),
            'created_at'         => $this->created_at?->toISOString(),
            'updated_at'         => $this->updated_at?->toISOString(),
        ];
    }
}
