@props([
    'title',
    'quotes',
    'href' => null,
    'allLabel' => null,
    'columns' => null,
    'id' => null,
    'head' => true,
    'firstLabel' => null,
    'firstField' => 'name',
    'urlResolver' => null,
    'sectionClass' => null,
])

@php
    $allLabel = $allLabel ?? __('ui.all_see');
    $columns = $columns ?? [
        ['label' => __('market.last'), 'field' => 'last', 'class' => 'lastPrice'],
        ['label' => '%', 'field' => 'change_percent', 'class' => 'percentChange', 'color' => true],
    ];
    $firstLabel = $firstLabel ?? $title;
@endphp

<section class="market-data-block {{ $sectionClass }}" @if ($id) id="{{ $id }}" @endif>
    @if ($head)
        <x-section-head :title="$title" :href="$href" :allLabel="$allLabel" />
    @endif
    <div class="table-scroll">
        <table data-type="table-type1" class="market-table w-full text-center text-sm bg-white">
            <thead>
                <tr class="border-b">
                    <th class="sticky left-0 p-4 text-left bg-white">{{ $firstLabel }}</th>
                    @foreach ($columns as $column)
                        <th class="p-4">{{ $column['label'] }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody data-connection="true">
                @forelse ($quotes as $quote)
                    <tr class="border-b text-sm security-{{ $quote->instrument->slug }} {{ $quote->direction() === 'up' ? 'is-up' : ($quote->direction() === 'down' ? 'is-down' : 'is-flat') }}"
                        data-market-code="{{ $quote->code() }}">
                        <td class="py-4 sticky left-0">
                            <a href="{{ $urlResolver ? $urlResolver($quote) : $quote->instrument->url }}" @if ($firstField === 'code') title="{{ $quote->instrument->name }}" @endif>
                                @if ($firstField === 'code')
                                    <span class="stock-code">{{ $quote->code() }}</span>
                                @else
                                    {{ $quote->instrument->name }}
                                @endif
                            </a>
                        </td>
                        @foreach ($columns as $column)
                            @php
                                $value = match ($column['field']) {
                                    'last' => $quote->formattedLast(),
                                    'change_percent' => $quote->formattedChangePercent(),
                                    'monthly_percent' => $quote->format($quote->monthlyPercent, 2).'%',
                                    'yearly_percent' => $quote->format($quote->yearlyPercent, 2).'%',
                                    'bid' => $quote->formattedBid(),
                                    'ask' => $quote->formattedAsk(),
                                    'change' => $quote->formattedChange(),
                                    'previous_close' => $quote->format($quote->previousClose),
                                    'high' => $quote->format($quote->high),
                                    'low' => $quote->format($quote->low),
                                    'open' => $quote->format($quote->open),
                                    'volume_lot' => $quote->formattedVolumeLot(),
                                    'volume_tl' => $quote->formattedVolumeTl(),
                                    'volume_24h' => $quote->formattedVolume24h(),
                                    'market_cap' => $quote->formattedMarketCap(),
                                    'change_7d' => $quote->format($quote->change7d, 2).'%',
                                    'time' => $quote->time(),
                                    default => $quote->format($quote->last),
                                };
                                $cls = $column['class'] ?? '';
                                if (! empty($column['color'])) {
                                    $cls .= $quote->direction() === 'up' ? ' text-green-700' : ($quote->direction() === 'down' ? ' text-red-700' : ' text-gray-400');
                                }
                            @endphp
                            <td class="py-4 px-4 {{ trim($cls) }}" data-market-field="{{ $column['field'] }}">{{ $value }}</td>
                        @endforeach
                    </tr>
                @empty
                    <tr><td colspan="{{ count($columns) + 1 }}" class="py-4 text-gray-400">{{ __('ui.no_data') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>