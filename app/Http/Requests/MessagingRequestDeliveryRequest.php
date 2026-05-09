<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MessagingRequestDeliveryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'delivery_address' => 'required|string|min:5|max:255',
            'preferred_date' => 'nullable|date|after_or_equal:today',
            'preferred_time' => 'nullable|date_format:H:i',
            'additional_notes' => 'nullable|string|max:250',
        ];
    }
}
