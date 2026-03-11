<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MerchantResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'hotel' => [
                'id' => $this->hotel?->id,
                'hotel_name' => $this->hotel?->hotel_name,
                'domain' => $this->hotel?->domain,
                'phone' => $this->hotel?->phone,
                'email' => $this->hotel?->email,
                'status' => $this->hotel?->status,
            ],
            'roles' => $this->roles->pluck('name'),
        ];
    }
}
