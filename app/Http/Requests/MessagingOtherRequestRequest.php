<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MessagingOtherRequestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'subject' => 'required|string|max:120',
            'body' => 'required|string|min:5|max:500',
        ];
    }
}
