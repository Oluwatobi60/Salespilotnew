<?php

namespace App\Services;

use App\Models\AppSetting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiService
{
    /**
     * Get the Gemini API Key from settings or environment
     */
    protected static function getApiKey(): ?string
    {
        // Try database setting first
        $key = AppSetting::get('gemini_api_key');
        
        if (empty($key)) {
            // Fallback to env
            $key = env('GEMINI_API_KEY');
        }
        
        return $key ?: null;
    }

    /**
     * Send a prompt to the Gemini API and return the response text
     *
     * @param string $prompt
     * @return string
     * @throws \Exception
     */
    public static function generateText(string $prompt): string
    {
        $apiKey = self::getApiKey();
        
        if (!$apiKey) {
            throw new \Exception('Gemini API key is not configured. Please set it in Superadmin Settings or your .env file.');
        }

        try {
            $response = Http::retry(2, 500)->withHeaders([
                'Content-Type' => 'application/json',
            ])->post('https://generativelanguage.googleapis.com/v1beta/models/gemini-3.5-flash-lite:generateContent?key=' . $apiKey, [
                'contents' => [
                    [
                        'parts' => [
                            [
                                'text' => $prompt
                            ]
                        ]
                    ]
                ]
            ]);

            if ($response->failed()) {
                Log::error('Gemini API Error', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                throw new \Exception('Failed to communicate with Gemini API: ' . $response->reason());
            }

            $data = $response->json();
            
            // Extract the generated text from candidates
            $text = $data['candidates'][0]['content']['parts'][0]['text'] ?? null;

            if ($text === null) {
                Log::error('Gemini Invalid Response Structure', ['response' => $data]);
                throw new \Exception('Invalid response structure returned by Gemini API.');
            }

            return trim($text);

        } catch (\Exception $e) {
            Log::error('Exception in GeminiService: ' . $e->getMessage());
            throw $e;
        }
    }
}
