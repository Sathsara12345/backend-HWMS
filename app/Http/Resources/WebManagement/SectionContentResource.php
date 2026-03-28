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
            $data['url'] = Storage::url($this->field_value);
        }

        return $data;
    }
}
