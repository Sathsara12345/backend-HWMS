<?php

namespace App\Http\Requests\WebManagement;

use Illuminate\Foundation\Http\FormRequest;

class ReorderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'items'          => ['required', 'array', 'min:1'],
            'items.*.id'     => ['required', 'integer'],
            'items.*.order'  => ['required', 'integer', 'min:0'],
        ];
    }
}
