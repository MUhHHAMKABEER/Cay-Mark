<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PostAuctionConfirmPickupPinRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'pickup_pin' => 'required|string|size:4',
        ];
    }
}

