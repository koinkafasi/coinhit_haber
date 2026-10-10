{{-- KAP detay / kap-haberleri kenar çubuğu pariteleri (BIST100, USD/TRY, ..., BRENT) --}}
@php($marketService = app(\App\Services\Market\MarketDataService::class))
<div class="parities-sidebar" data-market-poll="{{ config('market.poll_seconds', 5) }}">
    <x-section-head title="Pariteler" href="/doviz/pariteler" />
    <table data-type="table-type1" class="market-table w-full text-center text-sm bg-white">
        <thead>
            <tr class="border-b">
                <th class="sticky left-0 p-4 text-left bg-white">PARİTE</th>
                <th class="p-4">SON</th>
                <th class="p-4">%</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($parities as $quote)
                <tr class="border-b text-sm security-{{ $quote->instrument->slug }} {{ $quote->direction() === 'up' ? 'is-up' : ($quote->direction() === 'down' ? 'is-down' : 'is-flat') }}"
                    data-market-code="{{ $quote->code() }}">
                    <td class="py-3 sticky left-0">
                        <a href="{{ $quote->instrument->url }}">{{ $quote->instrument->name }}</a>
                    </td>
                    <td class="py-3 px-4 lastPrice {{ $quote->direction() === 'up' ? 'text-green-700' : ($quote->direction() === 'down' ? 'text-red-700' : 'text-gray-400') }}"
                        data-market-field="last">{{ $quote->formattedLast() }}</td>
                    <td class="py-3 px-4 percentChange {{ $quote->direction() === 'up' ? 'text-green-700' : ($quote->direction() === 'down' ? 'text-red-700' : 'text-gray-400') }}"
                        data-market-field="change_percent">{{ $quote->formattedChangePercent() }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <p class="market-updated text-xs">Güncelleme: <span data-market-updated>{{ $marketService->lastUpdated() }}</span></p>
</div>