<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MerchantResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id'         => $this->id,
            'name'       => $this->name,
            'email'      => $this->email,
            'hotel'      => $this->whenLoaded('hotel', function () {
                return [
                    'id'         => $this->hotel->id,
                    'hotel_name' => $this->hotel->hotel_name,
                    'domain'     => $this->hotel->domain,
                    'email'      => $this->hotel->email,
                    'phone'      => $this->hotel->phone,
                    'status'     => $this->hotel->status,
                ];
            }),
            'roles'      => $this->whenLoaded('roles', function () {
                return $this->roles->pluck('name');
            }),
        ];
    }
}
