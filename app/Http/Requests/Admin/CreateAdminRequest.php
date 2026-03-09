<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class CreateAdminRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'name'       => ['required', 'string', 'max:255'],
            'email'      => ['required', 'email', 'unique:users,email'],
            'password'   => ['required', 'string', 'min:8'],
            'hotel_name' => ['required', 'string'],
            'domain'     => ['nullable', 'string', 'unique:hotels,domain'],
            'phone'      => ['required', 'string'],
        ];
    }
}