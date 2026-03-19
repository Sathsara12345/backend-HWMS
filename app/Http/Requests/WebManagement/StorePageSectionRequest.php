<?php

namespace App\Http\Requests\WebManagement;

use Illuminate\Foundation\Http\FormRequest;

class StorePageSectionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'navigation_item_id' => ['nullable', 'integer', 'exists:navigation_items,id'],
            'section_name'       => ['required', 'string', 'max:255'],
            'title'              => ['nullable', 'string', 'max:255'],
            'content'            => ['nullable', 'string'],
            'order'              => ['sometimes', 'integer', 'min:0'],
            'is_visible'         => ['sometimes', 'boolean'],
            'settings'           => ['nullable', 'array'],
        ];
    }
}
