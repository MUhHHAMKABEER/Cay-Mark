<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SellerPayoutMethodStoreRequest extends FormRequest
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
            'account_number' => 'required|string|max:255',
            'routing_number' => 'nullable|string|max:50',
            'swift_number' => 'nullable|string|max:50',
            'country' => 'nullable|string|max:255',
            'card_number' => 'required|string|max:23',
            'card_cvc' => 'required|string|max:4',
            'card_expiry' => ['required', 'string', 'max:5', 'regex:/^(0[1-9]|1[0-2])\/\d{2}$/'],
            'additional_instructions' => 'nullable|string|max:1000',
        ];
    }
}

