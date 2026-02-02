<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BuyerPaymentInvoiceSelectionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'invoice_ids' => 'required|array',
            'invoice_ids.*' => 'exists:invoices,id',
        ];
    }
}

