<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PostAuctionPickupChangeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'pickup_detail_id' => 'required|exists:pickup_details,id',
            'requested_pickup_date' => 'nullable|date|after_or_equal:today',
            'requested_pickup_time' => 'nullable|date_format:H:i',
        ];
    }
}

