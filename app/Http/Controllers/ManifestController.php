<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;

/**
 * PWA manifest (master spec §3, §11.5).
 *
 * Site service worker KULLANMAZ (birebir not); yalnızca manifest sunulur.
 */
class ManifestController extends Controller
{
    public function show(): JsonResponse
    {
        return response()->json([
            'name' => 'Bloomberg HT',
            'short_name' => 'Bloomberg HT',
            'description' => 'Türkiye\'nin ekonomi ve finans platformu',
            'lang' => 'tr',
            'start_url' => '/',
            'scope' => '/',
            'display' => 'browser',
            'orientation' => 'any',
            'background_color' => '#ffffff',
            'theme_color' => '#384552',
            'icons' => [
                [
                    'src' => url('/img/manifest-192.png'),
                    'sizes' => '192x192',
                    'type' => 'image/png',
                    'purpose' => 'any maskable',
                ],
                [
                    'src' => url('/img/manifest-512.png'),
                    'sizes' => '512x512',
                    'type' => 'image/png',
                    'purpose' => 'any maskable',
                ],
            ],
        ], 200, ['Content-Type' => 'application/manifest+json'], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
            ->header('Cache-Control', 'public, max-age=86400');
    }
}
