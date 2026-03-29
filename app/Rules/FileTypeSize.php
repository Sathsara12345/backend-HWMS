<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class FileTypeSize implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $mime = $value->getMimeType();
        $size = $value->getSize(); // bytes

        if (str_starts_with($mime, 'image/') && $size > 5 * 1024 * 1024) {
            $fail('The image must not exceed 5MB.');
        }

        if (str_starts_with($mime, 'video/') && $size > 10 * 1024 * 1024) {
            $fail('The video must not exceed 10MB.');
        }
    }
}
