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
            'bank_name'            => 'required|string|max:255',
            'account_holder_name'  => 'required|string|max:255',
            'account_number'       => 'nullable|string|max:255',
            'routing_number'       => 'nullable|string|max:255',
        ];
    }
}

