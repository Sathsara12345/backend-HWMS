<?php

namespace App\Http\Requests\WebManagement;

use Illuminate\Foundation\Http\FormRequest;

class UpdateNavigationItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'label'     => ['sometimes', 'string', 'max:255'],
            'url'       => ['sometimes', 'string', 'max:255'],
            'order'     => ['sometimes', 'integer', 'min:0'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
