<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdminExtendAuctionTimeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'additional_days' => 'required|integer|min:1|max:30',
        ];
    }
}

