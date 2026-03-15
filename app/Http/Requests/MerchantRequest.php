<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MerchantRequest extends FormRequest
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
            'name'       => 'required|string|max:255',
            'email'      => 'required|email',
            'password'   => 'nullable|string|min:8',
            'hotel_name' => 'required|string',
            'domain'     => 'nullable|string',
            'phone'      => 'required|string',
            'status'     => 'nullable|in:active,suspended',
            'navigation_items' => 'nullable|array',
            'navigation_items.*.label' => 'required|string',
            'navigation_items.*.url' => 'nullable|string',
            'navigation_items.*.order' => 'required|integer',
            'page_sections' => 'nullable|array',
            'page_sections.*.section_name' => 'required|string',
            'page_sections.*.title' => 'nullable|string',
            'page_sections.*.content' => 'nullable|string',
            'page_sections.*.order' => 'required|integer',
        ];
    }
}
