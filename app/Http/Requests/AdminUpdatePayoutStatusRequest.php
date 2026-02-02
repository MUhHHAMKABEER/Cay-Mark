<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdminUpdatePayoutStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => 'required|in:pending,processing,sent,on_hold,paid_successfully',
            'transaction_reference' => 'nullable|string|max:255',
            'date_sent' => 'nullable|date',
            'finance_notes' => 'nullable|string|max:1000',
        ];
    }
}

