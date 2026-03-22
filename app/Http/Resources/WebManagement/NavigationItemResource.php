<?php

namespace App\Http\Resources\WebManagement;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NavigationItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'hotel_id'    => $this->hotel_id,
            'label'       => $this->label,
            'url'         => $this->url,
            'order'       => $this->order,
            'is_active'   => $this->is_active,
            'sections_count' => $this->whenCounted('pageSections'),
            'sections'    => PageSectionResource::collection(
                                $this->whenLoaded('pageSections')
                             ),
        ];
    }
}
