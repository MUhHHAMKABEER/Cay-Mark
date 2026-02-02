<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PostAuctionThirdPartyPickupRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'authorized_name' => 'required|string|max:255',
            'pickup_type' => 'required|in:tow_company,individual,authorized_representative',
        ];
    }
}

