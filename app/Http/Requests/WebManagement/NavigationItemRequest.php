<?php

namespace App\Http\Requests\WebManagement;

use Illuminate\Foundation\Http\FormRequest;

class NavigationItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $isUpdate = $this->isMethod('PUT') || $this->isMethod('PATCH');

        return [
            // ── Core ──────────────────────────────────────────
            'label'     => [$isUpdate ? 'sometimes' : 'required', 'string', 'max:255'],
            'url'       => [$isUpdate ? 'sometimes' : 'required', 'string', 'max:255'],
            'slug'      => ['sometimes', 'nullable', 'string', 'max:100'],
            'order'     => ['sometimes', 'integer', 'min:0'],
            'is_active' => ['sometimes', 'boolean'],

            // ── SEO ───────────────────────────────────────────
            'meta_title'       => ['sometimes', 'nullable', 'string', 'max:60'],
            'meta_description' => ['sometimes', 'nullable', 'string', 'max:160'],
            'meta_keywords'    => ['sometimes', 'nullable', 'string', 'max:255'],
            'og_title'         => ['sometimes', 'nullable', 'string', 'max:60'],
            'og_description'   => ['sometimes', 'nullable', 'string', 'max:160'],
            'og_image'         => ['sometimes', 'nullable', 'string', 'max:255'],
            'canonical_url'    => ['sometimes', 'nullable', 'url', 'max:255'],
            'is_indexable'     => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'label.required'       => 'Navigation label is required.',
            'url.required'         => 'URL is required.',
            'meta_title.max'       => 'Meta title must not exceed 60 characters (Google limit).',
            'meta_description.max' => 'Meta description must not exceed 160 characters.',
            'og_title.max'         => 'OG title must not exceed 60 characters.',
            'og_description.max'   => 'OG description must not exceed 160 characters.',
            'canonical_url.url'    => 'Canonical URL must be a valid URL.',
        ];
    }
}
