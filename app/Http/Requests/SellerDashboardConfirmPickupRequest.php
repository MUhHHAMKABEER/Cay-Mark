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
            'pickup_pin' => 'required|string',
        ];
    }
}

