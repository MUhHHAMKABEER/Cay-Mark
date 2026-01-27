<?php

namespace App\Services;

use App\Helpers\TextFormatter;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class VinHinDecoderService
{
    /**
     * Detect if input is VIN (17 chars) or HIN (12 chars).
     * 
     * @param string $input
     * @return string 'vin' or 'hin'
     */
    public function detectType(string $input): string
    {
        $cleaned = strtoupper(preg_replace('/[^A-Z0-9]/', '', $input));
        
        // HIN is typically 12 characters, VIN is 17
        if (strlen($cleaned) <= 12) {
            return 'hin';
        }
        
        return 'vin';
    }

    /**
     * Decode VIN or HIN and return vehicle data.
     * 
     * @param string $vinOrHin
     * @return array ['success' => bool, 'data' => array, 'message' => string]
     */
    public function decode(string $vinOrHin): array
    {
        $type = $this->detectType($vinOrHin);
        $cleaned = strtoupper(preg_replace('/[^A-Z0-9]/', '', $vinOrHin));

        try {
            if ($type === 'vin') {
                return $this->decodeVIN($cleaned);
            } else {
                return $this->decodeHIN($cleaned);
            }
        } catch (\Exception $e) {
            Log::error('VIN/HIN Decoder Error: ' . $e->getMessage());
            return [
                'success' => false,
                'data' => [],
                'message' => 'VEHICLE/HULL NUMBER NOT FOUND. PLEASE ENTER DETAILS MANUALLY.',
            ];
        }
    }

    /**
     * Decode VIN using Auto.dev API.
     * 
     * @param string $vin
     * @return array
     */
    protected function decodeVIN(string $vin): array
    {
        $apiKey = config('services.auto_dev.api_key');
        $baseUrl = config('services.auto_dev.base_url');
        
        if (empty($apiKey)) {
            Log::error('Auto.dev API key not configured');
            return [
                'success' => false,
                'data' => [],
                'message' => 'VEHICLE/HULL NUMBER NOT FOUND. PLEASE ENTER DETAILS MANUALLY.',
            ];
        }

        try {
            // Call Auto.dev API with Authorization header
            $url = "{$baseUrl}/vin/{$vin}";
            
            Log::info('Auto.dev API Request', [
                'url' => $url,
                'vin' => $vin,
                'api_key_prefix' => substr($apiKey, 0, 10) . '...',
            ]);
            
            $response = Http::withoutVerifying()->withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])->timeout(30)->get($url);

            $status = $response->status();
            $body = $response->body();
            
            Log::info('Auto.dev API Response', [
                'status' => $status,
                'vin' => $vin,
                'response_preview' => substr($body, 0, 500),
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                // Log full response for debugging
                Log::debug('Auto.dev API Full Response', [
                    'vin' => $vin,
                    'response' => $data,
                ]);
                
                // Parse Auto.dev API response and map to our format
                $decodedData = $this->parseAutoDevResponse($data);
                
                Log::info('Auto.dev Parsed Data', [
                    'vin' => $vin,
                    'decoded_fields' => array_keys($decodedData),
                    'decoded_data' => $decodedData,
                ]);
                
                if (!empty($decodedData)) {
                    return [
                        'success' => true,
                        'data' => $decodedData,
                        'message' => 'Vehicle information decoded successfully.',
                    ];
                } else {
                    Log::warning('Auto.dev API returned empty parsed data', [
                        'vin' => $vin,
                        'raw_response' => $data,
                    ]);
                }
            } else {
                Log::warning('Auto.dev API error', [
                    'status' => $status,
                    'vin' => $vin,
                    'body' => $body,
                    'headers' => $response->headers(),
                ]);
            }
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error('Auto.dev API connection exception', [
                'vin' => $vin,
                'message' => $e->getMessage(),
            ]);
        } catch (\Exception $e) {
            Log::error('Auto.dev API exception', [
                'vin' => $vin,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }

        // Return failure if API call fails or no data found
        return [
            'success' => false,
            'data' => [],
            'message' => 'VEHICLE/HULL NUMBER NOT FOUND. PLEASE ENTER DETAILS MANUALLY.',
        ];
    }

    /**
     * Decode HIN using Auto.dev API (if supported) or return failure.
     * Note: Auto.dev primarily supports VIN decoding. HIN decoding may need separate service.
     * 
     * @param string $hin
     * @return array
     */
    protected function decodeHIN(string $hin): array
    {
        // Auto.dev primarily supports VIN decoding
        // For HIN (Hull Identification Number), we may need a different service
        // For now, try Auto.dev API (it may support some HIN formats)
        
        $apiKey = config('services.auto_dev.api_key');
        $baseUrl = config('services.auto_dev.base_url');
        
        if (empty($apiKey)) {
            Log::error('Auto.dev API key not configured');
            return [
                'success' => false,
                'data' => [],
                'message' => 'VEHICLE/HULL NUMBER NOT FOUND. PLEASE ENTER DETAILS MANUALLY.',
            ];
        }

        try {
            // Try calling Auto.dev API with HIN (may or may not work)
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json',
            ])->get("{$baseUrl}/vin/{$hin}"); // Using same endpoint, may need adjustment

            if ($response->successful()) {
                $data = $response->json();
                $decodedData = $this->parseAutoDevResponse($data);
                
                if (!empty($decodedData)) {
                    return [
                        'success' => true,
                        'data' => $decodedData,
                        'message' => 'Hull information decoded successfully.',
                    ];
                }
            }
        } catch (\Exception $e) {
            Log::error('Auto.dev HIN API exception: ' . $e->getMessage());
        }

        return [
            'success' => false,
            'data' => [],
            'message' => 'VEHICLE/HULL NUMBER NOT FOUND. PLEASE ENTER DETAILS MANUALLY.',
        ];
    }

    /**
     * Parse Auto.dev API response and map to our internal format.
     * Auto.dev API response structure may vary, so we handle multiple possible field names.
     * 
     * @param array $apiResponse
     * @return array
     */
    protected function parseAutoDevResponse(array $apiResponse): array
    {
        // Auto.dev API response structure:
        // - Root level: make, model, trim, engine, drive, transmission, body
        // - Nested 'vehicle': year, make, model, manufacturer
        
        // Extract from root level
        $data = $apiResponse;
        
        // Extract from nested 'vehicle' object if it exists
        if (isset($apiResponse['vehicle']) && is_array($apiResponse['vehicle'])) {
            $data = array_merge($data, $apiResponse['vehicle']);
        }
        
        // Map Auto.dev fields to our internal format
        // Based on actual API response structure
        $mapped = [
            'make' => $data['make'] ?? $data['manufacturer'] ?? null,
            'model' => $data['model'] ?? null,
            'year' => $data['year'] ?? $data['modelYear'] ?? null,
            'trim' => $data['trim'] ?? $data['trimLevel'] ?? $data['series'] ?? null,
            'engine_size' => $data['engine'] ?? $data['engineSize'] ?? $data['style'] ?? $data['displacement'] ?? null,
            'cylinders' => $this->extractCylinders($data['engine'] ?? $data['style'] ?? null),
            'drive_type' => $data['drive'] ?? $data['driveType'] ?? $data['driveline'] ?? $data['driveTrain'] ?? null,
            'fuel_type' => $data['fuelType'] ?? $data['fuel'] ?? $data['fuelSystem'] ?? null,
            'transmission' => $data['transmission'] ?? $data['transmissionType'] ?? $data['transmissionDesc'] ?? null,
            'vehicle_type' => $data['body'] ?? $data['type'] ?? $data['vehicleType'] ?? $data['bodyStyle'] ?? $data['category'] ?? null,
        ];
        
        // Remove null values
        return array_filter($mapped, function ($value) {
            return $value !== null && $value !== '';
        });
    }
    
    /**
     * Extract cylinder count from engine description.
     * 
     * @param string|null $engineDescription
     * @return string|null
     */
    protected function extractCylinders(?string $engineDescription): ?string
    {
        if (empty($engineDescription)) {
            return null;
        }
        
        // Try to extract cylinder count from strings like "4.0, Flat 6 Cylinder Engine"
        if (preg_match('/(\d+)\s*Cylinder/i', $engineDescription, $matches)) {
            return $matches[1];
        }
        
        // Try patterns like "V6", "V8", "I4", "Flat 6"
        if (preg_match('/(?:V|I|Flat|Inline|Boxer)\s*(\d+)/i', $engineDescription, $matches)) {
            return $matches[1];
        }
        
        return null;
    }

    /**
     * Format decoded data to match form fields (ALL CAPS).
     * 
     * @param array $decodedData
     * @return array
     */
    public function formatDecodedData(array $decodedData): array
    {
        return [
            'make' => TextFormatter::toAllCaps($decodedData['make'] ?? null),
            'model' => TextFormatter::toAllCaps($decodedData['model'] ?? null),
            'year' => TextFormatter::toAllCaps($decodedData['year'] ?? null),
            'trim' => TextFormatter::toAllCaps($decodedData['trim'] ?? null),
            'engine_size' => TextFormatter::toAllCaps($decodedData['engine_size'] ?? $decodedData['engine'] ?? null),
            'cylinders' => TextFormatter::toAllCaps($decodedData['cylinders'] ?? null),
            'drive_type' => TextFormatter::toAllCaps($decodedData['drive_type'] ?? $decodedData['driveline'] ?? null),
            'fuel_type' => TextFormatter::toAllCaps($decodedData['fuel_type'] ?? $decodedData['fuel'] ?? null),
            'transmission' => TextFormatter::toAllCaps($decodedData['transmission'] ?? null),
            'vehicle_type' => TextFormatter::toAllCaps($decodedData['vehicle_type'] ?? $decodedData['body_style'] ?? null),
        ];
    }
}
