<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class NavItemStoreRequest extends FormRequest
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
            'label'     => ['required', 'string'],
            'href'      => ['nullable', 'string'],
            'icon'      => ['nullable', 'string'],
            'section'   => ['nullable', 'string'],
            'parent_id' => ['nullable', 'exists:nav_items,id'],
            'order'     => ['integer'],
            'is_active' => ['boolean'],
            'role_ids'  => ['required', 'array'],
            'role_ids.*'=> ['exists:roles,id'],
        ];
    }
}