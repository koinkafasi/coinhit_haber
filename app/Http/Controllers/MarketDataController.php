<?php

namespace App\Http\Controllers;

use App\Services\Market\MarketDataService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Piyasa veri polling ucu (Bölüm 5, 10, 14.1).
 *
 * `GET /piyasa/veri?groups=bist_index,currency&code=XU100`
 * `GET /finance-slider-data` (ana sayfa hero/ticker verisi)
 */
class MarketDataController extends Controller
{
    public function __construct(private readonly MarketDataService $market) {}

    public function snapshot(Request $request): JsonResponse
    {
        $groups = $this->parseGroups($request->query('groups'));
        $code = $request->query('code');

        if (is_string($code) && $code !== '') {
            $quote = $this->market->quote($code);

            return response()->json([
                'updated_at' => $this->market->lastUpdated(),
                'quote' => $quote->toArray(),
            ]);
        }

        return response()->json([
            'updated_at' => $this->market->lastUpdated(),
            'poll_seconds' => (int) config('market.poll_seconds', 5),
            'groups' => $this->market->snapshot($groups),
        ]);
    }

    /**
     * Finance slider / ticker seçkisi (BIST100, USD/TRY, EUR/TRY, ALTIN ONS, BRENT, BTC).
     */
    public function slider(): JsonResponse
    {
        $codes = ['XU100', 'USDTRY', 'EURTRY', 'GAU', 'BRENT', 'BTC'];

        $quotes = collect($codes)
            ->map(function (string $code) {
                $instrument = $this->market->all()->firstWhere('code', $code);

                return $instrument ? $this->market->quote($instrument)->toArray() : null;
            })
            ->filter()
            ->values()
            ->all();

        return response()->json([
            'updated_at' => $this->market->lastUpdated(),
            'poll_seconds' => (int) config('market.poll_seconds', 5),
            'quotes' => $quotes,
        ]);
    }

    /**
     * @return array<int, string>|null
     */
    private function parseGroups(mixed $value): ?array
    {
        if (! is_string($value) || trim($value) === '') {
            return null;
        }

        $groups = array_filter(array_map('trim', explode(',', $value)));
        $valid = array_keys($this->market->groups());

        $groups = array_values(array_intersect($groups, $valid));

        return $groups === [] ? null : $groups;
    }
}