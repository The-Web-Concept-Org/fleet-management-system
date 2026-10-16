<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OcrController extends Controller
{
    /**
     * Gemini candidate models in order of speed, lowest price, and accuracy.
     */
    protected array $candidateModels = [
        'gemini-3.5-flash',
        'gemini-3.5-flash-lite',
        'gemini-3.8-flash',
        'gemini-flash-latest',
        'gemini-flash-lite-latest',
    ];

    /**
     * Extract Odometer / Speedometer reading from vehicle instrument cluster photo.
     *
     * POST /api/ocr/odometer or POST /api/ocr/meter
     */
    public function extractOdometer(Request $request): JsonResponse
    {
        $fileInfo = $this->resolveImagePayload($request);
        if (!$fileInfo) {
            return response()->json([
                'success' => false,
                'fallbackToLocal' => true,
                'message' => 'No valid meter image provided. Please upload a photo of the speedometer or odometer.',
            ], 400);
        }

        $apiKey = $this->getGeminiApiKey($request);
        if (!$apiKey) {
            return response()->json([
                'success' => false,
                'fallbackToLocal' => true,
                'message' => 'GEMINI_API_KEY is not configured on the server. Fallback to on-device ML Kit OCR.',
            ], 200);
        }

        $prompt = <<<PROMPT
You are an expert commercial fleet instrument cluster and speedometer vision analyst.
Analyze this photo of a vehicle speedometer / odometer / dashboard instrument cluster.

CRITICAL INSTRUCTIONS:
1. Locate the ODOMETER reading (the total distance / kilometers / miles logged by the vehicle):
   - On ANALOG rolling-drum counters: Read the mechanical numbers. Note that if the rightmost drum has a different background (often red or white on black) or is separated by a decimal, it is tenths of a kilometer (0.1 km); ONLY extract the whole integer kilometers in "odometer".
   - On DIGITAL LCD / TFT screens: Look for the number next to or below "ODO", "ODOMETER", "KM", or "TOTAL".
2. Identify the unit: "km" or "miles" (default "km" if standard metric or unclear).
3. Identify dial type: "analog" (mechanical rolling drum counter) or "digital" (electronic LCD/LED display).
4. If a fuel gauge is visible on the cluster, estimate the fuel level percentage as an integer from 0 to 100 (or null if not visible/unclear).
5. Confidence: estimate confidence between 85.0 and 99.9.

Return ONLY a valid JSON object with this exact structure:
{
  "odometer": 142760,
  "unit": "km",
  "dial_type": "analog",
  "fuel_level_percent": 75,
  "confidence": 99.2,
  "raw_text": "KILOMETERS 142760 km"
}
PROMPT;

        $result = $this->callGeminiVision($prompt, $fileInfo['base64'], $fileInfo['mimeType'], $apiKey);

        if (!$result || !isset($result['odometer'])) {
            return response()->json([
                'success' => false,
                'fallbackToLocal' => true,
                'message' => 'Gemini vision could not read the odometer. Fallback to on-device ML Kit OCR.',
            ], 422);
        }

        $odometerInt = (int) preg_replace('/[^0-9]/', '', (string) $result['odometer']);

        return response()->json([
            'success' => true,
            'scan_method' => 'GEMINI_AI',
            'method_label' => 'Google Gemini 3.5 Flash-Lite Vision',
            'data' => [
                'odometer' => $odometerInt,
                'unit' => strtolower((string) ($result['unit'] ?? 'km')),
                'dial_type' => strtolower((string) ($result['dial_type'] ?? 'analog')),
                'fuel_level_percent' => isset($result['fuel_level_percent']) ? (int) $result['fuel_level_percent'] : null,
                'confidence' => (float) ($result['confidence'] ?? 98.5),
                'raw_text' => (string) ($result['raw_text'] ?? "Odometer: {$odometerInt} km"),
                'extracted_locally' => false,
            ],
        ]);
    }

    /**
     * Extract Fuel Station Pump Screen or Printed Receipt details.
     *
     * POST /api/ocr/fuel
     */
    public function extractFuel(Request $request): JsonResponse
    {
        $fileInfo = $this->resolveImagePayload($request);
        if (!$fileInfo) {
            return response()->json([
                'success' => false,
                'fallbackToLocal' => true,
                'message' => 'No valid fuel image provided. Please upload a photo of the fuel pump display or receipt.',
            ], 400);
        }

        $apiKey = $this->getGeminiApiKey($request);
        if (!$apiKey) {
            return response()->json([
                'success' => false,
                'fallbackToLocal' => true,
                'message' => 'GEMINI_API_KEY is not configured on the server. Fallback to on-device ML Kit OCR.',
            ], 200);
        }

        $prompt = <<<PROMPT
You are an expert fuel station pump display and printed receipt analyst for commercial fleets.
Analyze this photo of a fuel pump screen or printed fuel receipt slip.

CRITICAL INSTRUCTIONS:
1. Extract the fuel volume in Liters (or Gallons):
   - Look for "Volume", "Liters", "Litres", "Qty", "L", "LTR". Return as a float number in "liters".
2. Extract the Total Cost / Amount:
   - Look for "Total", "Amount", "Cost", "Price", "$", "EUR", "USD", etc. Return as float in "total_cost".
3. Extract the Unit Price per liter:
   - Look for "Price/L", "Rate", "Price/Unit", "$/L". If missing but total_cost and liters are present, calculate total_cost / liters. Return as float in "unit_price".
4. Extract the Fuel Station / Brand / Location:
   - Name of brand or station (e.g. Shell, Chevron, BP, Total, Depot Terminal, etc.) in "station_name".
5. Extract the Fuel Type:
   - ONLY "Diesel" or "Petrol" in "fuel_type" (Commercial bus fleet vehicles use Diesel or Petrol). If not explicitly Petrol, default to "Diesel". Do NOT use CNG, Bio-Diesel, LPG, or Electric.
6. If an odometer or mileage reading is printed on the receipt, extract it into "odometer" as an integer (or null).
7. Extract receipt number or transaction reference in "receipt_number" (or null).
8. Extract date in "YYYY-MM-DD" format in "date" (or null).
9. Confidence: estimate confidence between 85.0 and 99.9.

Return ONLY a valid JSON object with this exact structure:
{
  "liters": 120.0,
  "total_cost": 195.0,
  "unit_price": 1.625,
  "station_name": "Airport Terminal Fuel Depot",
  "fuel_type": "Diesel",
  "odometer": null,
  "receipt_number": "REC-8841",
  "date": "2026-10-16",
  "confidence": 99.0,
  "raw_text": "VOLUME 120.0L TOTAL $195.00"
}
PROMPT;

        $result = $this->callGeminiVision($prompt, $fileInfo['base64'], $fileInfo['mimeType'], $apiKey);

        if (!$result || (!isset($result['liters']) && !isset($result['total_cost']))) {
            return response()->json([
                'success' => false,
                'fallbackToLocal' => true,
                'message' => 'Gemini vision could not read fuel details. Fallback to on-device ML Kit OCR.',
            ], 422);
        }

        $liters = isset($result['liters']) ? (float) $result['liters'] : 0.0;
        $cost = isset($result['total_cost']) ? (float) $result['total_cost'] : 0.0;
        $unitPrice = isset($result['unit_price']) ? (float) $result['unit_price'] : ($liters > 0 ? round($cost / $liters, 3) : 0.0);

        $rawType = strtolower((string) ($result['fuel_type'] ?? 'diesel'));
        $fuelType = (str_contains($rawType, 'petrol') || str_contains($rawType, 'gasoline') || str_contains($rawType, 'unleaded')) ? 'Petrol' : 'Diesel';

        return response()->json([
            'success' => true,
            'scan_method' => 'GEMINI_AI',
            'method_label' => 'Google Gemini 3.5 Flash-Lite Vision',
            'data' => [
                'liters' => $liters,
                'cost' => $cost,
                'unit_price' => $unitPrice,
                'station' => (string) ($result['station_name'] ?? 'Depot Fuel Station'),
                'fuel_type' => $fuelType,
                'odometer' => isset($result['odometer']) ? (int) $result['odometer'] : null,
                'receipt_number' => (string) ($result['receipt_number'] ?? ''),
                'date' => (string) ($result['date'] ?? ''),
                'confidence' => (float) ($result['confidence'] ?? 98.5),
                'raw_text' => (string) ($result['raw_text'] ?? "{$liters}L @ {$unitPrice} = \${$cost}"),
                'extracted_locally' => false,
            ],
        ]);
    }

    /**
     * Unified OCR extract endpoint with automatic mode detection.
     *
     * POST /api/ocr/extract
     */
    public function extract(Request $request): JsonResponse
    {
        $type = strtolower((string) $request->input('type', ''));

        if (in_array($type, ['odometer', 'meter', 'speedometer', 'mileage'], true)) {
            return $this->extractOdometer($request);
        }

        if (in_array($type, ['fuel', 'pump', 'receipt', 'petrol', 'diesel'], true)) {
            return $this->extractFuel($request);
        }

        // Auto-detect based on image content
        $fileInfo = $this->resolveImagePayload($request);
        if (!$fileInfo) {
            return response()->json([
                'success' => false,
                'fallbackToLocal' => true,
                'message' => 'No valid document or image provided.',
            ], 400);
        }

        $apiKey = $this->getGeminiApiKey($request);
        if (!$apiKey) {
            return response()->json([
                'success' => false,
                'fallbackToLocal' => true,
                'message' => 'GEMINI_API_KEY is not configured on the server.',
            ], 200);
        }

        $classifierPrompt = <<<PROMPT
Inspect this fleet management image.
Is this:
A) "odometer" (vehicle dashboard, speedometer dial, mechanical rolling drum counter, or digital mileage display)
B) "fuel" (fuel station pump screen, dispenser meter, or printed fuel receipt)
C) "other"

Return ONLY a JSON object: {"category": "odometer" | "fuel" | "other"}
PROMPT;

        $classification = $this->callGeminiVision($classifierPrompt, $fileInfo['base64'], $fileInfo['mimeType'], $apiKey);
        $cat = strtolower((string) ($classification['category'] ?? ''));

        if ($cat === 'odometer') {
            return $this->extractOdometer($request);
        }

        return $this->extractFuel($request);
    }

    /**
     * Call Gemini Vision API using candidate flash-lite models.
     */
    protected function callGeminiVision(string $prompt, string $base64Data, string $mimeType, string $apiKey): ?array
    {
        foreach ($this->candidateModels as $model) {
            try {
                $endpoint = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key=" . trim($apiKey);

                $response = Http::timeout(20)->withHeaders([
                    'Content-Type' => 'application/json',
                ])->post($endpoint, [
                    'contents' => [
                        [
                            'parts' => [
                                ['text' => $prompt],
                                [
                                    'inline_data' => [
                                        'mime_type' => $mimeType,
                                        'data' => $base64Data,
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'generationConfig' => [
                        'response_mime_type' => 'application/json',
                    ],
                ]);

                if ($response->successful()) {
                    $json = $response->json();
                    $text = $json['candidates'][0]['content']['parts'][0]['text'] ?? null;
                    if ($text) {
                        $cleaned = trim(preg_replace('/^```(?:json)?\s*/i', '', $text));
                        $cleaned = trim(preg_replace('/\s*```$/', '', $cleaned));
                        $parsed = json_decode($cleaned, true);
                        if (is_array($parsed)) {
                            return $parsed;
                        }
                    }
                } else {
                    Log::warning("[Gemini OCR] Model {$model} returned status " . $response->status() . ": " . $response->body());
                }
            } catch (Exception $e) {
                Log::warning("[Gemini OCR] Model {$model} error: " . $e->getMessage());
            }
        }

        return null;
    }

    /**
     * Resolves an uploaded file or base64 image payload from the request.
     */
    protected function resolveImagePayload(Request $request): ?array
    {
        $file = $request->file('image')
            ?? $request->file('meter_image')
            ?? $request->file('fuel_image')
            ?? $request->file('receipt_image')
            ?? $request->file('file')
            ?? $request->file('photo');

        if ($file && $file->isValid()) {
            $realPath = $file->getRealPath();
            $mimeType = $file->getMimeType() ?: 'image/jpeg';
            $rawContent = file_get_contents($realPath);
            $base64 = base64_encode($rawContent);

            return [
                'base64' => $base64,
                'mimeType' => $mimeType,
            ];
        }

        // Support base64 image strings directly
        $base64Input = $request->input('base64') ?? $request->input('image_base64');
        if ($base64Input && is_string($base64Input)) {
            $cleanBase64 = preg_replace('/^data:image\/[a-zA-Z]+;base64,/', '', $base64Input);
            return [
                'base64' => $cleanBase64,
                'mimeType' => 'image/jpeg',
            ];
        }

        return null;
    }

    /**
     * Resolves the server or header Gemini API Key.
     */
    protected function getGeminiApiKey(Request $request): ?string
    {
        $key = env('GEMINI_API_KEY')
            ?: $request->header('x-gemini-api-key')
            ?: config('services.gemini.key');

        return $key && trim($key) !== '' ? trim($key) : null;
    }
}
