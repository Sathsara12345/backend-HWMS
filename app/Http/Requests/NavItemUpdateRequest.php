<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class NavItemUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'label'     => ['sometimes', 'string'],
            'href'      => ['nullable', 'string'],
            'icon'      => ['nullable', 'string'],
            'section'   => ['nullable', 'string'],
            'parent_id' => ['nullable', 'exists:nav_items,id'],
            'order'     => ['sometimes', 'integer'],
            'is_active' => ['sometimes', 'boolean'],
            'role_ids'  => ['sometimes', 'array'],
            'role_ids.*'=> ['exists:roles,id'],
        ];
    }
}