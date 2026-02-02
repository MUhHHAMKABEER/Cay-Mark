<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PostAuctionPickupDetailsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'pickup_date' => 'required|date|after_or_equal:today',
            'pickup_time' => 'required|date_format:H:i',
            'street_address' => 'required|string|max:255',
            'directions_notes' => 'nullable|string|max:1000',
        ];
    }
}

