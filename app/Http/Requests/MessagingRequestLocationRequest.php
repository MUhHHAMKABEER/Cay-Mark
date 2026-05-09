<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MessagingRequestLocationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'pickup_detail_id' => 'required|integer|exists:pickup_details,id',
            'requested_location' => 'required|string|min:5|max:255',
            'additional_notes' => 'nullable|string|max:250',
        ];
    }
}
