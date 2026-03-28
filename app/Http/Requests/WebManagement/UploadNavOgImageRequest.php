<?php

namespace App\Http\Requests\WebManagement;

use Illuminate\Foundation\Http\FormRequest;

class UploadNavOgImageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'og_image' => [
                'required',
                'file',
                'mimes:jpg,jpeg,png,webp',
                'max:5120', // 5MB
                // Recommended OG image size is 1200x627px
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'og_image.required' => 'Please select an image to upload.',
            'og_image.mimes'    => 'OG image must be jpg, png, or webp.',
            'og_image.max'      => 'OG image must not exceed 5MB.',
        ];
    }
}
