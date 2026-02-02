<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SellerListingStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Authorization is handled elsewhere (middleware / guards)
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $user = $this->user();
        $userPackage = $user?->activeSubscription?->package;
        $isIndividualSeller = $userPackage && ((float) $userPackage->price === 25.00);

        return [
            // VIN/HIN (optional if manual entry)
            'vin' => 'nullable|string|max:17',

            // Manual fields (required if VIN decode fails)
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

            // Required condition fields
            'title_status' => 'required|in:yes,no',
            'island' => 'required|string',
            'color' => 'required|string',
            'interior_color' => 'required|string',
            'primary_damage' => 'required|string',
            'keys_available' => 'required|in:yes,no',
            'secondary_damage' => 'nullable|string',
            'additional_notes' => 'nullable|string',

            // SECTION 2 - Photos
            'cover_photo' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'photos.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:5120',

            // SECTION 3 - Auction Settings
            'auction_duration' => 'required|in:5,7,14,21,28',
            'starting_price' => 'nullable|numeric|min:0',
            'reserve_price' => 'nullable|numeric|min:0',
            'buy_now_price' => 'nullable|numeric|min:0',

            // Payment (Individual Sellers only)
            'payment_method' => $isIndividualSeller ? 'required|string' : 'nullable',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            // Section 1 - Vehicle Information
            'title_status.required' => 'Please select the title status (Yes/No) for your vehicle.',
            'title_status.in' => 'Invalid title status selected. Please choose Yes or No.',
            'island.required' => 'Please select the island location where your vehicle is located.',
            'color.required' => 'Please select the exterior color of your vehicle.',
            'interior_color.required' => 'Please select the interior color of your vehicle.',
            'primary_damage.required' => 'Please select the primary damage type for your vehicle.',
            'keys_available.required' => 'Please indicate if keys are available for your vehicle.',
            'keys_available.in' => 'Invalid selection. Please choose Yes or No for keys availability.',

            // Section 2 - Photos
            'cover_photo.required' => 'Cover photo is required. Please upload a cover image for your listing.',
            'cover_photo.image' => 'Cover photo must be an image file (JPEG, PNG, JPG, GIF, or WEBP).',
            'cover_photo.mimes' => 'Cover photo must be in JPEG, PNG, JPG, GIF, or WEBP format.',
            'cover_photo.max' => 'Cover photo size must not exceed 5MB. Please compress your image and try again.',
            'photos.*.image' => 'One or more photos are not valid image files. Please upload only image files.',
            'photos.*.mimes' => 'Photos must be in JPEG, PNG, JPG, GIF, or WEBP format.',
            'photos.*.max' => 'One or more photos exceed 5MB size limit. Please compress your images and try again.',

            // Section 3 - Auction Settings
            'auction_duration.required' => 'Please select the auction duration (5, 7, 14, 21, or 28 days).',
            'auction_duration.in' => 'Invalid auction duration selected. Please choose 5, 7, 14, 21, or 28 days.',
            'starting_price.numeric' => 'Starting price must be a valid number.',
            'starting_price.min' => 'Starting price cannot be negative.',
            'reserve_price.numeric' => 'Reserve price must be a valid number.',
            'reserve_price.min' => 'Reserve price cannot be negative.',
            'buy_now_price.numeric' => 'Buy Now price must be a valid number.',
            'buy_now_price.min' => 'Buy Now price cannot be negative.',

            // Payment
            'payment_method.required' => 'Payment method is required for Individual Sellers. Please select a payment method.',
        ];
    }
}

