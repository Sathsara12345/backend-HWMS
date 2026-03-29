<?php

namespace App\Http\Requests\WebManagement;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\FileTypeSize;

class UploadSectionMediaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'file' => [
                'required',
                'file',
                'mimetypes:image/jpeg,image/png,image/webp,video/mp4,video/webm,video/ogg,video/x-matroska,video/quicktime',
                new FileTypeSize(),
            ],
            'key' => ['required', 'string', 'max:100'],
        ];
    }

    public function messages(): array
    {
         return [
            'file.required'  => 'Please select a file to upload.',
            'file.mimetypes' => 'File must be an image (jpg, png, webp) or video (mp4, webm, mkv, mov).',
            'key.required'   => 'A field key is required (e.g. background_image, background_video).',
        ];
    }
}
