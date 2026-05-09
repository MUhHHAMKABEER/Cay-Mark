<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MessagingRespondDeliveryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'action' => 'required|in:approve,reject',
            'response_notes' => 'nullable|string|max:500',
        ];
    }
}
