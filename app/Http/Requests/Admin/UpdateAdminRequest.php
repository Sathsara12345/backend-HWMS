<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAdminRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $userId = $this->route('id');

        return [
            'name'       => ['sometimes', 'string', 'max:255'],
            'email'      => ['sometimes', 'email', "unique:users,email,{$userId}"],
            'password'   => ['sometimes', 'string', 'min:8'],
            'hotel_name' => ['sometimes', 'string'],
            'domain'     => ['nullable', 'string', "unique:hotels,domain,{$userId},user_id"],
            'phone'      => ['sometimes', 'string'],
            'status'     => ['sometimes', 'in:active,suspended'],
        ];
    }
}