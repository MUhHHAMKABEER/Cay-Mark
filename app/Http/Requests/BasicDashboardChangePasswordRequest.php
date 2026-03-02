<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;

class BasicDashboardChangePasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'current_password' => 'required|current_password',
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
                function (string $attribute, mixed $value, \Closure $fail): void {
                    if (Hash::check($value, $this->user()->password)) {
                        $fail('The new password must be different from your current password.');
                    }
                },
            ],
        ];
    }
}

