<?php

namespace App\Services\OmniRoute;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OmniRouteService
{
    protected $baseUrl;
    protected $apiKey;
    protected $defaultCombo;

    public function __construct()
    {
        $this->baseUrl = config('omniroute.base_url', env('OMNIR_ROUTE_BASE_URL', 'http://localhost:20128/v1'));
        $this->apiKey = config('omniroute.api_key', env('OMNIR_ROUTE_API_KEY', ''));
        $this->defaultCombo = config('omniroute.default_combo', env('OMNIR_ROUTE_COMBOS', 'best-effort'));
    }

    public function chat($messages, $options = [])
    {
        $payload = [
            'model' => $options['model'] ?? 'auto/' . $this->defaultCombo,
            'messages' => $messages,
            'stream' => $options['stream'] ?? false,
        ];

        if (isset($options['temperature'])) {
            $payload['temperature'] = $options['temperature'];
        }

        if (isset($options['max_tokens'])) {
            $payload['max_tokens'] = $options['max_tokens'];
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '/chat/completions', $payload);

            return $response->json();
        } catch (\Exception $e) {
            Log::error('OmniRoute API error: ' . $e->getMessage());
            return ['error' => $e->getMessage()];
        }
    }

    public function getAvailableCombos()
    {
        return explode(',', env('OMNIR_ROUTE_COMBOS', 'best-effort,cost-effective,fast-response'));
    }

    public function getComboConfig($comboName)
    {
        $configs = [
            'best-effort' => [
                'strategy' => 'auto',
                'providers' => ['openai', 'anthropic', 'gemini'],
                'fallback' => true,
                'cache' => true
            ],
            'cost-effective' => [
                'strategy' => 'auto',
                'providers' => ['gemini', 'openai', 'anthropic'],
                'fallback' => true,
                'cache' => true,
                'budget' => ['maxCost' => 0.05, 'currency' => 'USD']
            ],
            'fast-response' => [
                'strategy' => 'auto',
                'providers' => ['gemini', 'openai'],
                'fallback' => false,
                'cache' => true
            ]
        ];

        return $configs[$comboName] ?? null;
    }
}
