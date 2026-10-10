@props([
    'title',
    'quotes',
    'href' => null,
    'allLabel' => 'TÜMÜNÜ GÖR',
    'columns' => null,
    'id' => null,
])

@php
    $columns = $columns ?? [
        ['label' => 'SON', 'field' => 'last', 'class' => 'lastPrice'],
        ['label' => '%', 'field' => 'change_percent', 'class' => 'percentChange', 'color' => true],
    ];
@endphp

<section class="market-data-block" @if ($id) id="{{ $id }}" @endif>
    <x-section-head :title="$title" :href="$href" :allLabel="$allLabel" />
    <div class="table-scroll">
        <table data-type="table-type1" class="market-table w-full text-center text-sm bg-white">
            <thead>
                <tr class="border-b">
                    <th class="sticky left-0 p-4 text-left bg-white">{{ $title }}</th>
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
                            <a href="{{ $quote->instrument->url }}">{{ $quote->instrument->name }}</a>
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
                    <tr><td colspan="{{ count($columns) + 1 }}" class="py-4 text-gray-400">Veri bulunmuyor.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>