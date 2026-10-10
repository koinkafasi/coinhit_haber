{{-- Kripto Para tablosu (spec §5, §8, §14.5): KRİPTO PARA | FİYAT($) | %(24s) | %(7g) | 24s Hacim($) | Piyasa Değ($) --}}
@php
    $dirClass = fn ($quote) => $quote->direction() === 'up' ? 'text-green-700' : ($quote->direction() === 'down' ? 'text-red-700' : 'text-gray-400');
    $change7dClass = fn ($quote) => $quote->change7d > 0 ? 'text-green-700' : ($quote->change7d < 0 ? 'text-red-700' : 'text-gray-400');
    $sign = fn (float $v) => $v > 0 ? '+' : '';
@endphp

<section class="market-data-block" id="kripto-para">
    <x-section-head title="Kripto Para" href="/kripto" />
    <div class="table-scroll">
        <table data-type="table-type1" class="market-table w-full text-center text-sm bg-white">
            <thead>
                <tr class="border-b">
                    <th class="sticky left-0 p-4 text-left bg-white">KRİPTO PARA</th>
                    <th class="p-4">FİYAT($)</th>
                    <th class="p-4">%(24s)</th>
                    <th class="p-4">%(7g)</th>
                    <th class="p-4">24s Hacim($)</th>
                    <th class="p-4">Piyasa Değ($)</th>
                </tr>
            </thead>
            <tbody data-connection="true">
                @forelse ($quotes as $quote)
                    @php($detailUrl = $quote->instrument->cryptoUrl())
                    <tr class="border-b text-sm security-{{ $quote->instrument->slug }} {{ $quote->direction() === 'up' ? 'is-up' : ($quote->direction() === 'down' ? 'is-down' : 'is-flat') }}"
                        data-market-code="{{ $quote->code() }}">
                        <td class="py-4 sticky left-0">
                            @if ($detailUrl)
                                <a href="{{ $detailUrl }}">{{ $quote->instrument->name }} <span class="stock-code">{{ $quote->code() }}</span></a>
                            @else
                                <span>{{ $quote->instrument->name }} <span class="stock-code">{{ $quote->code() }}</span></span>
                            @endif
                        </td>
                        <td class="py-4 px-4 lastPrice" data-market-field="last">{{ $quote->formattedLast() }}</td>
                        <td class="py-4 px-4 percentChange {{ $dirClass($quote) }}" data-market-field="change_percent">{{ $quote->formattedChangePercent() }}</td>
                        <td class="py-4 px-4 percentChange {{ $change7dClass($quote) }}" data-market-field="change_7d">{{ $sign($quote->change7d).$quote->format($quote->change7d, 2).'%' }}</td>
                        <td class="py-4 px-4" data-market-field="volume_24h">{{ $quote->formattedVolume24h() }}</td>
                        <td class="py-4 px-4" data-market-field="market_cap">{{ $quote->formattedMarketCap() }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="py-4 text-gray-400">{{ __('ui.no_data') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>