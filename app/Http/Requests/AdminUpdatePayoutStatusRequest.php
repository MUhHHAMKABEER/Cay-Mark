<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
            'transaction_reference' => [
                'nullable',
                'string',
                'max:255',
                Rule::requiredIf(fn () => $this->input('status') === 'paid_successfully'),
            ],
            'date_sent' => [
                'nullable',
                'date',
                Rule::requiredIf(fn () => $this->input('status') === 'paid_successfully'),
            ],
            'finance_notes' => 'nullable|string|max:1000',
        ];
    }
}

