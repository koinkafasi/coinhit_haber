{{-- Canlı pano güncelleme damgası (Bölüm 5) --}}
<p class="market-updated text-xs" data-market-poll="{{ config('market.poll_seconds_stock', 3) }}">
    Güncelleme: <span data-market-updated>{{ app(\App\Services\Market\MarketDataService::class)->lastUpdated() }}</span>
</p>