{{-- Karşılaştırmalı Fon Getirileri tablosu — spec §8 --}}
@php
    $columns = \App\Models\Fund::returnColumns();
    $numClass = fn (float $v): string => $v > 0 ? 'text-green-700' : ($v < 0 ? 'text-red-700' : 'text-gray-400');
@endphp
<div class="table-scroll">
    <table class="market-table fund-returns-table w-full text-center text-sm bg-white">
        <thead>
            <tr class="border-b">
                <th class="p-4 text-left sticky left-0 bg-white">{{ __('media.code') }}</th>
                <th class="p-4 text-left">{{ __('media.funds') }}</th>
                <th class="p-4">{{ __('media.price') }}</th>
                @foreach ($columns as $column)
                    <th class="p-4">{{ $column['label'] }}</th>
                @endforeach
                <th class="p-4">{{ __('media.risk_value') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($rows as $row)
                <tr class="border-b text-sm">
                    <td class="py-4 px-4 text-left sticky left-0 bg-white font-bold">
                        <a href="{{ $row['url'] }}">{{ $row['code'] }}</a>
                    </td>
                    <td class="py-4 px-4 text-left">
                        <a href="{{ $row['url'] }}">{{ $row['name'] }}</a>
                    </td>
                    <td class="py-4 px-4">{{ $row['price_formatted'] }}</td>
                    @foreach ($columns as $column)
                        @php($value = (float) ($row['returns'][$column['field']] ?? 0))
                        <td class="py-4 px-4 {{ $numClass($value) }}">{{ number_format($value, 2, ',', '.') }}%</td>
                    @endforeach
                    <td class="py-4 px-4">{{ $row['risk_value'] }} / 7</td>
                </tr>
            @empty
                <tr><td colspan="{{ count($columns) + 4 }}" class="py-4 text-gray-400">{{ __('ui.no_data') }}</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="fund-returns-footer">
    <span class="fund-returns-count">{{ __('media.total_funds', ['count' => number_format($totalFundCount, 0, ',', '.')]) }}</span>
    @if ($showAll)
        <a class="fund-returns-all" href="{{ \App\Support\Locale::url('/yatirim-fonlari#fon-getirileri') }}">{{ __('media.show_less') }}</a>
    @else
        <a class="fund-returns-all" href="{{ \App\Support\Locale::url('/yatirim-fonlari?tumu=1#fon-getirileri') }}">{{ __('media.show_all') }} &raquo;</a>
    @endif
</div>