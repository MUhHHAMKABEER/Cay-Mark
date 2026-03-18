<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SellerListingUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Same as store but cover_photo and photos are optional (keep existing if not provided).
     */
    public function rules(): array
    {
        $user = $this->user();
        $userPackage = $user?->activeSubscription?->package;
        $isIndividualSeller = $userPackage && ((float) $userPackage->price === 25.00);

        return [
            'vin' => 'nullable|string|max:17',
            'make' => 'nullable|string',
            'model' => 'nullable|string',
            'year' => 'nullable|string',
            'trim' => 'nullable|string',
            'engine_size' => 'nullable|string',
            'cylinders' => 'nullable|string',
            'drive_type' => 'nullable|string',
            'fuel_type' => 'nullable|string',
            'transmission' => 'nullable|string',
            'vehicle_type' => 'nullable|string',
            'title_status' => 'required|in:yes,no',
            'island' => 'required|string',
            'color' => 'required|string',
            'interior_color' => 'required|string',
            'primary_damage' => 'required|string',
            'keys_available' => 'required|in:yes,no',
            'is_salvaged' => 'required|in:0,1',
            'odometer' => 'nullable|integer|min:0|max:9999999',
            'odometer_estimated' => 'nullable|boolean',
            'secondary_damage' => 'nullable|string',
            'additional_notes' => 'nullable|string',
            'cover_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'photos.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'auction_duration' => 'required|in:5,7,14,21,28',
            'starting_price' => 'nullable|numeric|min:0',
            'reserve_price' => 'nullable|numeric|min:0',
            'buy_now_price' => 'nullable|numeric|min:0',
            'payment_method' => 'nullable|string',
        ];
    }
}
