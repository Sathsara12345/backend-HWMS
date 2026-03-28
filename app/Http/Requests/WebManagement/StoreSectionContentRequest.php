<?php

namespace App\Http\Requests\WebManagement;

use Illuminate\Foundation\Http\FormRequest;

class StoreSectionContentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'fields'   => ['required', 'array'],
            'fields.*' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'fields.required' => 'At least one field is required.',
            'fields.array'    => 'Fields must be a key-value object.',
        ];
    }
}
