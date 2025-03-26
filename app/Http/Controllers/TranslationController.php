<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class TranslationController extends Controller
{
    public function translate(Request $request)
    {
        $request->validate([
            'html' => 'required|string',
            'targetLang' => 'required|string'
        ]);

        $authKey = env('DEEPL_API_KEY');

        if (!$authKey) {
            return response()->json(['error' => 'Missing DeepL API key'], 500);
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'DeepL-Auth-Key ' . $authKey
            ])->post('https://api-free.deepl.com/v2/translate', [
                'text' => [$request->input('html')],
                'target_lang' => $request->input('targetLang')
            ]);

            $data = $response->json();

            return response()->json(['translatedHtml' => $data['translations'][0]['text'] ?? '']);
        } catch (Exception $e) {
            return response()->json(['error' => 'Translation failed'], 500);
        }
    }
}
