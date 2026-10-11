<?php

namespace App\Services\Market;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * CoinGecko REST API istemcisi (CH-2).
 *
 * Ücretsiz/demo uçlar `x-cg-demo-api-key` başlığı ister; anahtar `.env`'den
 * okunur ve hiçbir yere loglanmaz. Ağ hatası durumunda sessizce boş döner —
 * site simülatör verisiyle ayakta kalır, gerçek veri bir "bonus"tur.
 */
class CoinGeckoClient
{
    private const BASE_URL = 'https://api.coingecko.com/api/v3';

    private const TIMEOUT_SECONDS = 12;

    /**
     * CoinGecko id → market_instruments kodu eşlemesi (seed'deki 20 kripto).
     *
     * @var array<string, string>
     */
    public const ID_MAP = [
        'bitcoin' => 'BTC',
        'ethereum' => 'ETH',
        'tether' => 'USDT',
        'binancecoin' => 'BNB',
        'ripple' => 'XRP',
        'usd-coin' => 'USDC',
        'solana' => 'SOL',
        'tron' => 'TRX',
        'dogecoin' => 'DOGE',
        'cardano' => 'ADA',
        'chainlink' => 'LINK',
        'monero' => 'XMR',
        'litecoin' => 'LTC',
        'avalanche-2' => 'AVAX',
        'polkadot' => 'DOT',
        'staked-ether' => 'STETH',
        'zcash' => 'ZEC',
        'hyperliquid' => 'HYPE',
        'usds' => 'USDS',
        'wrapped-steth' => 'WSTETH',
    ];

    /**
     * Tanılı enstrümanların güncel piyasa durumunu çeker.
     *
     * @return array<string, array<string, mixed>> CoinGecko id => /coins/markets satırı
     */
    public function markets(): array
    {
        $key = (string) config('market.coingecko.api_key');

        if ($key === '') {
            return [];
        }

        try {
            $response = Http::withHeaders(['x-cg-demo-api-key' => $key])
                ->timeout(self::TIMEOUT_SECONDS)
                ->retry(2, 500, throw: false)
                ->get(self::BASE_URL.'/coins/markets', [
                    'vs_currency' => 'usd',
                    'ids' => implode(',', array_keys(self::ID_MAP)),
                    'order' => 'market_cap_desc',
                    'per_page' => 50,
                    'page' => 1,
                    'sparkline' => 'true',
                    'price_change_percentage' => '24h,7d',
                ]);

            if (! $response->successful()) {
                Log::warning('coingecko: HTTP '.$response->status());

                return [];
            }

            $rows = $response->json();

            return is_array($rows) ? $rows : [];
        } catch (ConnectionException | RequestException $e) {
            Log::warning('coingecko: '.$e->getMessage());

            return [];
        } catch (Throwable $e) {
            Log::warning('coingecko: '.$e->getMessage());

            return [];
        }
    }
}
