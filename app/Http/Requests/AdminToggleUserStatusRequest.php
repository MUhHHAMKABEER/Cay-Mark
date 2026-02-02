<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdminToggleUserStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'action' => 'required|in:suspend,reactivate',
            'reason' => 'required_if:action,suspend|string|max:255',
        ];
    }
}

