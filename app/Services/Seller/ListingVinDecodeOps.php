<?php

namespace App\Services\Seller;

class ListingVinDecodeOps
{
    public static function decode($request)
    {
        \Log::info('[VIN-API] decodeVinHin request received', [
            'vin_hin_length' => strlen($request->input('vin_hin', '')),
            'vin_hin_preview' => substr($request->input('vin_hin', ''), 0, 6) . '...',
            'has_csrf' => $request->hasHeader('X-CSRF-TOKEN') || $request->has('_token'),
        ]);

        try {
            $request->validated();
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::warning('[VIN-API] decodeVinHin validation failed', [
                'errors' => $e->errors(),
                'input' => $request->only('vin_hin'),
            ]);
            throw $e;
        }

        $vinHin = $request->vin_hin;
        \Log::info('[VIN-API] decodeVinHin calling decoder', ['vin_hin' => $vinHin]);

        try {
            $decoder = new \App\Services\VinHinDecoderService();
            $result = $decoder->decode($vinHin);
        } catch (\Throwable $e) {
            \Log::error('[VIN-API] decodeVinHin decoder threw', [
                'vin_hin' => $vinHin,
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'VEHICLE/HULL NUMBER NOT FOUND. PLEASE ENTER DETAILS MANUALLY.',
            ], 500);
        }

        if ($result['success']) {
            $formatted = $decoder->formatDecodedData($result['data']);
            \Log::info('[VIN-API] decodeVinHin success', [
                'vin_hin' => $vinHin,
                'fields_returned' => array_keys($formatted),
            ]);
            return response()->json([
                'success' => true,
                'data' => $formatted,
            ]);
        }

        \Log::info('[VIN-API] decodeVinHin no data', [
            'vin_hin' => $vinHin,
            'service_message' => $result['message'] ?? '',
        ]);
        return response()->json([
            'success' => false,
            'message' => $result['message'],
        ]);
    }
}

