<?php

namespace App\Http\Resources\WebManagement;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class SectionContentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $data = [
            'id'          => $this->id,
            'field_key'   => $this->field_key,
            'field_value' => $this->field_value,
            'type'        => $this->type,
        ];

        // Append public URL for media fields
        if (in_array($this->type, ['image', 'video']) && $this->field_value) {
            $url = Storage::url($this->field_value);
            // Ensure absolute URL
            $data['url'] = str_starts_with($url, 'http') ? $url : config('app.url') . $url;
        }

        return $data;
    }
}
