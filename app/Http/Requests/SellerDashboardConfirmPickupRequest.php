<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SellerDashboardConfirmPickupRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'pickup_pin' => ['required', 'string', 'size:6', 'regex:/^[0-9]{6}$/'],
        ];
    }

    public function messages(): array
    {
        return [
            'pickup_pin.size' => 'Pickup code must be exactly 6 digits.',
            'pickup_pin.regex' => 'Pickup code must contain only digits.',
        ];
    }
}

