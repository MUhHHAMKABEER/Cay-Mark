<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdminUpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('id');

        return [
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $id,
            'phone' => 'sometimes|nullable|string',
            'role' => 'sometimes|in:buyer,seller,admin',
            'is_restricted' => 'sometimes|boolean',
            'restriction_ends_at' => 'sometimes|nullable|date',
            'restriction_reason' => 'sometimes|nullable|string',
            'internal_notes' => 'sometimes|nullable|string',
        ];
    }
}

