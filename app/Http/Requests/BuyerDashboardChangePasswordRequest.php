<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class BuyerDashboardChangePasswordRequest extends FormRequest
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
                'confirmed',
                'max:15',
                Password::defaults(),
                function (string $attribute, mixed $value, \Closure $fail): void {
                    if (Hash::check($value, $this->user()->password)) {
                        $fail('The new password must be different from your current password.');
                    }
                },
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'password.regex' => 'Password must include at least one uppercase letter, one number, and one special character.',
        ];
    }
}

