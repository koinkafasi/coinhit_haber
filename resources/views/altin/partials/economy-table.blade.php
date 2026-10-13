{{-- Ekonomi tablosu (referans `economy-table-type3` birebir):
     head-type1 (h2 link) + sticky ilk kolon + sağ hizalı ALIŞ/SATIŞ/% (+SAAT) + ok ikonları.
     Props: title, firstLabel, columns[{label,field,color,pct}], quotes, href, allLabel,
            urlResolver, id, rowClass. --}}
@props([
    'title',
    'firstLabel',
    'columns',
    'quotes',
    'href' => null,
    'allLabel' => null,
    'urlResolver' => null,
    'id' => null,
    'rowClass' => '',
])

@php
    $dirClass = fn (string $dir): string => $dir === 'up' ? 'text-green-700' : ($dir === 'down' ? 'text-red-700' : 'text-gray-400');
    $iconCls = fn (string $dir): string => $dir === 'flat' ? '' : 'bloomberght-icon-font-icon-graphic-'.$dir;
    $valueOf = fn ($quote, string $field): string => match ($field) {
        'bid' => $quote->formattedBid(),
        'ask' => $quote->formattedAsk(),
        'last' => $quote->formattedLast(),
        'change_percent' => $quote->formattedChangePercent(),
        'time' => $quote->time(),
        default => $quote->format((float) ($quote->last ?? 0)),
    };
    $valueCls = fn (array $c): string => ! empty($c['pct']) ? 'percentChange' : 'lastPrice';
@endphp

<div data-widget-type="economy-table-type3" class="mb-4 md:mb-12 relative overflow-hidden" @if ($id) id="{{ $id }}" @endif>
    <div data-type="head-type1">
        <div class="relative before:absolute before:top-0 before:m-auto before:w-[230px] before:h-2.5 before:bg-black before:bottom-0 before:h-2.5 border-b border-black mb-4"></div>
        @if ($href)
            <a href="{{ $href }}" title="{{ $title }}" class="inline-block mb-3">
                <h2 class="font-unna font-bold text-3xl">{{ $title }}</h2>
            </a>
            @if ($allLabel)
                <a href="{{ $href }}" class="section-head-all inline-block mb-3 ml-3 text-sm font-bold">{{ $allLabel }} &raquo;</a>
            @endif
        @else
            <div class="mb-3">
                <h2 class="font-unna font-bold text-3xl">{{ $title }}</h2>
            </div>
        @endif
    </div>

    <div class="border md:h-[calc(100%-75px)] overflow-x-auto">
        <table data-type="table-type1" class="w-full text-center text-sm bg-white">
            <thead>
                <tr class="border-b text-sm">
                    <th class="sticky left-0 p-4 text-left bg-white">{{ $firstLabel }}</th>
                    @foreach ($columns as $column)
                        <th class="p-4 text-right min-w-20 2xl:min-w-24">{{ $column['label'] }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody data-connection="true">
                @forelse ($quotes as $quote)
                    @php
                        $dir = $quote->direction();
                        $dcls = $dirClass($dir);
                        $icon = $iconCls($dir);
                        $name = $quote->instrument->name;
                    @endphp
                    <tr class="border-b text-sm security-{{ $quote->instrument->slug }}{{ $rowClass ? ' '.$rowClass : '' }}">
                        <td class="py-4 sticky left-0 w-full px-4 bg-white">
                            <a href="{{ $urlResolver ? $urlResolver($quote) : $quote->instrument->url }}"
                               title="{{ $quote->instrument->name }}"
                               class="flex items-center gap-2 text-left">
                                <div class="setTextColor {{ $dcls }} text-xs{{ $icon ? ' '.$icon : '' }}"></div>
                                <div>
                                    <div class="font-bold">{{ $name }}</div>
                                    <div class="text-ellipses text-xs"></div>
                                </div>
                            </a>
                        </td>
                        @foreach ($columns as $column)
                            @php
                                $isColor = ! empty($column['color']);
                                $colDcls = $isColor ? $dcls : '';
                            @endphp
                            <td class="py-4 px-4 bg-white">
                                <div class="text-right flex justify-end whitespace-pre{{ $isColor ? ' gap-1' : '' }}">
                                    <span class="{{ $valueCls($column) }}{{ $isColor ? ' setTextColor '.$colDcls : '' }}">{{ $valueOf($quote, $column['field']) }}</span>
                                    @if ($isColor && $icon)
                                        <span class="text-xs setIcon setTextColor {{ $colDcls }} {{ $icon }}"></span>
                                    @endif
                                </div>
                            </td>
                        @endforeach
                    </tr>
                @empty
                    <tr><td colspan="{{ count($columns) + 1 }}" class="py-4 text-gray-400">{{ __('ui.no_data') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="border sticky bottom-0 left-0 bg-white"></div>
</div>
