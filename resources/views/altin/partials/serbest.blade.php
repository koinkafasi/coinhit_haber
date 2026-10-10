{{-- Serbest Piyasada Altın tablosu — 17 satır (ALIŞ | SATIŞ | % | SAAT), spec §5/§8 --}}
@php
    $dirClass = fn ($quote) => $quote->direction() === 'up' ? 'text-green-700' : ($quote->direction() === 'down' ? 'text-red-700' : 'text-gray-400');
    $title = $title ?? __('market.free_market_gold');
    $href = $href ?? null;
    $allLabel = $allLabel ?? null;
@endphp

<section class="market-data-block" id="serbest-piyasada-altin">
    <x-section-head :title="$title" :href="$href" :allLabel="$allLabel" />
    <div class="table-scroll">
        <table data-type="table-type1" class="market-table w-full text-center text-sm bg-white">
            <thead>
                <tr class="border-b">
                    <th class="sticky left-0 p-4 text-left bg-white">{{ __('market.free_market_gold') }}</th>
                    <th class="p-4">{{ __('market.bid') }}</th>
                    <th class="p-4">{{ __('market.ask') }}</th>
                    <th class="p-4">%</th>
                    <th class="p-4">{{ __('market.time') }}</th>
                </tr>
            </thead>
            <tbody data-connection="true">
                @forelse ($quotes as $quote)
                    <tr class="gold-row border-b text-sm security-{{ $quote->instrument->slug }} {{ $quote->direction() === 'up' ? 'is-up' : ($quote->direction() === 'down' ? 'is-down' : 'is-flat') }}"
                        data-market-code="{{ $quote->code() }}">
                        <td class="py-4 sticky left-0">
                            <a href="{{ $quote->instrument->goldUrl() }}">{{ $quote->instrument->name }}</a>
                        </td>
                        <td class="py-4 px-4 lastPrice" data-market-field="bid">{{ $quote->formattedBid() }}</td>
                        <td class="py-4 px-4 lastPrice" data-market-field="ask">{{ $quote->formattedAsk() }}</td>
                        <td class="py-4 px-4 percentChange {{ $dirClass($quote) }}" data-market-field="change_percent">{{ $quote->formattedChangePercent() }}</td>
                        <td class="py-4 px-4 altin-time">{{ $quote->staleTime() }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="py-4 text-gray-400">{{ __('ui.no_data') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>