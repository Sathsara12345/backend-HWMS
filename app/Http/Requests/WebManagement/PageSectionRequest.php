<?php

namespace App\Http\Requests\WebManagement;

use Illuminate\Foundation\Http\FormRequest;

class PageSectionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // ── Text fields ───────────────────────────────────────
            'navigation_item_id' => ['nullable', 'integer', 'exists:navigation_items,id'],
            'section_name'       => ['required', 'string', 'max:255'],
            'title'              => ['nullable', 'string', 'max:255'],
            'sub_title'          => ['nullable', 'string', 'max:255'],
            'content'            => ['nullable', 'string'],
            'order'              => ['sometimes', 'integer', 'min:0'],
            'is_visible'         => ['sometimes', 'boolean'],

            // ── Media files (all optional) ──────────────
            'media'              => ['sometimes', 'array'],
            'media.video'        => ['sometimes', 'file', 'mimetypes:video/mp4,video/webm,video/ogg', 'max:10240'],
            'media.image'        => ['sometimes', 'file', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'media.banner'       => ['sometimes', 'file', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'media.poster'       => ['sometimes', 'file', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'media.background'   => ['sometimes', 'file', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ];
    }

    public function messages(): array
    {
        return [
            'section_name.required'    => 'Section name is required.',
            'media.video.mimetypes'    => 'Video must be mp4, webm, or ogg.',
            'media.video.max'          => 'Video file must not exceed 10MB.',
            'media.image.mimes'        => 'Image must be jpg, png, or webp.',
            'media.image.max'          => 'Image must not exceed 5MB.',
            'media.banner.mimes'       => 'Banner must be jpg, png, or webp.',
            'media.banner.max'         => 'Banner must not exceed 5MB.',
            'media.poster.mimes'       => 'Poster must be jpg, png, or webp.',
            'media.poster.max'         => 'Poster must not exceed 5MB.',
            'media.background.mimes'   => 'Background must be jpg, png, or webp.',
            'media.background.max'     => 'Background must not exceed 5MB.',
        ];
    }
}
