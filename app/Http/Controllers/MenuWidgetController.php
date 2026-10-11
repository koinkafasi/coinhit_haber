<?php

namespace App\Http\Controllers;

use App\Support\Locale;
use Illuminate\Http\JsonResponse;

/**
 * Mega navigasyon widget ucu (Bölüm 2.2) — `/menu-widget`.
 *
 * config/navigation.php ağacını aktif locale'e göre (etiket çevirisi +
 * locale önekli URL) JSON olarak döndürür. Canlı sitedeki
 * `menu-widget` isteğinin klon karşılığıdır.
 */
class MenuWidgetController extends Controller
{
    public function index(): JsonResponse
    {
        $items = [];

        foreach ((array) config('navigation.mega', []) as $item) {
            $items[] = $this->formatItem($item, 0);
        }

        return response()->json([
            'locale' => Locale::current(),
            'data' => $items,
        ]);
    }

    /**
     * @param  array<string, mixed>  $item
     * @return array<string, mixed>
     */
    private function formatItem(array $item, int $depth): array
    {
        $children = [];
        foreach ((array) ($item['children'] ?? []) as $child) {
            $children[] = $this->formatItem($child, $depth + 1);
        }

        return [
            'label' => Locale::navLabel((string) $item['label']),
            'url' => Locale::navUrl((string) $item['url']),
            'external' => (bool) ($item['external'] ?? false),
            'level' => $depth,
            'children' => $children,
        ];
    }
}