<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Service;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class TranslationController extends Controller
{
    // Map country/language codes to DeepL-supported language codes
    private static $languageMap = [
        // Original mappings
        'EN' => 'EN', // English
        'FR' => 'FR', // French
        'DE' => 'DE', // German
        'ES' => 'ES', // Spanish
        'IT' => 'IT', // Italian
        
        // New mappings from the list
        'US' => 'EN', // United States -> English
        'NL' => 'NL', // Dutch -> Dutch
        'SE' => 'SV', // Swedish -> DeepL uses 'SV' for Swedish
        'JP' => 'JA', // Japan -> DeepL uses 'JA' for Japanese
        'CN' => 'ZH', // Chinese -> DeepL uses 'ZH' for Chinese
        'AR' => 'AR', // Arabic
        
        // Lowercase versions for case-insensitive matching
        'us' => 'EN',
        'fr' => 'FR',
        'nl' => 'NL',
        'de' => 'DE',
        'es' => 'ES',
        'se' => 'SV',
        'jp' => 'JA',
        'cn' => 'ZH',
        'ar' => 'AR',
    ];
    
    private static function getDeepLLanguageCode($countryCode)
    {
        // Try to find the code directly (case-sensitive)
        if (isset(self::$languageMap[$countryCode])) {
            return self::$languageMap[$countryCode];
        }
        
        // Try with uppercase (for case-insensitive matching)
        $upperCode = strtoupper($countryCode);
        return self::$languageMap[$upperCode] ?? 'EN'; // Default to English if not found
    }
    
    public static function translate($text, $targetLang)
    {
        $apiKey = config('services.deepl.api_key');
        
        if (!$apiKey) {
            Log::error('DeepL API key is missing');
            return $text;
        }

        // Check if text is empty
        if (empty($text)) {
            return $text;
        }
        
        // Make sure text is a string
        if (!is_string($text)) {
            return $text;
        }
        
        // Convert country code to DeepL language code
        $deepLLangCode = self::getDeepLLanguageCode($targetLang);
        
        // Generate a cache key based on text and target language
        $cacheKey = 'translation_' . md5($text . '_' . $deepLLangCode);
        
        // Check if translation exists in cache (with 1 week expiration)
        return Cache::remember($cacheKey, 604800, function () use ($text, $deepLLangCode, $apiKey) {
            try {
                $response = Http::withHeaders([
                    'Authorization' => 'DeepL-Auth-Key ' . $apiKey,
                    'Content-Type' => 'application/json',
                ])->post('https://api-free.deepl.com/v2/translate', [
                    'text' => [$text],
                    'target_lang' => $deepLLangCode,
                ]);

                if ($response->successful()) {
                    return $response->json()['translations'][0]['text'] ?? $text;
                } else {
                    Log::error('DeepL API error: ' . $response->body());
                    return $text;
                }
            } catch (\Exception $e) {
                Log::error('DeepL translation error: ' . $e->getMessage());
                return $text;
            }
        });
    }

    public static function translateJson($data, $targetLang)
    {
        Log::info('Translating JSON data');
        Log::info('Translating JSON data', ['targetLang' => $targetLang]);
        // Avoid translating non-strings or empty arrays
        if (empty($data)) {
            return $data;
        }
        
        if (is_array($data)) {
            // Extract all translatable strings from the array
            $stringsToTranslate = [];
            $stringPaths = [];
            
            self::extractStrings($data, $stringsToTranslate, $stringPaths);
            
            // Batch translate if we have strings
            if (!empty($stringsToTranslate)) {
                $translatedStrings = self::batchTranslate($stringsToTranslate, $targetLang);
                
                // Reinsert translated strings
                return self::reinsertStrings($data, $translatedStrings, $stringPaths);
            }
            
            return $data;
        } elseif (is_string($data) && !empty(trim($data))) {
            return self::translate($data, $targetLang);
        }
        
        return $data;
    }
    
    // Helper method to extract all strings from nested array
    private static function extractStrings($data, &$strings, &$paths, $path = [])
    {
        foreach ($data as $key => $value) {
            $currentPath = array_merge($path, [$key]);
            
            if (is_array($value)) {
                self::extractStrings($value, $strings, $paths, $currentPath);
            } elseif (is_string($value) && !empty(trim($value))) {
                $strings[] = $value;
                $paths[] = $currentPath;
            }
        }
    }
    
    // Helper method to translate multiple strings at once
    private static function batchTranslate($strings, $targetLang)
    {
        $apiKey = config('services.deepl.api_key');
        
        if (!$apiKey || empty($strings)) {
            return $strings;
        }
        
        $deepLLangCode = self::getDeepLLanguageCode($targetLang);
        
        // Check cache for each string
        $results = [];
        $uncachedStrings = [];
        $uncachedIndices = [];
        
        foreach ($strings as $index => $string) {
            $cacheKey = 'translation_' . md5($string . '_' . $deepLLangCode);
            
            if (Cache::has($cacheKey)) {
                $results[$index] = Cache::get($cacheKey);
            } else {
                $uncachedStrings[] = $string;
                $uncachedIndices[] = $index;
            }
        }
        
        // If we have uncached strings, translate them in batches
        if (!empty($uncachedStrings)) {
            // Split into batches of 50 (DeepL recommendation)
            $batches = array_chunk($uncachedStrings, 50);
            $batchIndices = array_chunk($uncachedIndices, 50);
            
            foreach ($batches as $batchNum => $batch) {
                try {
                    $response = Http::withHeaders([
                        'Authorization' => 'DeepL-Auth-Key ' . $apiKey,
                        'Content-Type' => 'application/json',
                    ])->post('https://api-free.deepl.com/v2/translate', [
                        'text' => $batch,
                        'target_lang' => $deepLLangCode,
                    ]);
                    
                    if ($response->successful()) {
                        $translations = $response->json()['translations'] ?? [];
                        
                        foreach ($translations as $i => $translation) {
                            $index = $batchIndices[$batchNum][$i];
                            $translatedText = $translation['text'] ?? $batch[$i];
                            $results[$index] = $translatedText;
                            
                            // Store in cache
                            $cacheKey = 'translation_' . md5($batch[$i] . '_' . $deepLLangCode);
                            Cache::put($cacheKey, $translatedText, 604800); // 1 week
                        }
                    } else {
                        Log::error('DeepL batch API error: ' . $response->body());
                        
                        // Use original strings for this batch
                        foreach ($batchIndices[$batchNum] as $i => $index) {
                            $results[$index] = $batch[$i];
                        }
                    }
                    
                    // Add a small delay between batch requests to respect rate limits
                    if (count($batches) > 1) {
                        usleep(100000); // 100ms
                    }
                    
                } catch (\Exception $e) {
                    Log::error('DeepL batch translation error: ' . $e->getMessage());
                    
                    // Use original strings for this batch
                    foreach ($batchIndices[$batchNum] as $i => $index) {
                        $results[$index] = $batch[$i];
                    }
                }
            }
        }
        
        return $results;
    }
    
    // Helper method to reinsert translated strings into original structure
    private static function reinsertStrings($data, $translatedStrings, $paths)
    {
        $result = $data;
        
        foreach ($paths as $i => $path) {
            self::setNestedValue($result, $path, $translatedStrings[$i]);
        }
        
        return $result;
    }
    
    // Helper to set value in nested array
    private static function setNestedValue(&$array, $path, $value)
    {
        $current = &$array;
        
        foreach ($path as $key) {
            if (!isset($current[$key])) {
                $current[$key] = [];
            }
            $current = &$current[$key];
        }
        
        $current = $value;
    }
}