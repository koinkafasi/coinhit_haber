<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\OmniRoute\OmniRouteService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class OmniRouteController extends Controller
{
    protected $omniRoute;

    public function __construct(OmniRouteService $omniRoute)
    {
        $this->omniRoute = $omniRoute;
    }

    /**
     * Chat API endpoint
     */
    public function chat(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'messages' => 'required|array',
            'model' => 'nullable|string',
            'temperature' => 'nullable|numeric|min:0|max:2',
            'max_tokens' => 'nullable|integer|min:1|max:10000',
            'stream' => 'nullable|boolean'
        ]);

        $result = $this->omniRoute->chat($validated['messages'], [
            'model' => $validated['model'] ?? null,
            'temperature' => $validated['temperature'] ?? 0.7,
            'max_tokens' => $validated['max_tokens'] ?? 1000,
            'stream' => $validated['stream'] ?? false
        ]);

        return response()->json($result);
    }

    /**
     * Kombinasyon listesi
     */
    public function combos(): JsonResponse
    {
        $combos = $this->omniRoute->getAvailableCombos();
        $configs = [];

        foreach ($combos as $combo) {
            $configs[$combo] = $this->omniRoute->getComboConfig($combo);
        }

        return response()->json([
            'available_combos' => $combos,
            'configs' => $configs
        ]);
    }
}
