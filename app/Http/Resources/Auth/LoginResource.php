<?php

namespace App\Http\Resources\Auth;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LoginResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $user = $this->resource['user'];
        return [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'roles' => $user->roles->pluck('name'),
            ],
            'permissions' => [
                'direct' => $user->getDirectPermissions()->pluck('name'),
                'roles' => $user->getPermissionsViaRoles()->pluck('name'),
                'all' => $user->getAllPermissions()->pluck('name'),
            ],
            'token' => $this->resource['token'],
        ];
    }
}
