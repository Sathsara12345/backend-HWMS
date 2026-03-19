<?php

namespace App\Http\Requests\WebManagement;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePageSectionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'navigation_item_id' => ['sometimes', 'nullable', 'integer', 'exists:navigation_items,id'],
            'section_name'       => ['sometimes', 'string', 'max:255'],
            'title'              => ['sometimes', 'nullable', 'string', 'max:255'],
            'content'            => ['sometimes', 'nullable', 'string'],
            'order'              => ['sometimes', 'integer', 'min:0'],
            'is_visible'         => ['sometimes', 'boolean'],
            'settings'           => ['sometimes', 'nullable', 'array'],
        ];
    }
}
