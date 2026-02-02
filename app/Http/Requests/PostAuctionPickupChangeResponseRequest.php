<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PostAuctionPickupChangeResponseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'action' => 'required|in:approve,counter',
            'countered_pickup_date' => 'required_if:action,counter|date|after_or_equal:today',
            'countered_pickup_time' => 'required_if:action,counter|date_format:H:i',
        ];
    }
}

