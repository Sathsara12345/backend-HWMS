<?php

namespace App\Http\Requests\WebManagement;

use Illuminate\Foundation\Http\FormRequest;

class StoreNavigationItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        // hotel_id comes from the route; policy check happens in controller
        return true;
    }

    public function rules(): array
    {
        return [
            'label'     => ['required', 'string', 'max:255'],
            'url'       => ['required', 'string', 'max:255'],
            'order'     => ['sometimes', 'integer', 'min:0'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
