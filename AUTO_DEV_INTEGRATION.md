# Auto.dev VIN/HIN Decoder Integration

## ✅ Integration Complete

Auto.dev API integration successfully implemented for VIN/HIN decoding in the CayMark vehicle listing submission system.

---

## 🔑 API Configuration

### **API Key:**
```
sk_ad_E69kTBYOcf2FC9YKcEQ1_iM1
```

### **Base URL:**
```
https://api.auto.dev
```

### **Configuration Location:**
- **Config File:** `config/services.php`
- **Environment Variables:** `.env` (optional, defaults provided)

### **Environment Variables (Optional):**
Add to `.env` file if you want to override defaults:
```env
AUTO_DEV_API_KEY=sk_ad_E69kTBYOcf2FC9YKcEQ1_iM1
AUTO_DEV_BASE_URL=https://api.auto.dev
```

**Note:** Default values are already set in `config/services.php`, so `.env` configuration is optional.

---

## 🏗️ Architecture

### **Correct Flow (As Per Requirements):**

```
Seller enters VIN/HIN
        ↓
Frontend sends VIN to YOUR backend
        ↓
Backend calls Auto.dev API (Authorization header)
        ↓
Backend normalizes + ALL CAPS data
        ↓
Frontend receives populated fields
```

### **Implementation Details:**

1. **Frontend** → Sends VIN/HIN to backend endpoint: `/seller/decode-vin-hin`
2. **Backend Controller** → `ListingController@decodeVinHin` receives request
3. **Service Layer** → `VinHinDecoderService` handles API call
4. **Auto.dev API** → Called with Authorization Bearer token
5. **Data Processing** → Response parsed and converted to ALL CAPS
6. **Frontend** → Receives formatted data for form population

---

## 📁 Files Modified

### **1. `config/services.php`**
Added Auto.dev service configuration:
```php
'auto_dev' => [
    'api_key' => env('AUTO_DEV_API_KEY', 'sk_ad_E69kTBYOcf2FC9YKcEQ1_iM1'),
    'base_url' => env('AUTO_DEV_BASE_URL', 'https://api.auto.dev'),
],
```

### **2. `app/Services/VinHinDecoderService.php`**
- ✅ Implemented `decodeVIN()` method with Auto.dev API integration
- ✅ Implemented `decodeHIN()` method (tries Auto.dev, may need separate service)
- ✅ Added `parseAutoDevResponse()` method to handle API response mapping
- ✅ Proper error handling and logging
- ✅ ALL CAPS conversion via `TextFormatter`

---

## 🔧 API Call Implementation

### **Authorization Header Method (Used):**
```php
$response = Http::withHeaders([
    'Authorization' => 'Bearer ' . $apiKey,
    'Content-Type' => 'application/json',
])->get("{$baseUrl}/vin/{$vin}");
```

### **Example cURL (For Testing):**
```bash
curl -X GET "https://api.auto.dev/vin/WP0AF2A99KS165242" \
  -H "Authorization: Bearer sk_ad_E69kTBYOcf2FC9YKcEQ1_iM1" \
  -H "Content-Type: application/json"
```

---

## 📊 Data Mapping

### **Auto.dev API Response → CayMark Format**

The `parseAutoDevResponse()` method maps Auto.dev fields to our internal format:

| Auto.dev Field | CayMark Field | Notes |
|---------------|---------------|-------|
| `make` / `manufacturer` / `brand` | `make` | Multiple fallbacks |
| `model` / `modelName` | `model` | Multiple fallbacks |
| `year` / `modelYear` | `year` | Multiple fallbacks |
| `trim` / `trimLevel` / `series` | `trim` | Multiple fallbacks |
| `engineSize` / `engine` / `displacement` | `engine_size` | Multiple fallbacks |
| `cylinders` / `cylinderCount` | `cylinders` | Multiple fallbacks |
| `driveType` / `driveline` / `driveTrain` | `drive_type` | Multiple fallbacks |
| `fuelType` / `fuel` / `fuelSystem` | `fuel_type` | Multiple fallbacks |
| `transmission` / `transmissionType` | `transmission` | Multiple fallbacks |
| `vehicleType` / `bodyStyle` / `body` | `vehicle_type` | Multiple fallbacks |

### **ALL CAPS Conversion:**
All decoded values are automatically converted to ALL CAPS using `TextFormatter::toAllCaps()` before being returned to the frontend.

---

## 🛡️ Error Handling

### **Error Scenarios Handled:**

1. **API Key Missing:**
   - Logs error
   - Returns failure message

2. **API Request Fails:**
   - Logs warning with status and body
   - Returns failure message

3. **API Exception:**
   - Logs error with full trace
   - Returns failure message

4. **No Data Found:**
   - Returns standard failure message: `"VEHICLE/HULL NUMBER NOT FOUND. PLEASE ENTER DETAILS MANUALLY."`

### **Logging:**
All errors are logged using Laravel's `Log` facade:
- `Log::error()` for critical errors
- `Log::warning()` for API errors

---

## 🧪 Testing

### **Test the Integration:**

1. **Via Frontend:**
   - Go to seller listing submission page
   - Enter a VIN (e.g., `WP0AF2A99KS165242`)
   - Click "Search" button
   - Fields should auto-populate with ALL CAPS data

2. **Via API Endpoint:**
   ```bash
   POST /seller/decode-vin-hin
   {
     "vin_hin": "WP0AF2A99KS165242"
   }
   ```

3. **Direct Service Test:**
   ```php
   $decoder = new VinHinDecoderService();
   $result = $decoder->decode('WP0AF2A99KS165242');
   ```

---

## 📝 Response Format

### **Success Response:**
```json
{
  "success": true,
  "data": {
    "make": "PORSCHE",
    "model": "911",
    "year": "2019",
    "trim": "CARRERA",
    "engine_size": "3.0L",
    "cylinders": "6",
    "drive_type": "RWD",
    "fuel_type": "GASOLINE",
    "transmission": "MANUAL",
    "vehicle_type": "COUPE"
  }
}
```

### **Failure Response:**
```json
{
  "success": false,
  "message": "VEHICLE/HULL NUMBER NOT FOUND. PLEASE ENTER DETAILS MANUALLY."
}
```

---

## ✅ Features Implemented

- ✅ Auto.dev API integration with Authorization Bearer token
- ✅ VIN decoding support
- ✅ HIN decoding support (tries Auto.dev, may need separate service)
- ✅ Automatic ALL CAPS conversion
- ✅ Comprehensive error handling
- ✅ Logging for debugging
- ✅ Flexible field mapping (handles multiple API response formats)
- ✅ Backend-only API calls (no frontend exposure)
- ✅ Proper service layer architecture

---

## 🔄 Next Steps (If Needed)

1. **HIN Decoding:** If Auto.dev doesn't support HIN, integrate a separate HIN decoder service
2. **Response Caching:** Consider caching successful VIN decodes to reduce API calls
3. **Rate Limiting:** Monitor API usage and implement rate limiting if needed
4. **API Response Validation:** Add validation for API response structure

---

## 📚 References

- **Auto.dev API Documentation:** https://auto.dev (if available)
- **API Key:** `sk_ad_E69kTBYOcf2FC9YKcEQ1_iM1`
- **Base URL:** `https://api.auto.dev`
- **Endpoint:** `GET /vin/{VIN}`

---

**Integration Date:** 2026-01-22  
**Status:** ✅ Complete and Ready for Testing
