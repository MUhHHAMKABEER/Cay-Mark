<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SellerDashboardUpdatePayoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'bank_name' => 'required|string|max:255',
            'account_holder_name' => 'required|string|max:255',
            'account_number' => 'nullable|string|max:255', // optional when updating (leave blank to keep current)
            'routing_number' => 'nullable|string|max:255',
            'swift_number' => 'nullable|string|max:255',
            'swift_code' => 'nullable|string|max:255', // alias for backward compatibility
            'bank_address' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'card_number' => 'nullable|string|max:23',
            'card_cvc' => 'nullable|string|max:4',
            'card_expiry' => ['nullable', 'string', 'max:5', 'regex:/^(0[1-9]|1[0-2])\/\d{2}$/'],
        ];
    }
}

