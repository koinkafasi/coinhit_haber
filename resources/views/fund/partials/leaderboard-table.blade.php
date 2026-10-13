{{-- Fon sınıfı lider tablosu — referans birebir:
     sticky gri başlık bandı + Kod|Ad|Fon Fiyatı (TL)|Değişim (TL) %|Değişim (USD) %,
     satır zebra (bg-gray-100), px-8 hücreler, sol hizalı. --}}
@props([
    'title' => '',
    'rows' => [],
])

@php
    $dirClass = fn (array $row): string => $row['direction'] === 'up' ? 'text-green-700' : ($row['direction'] === 'down' ? 'text-red-700' : 'text-gray-400');
    // Referans birebir: ham noktalı değerler (3.81832, 1.53) — % işareti yok
    $rawPrice = fn (float $v): string => rtrim(rtrim(number_format($v, 8, '.', ''), '0'), '.');
    $rawPct = fn (float $v): string => number_format($v, 2, '.', '');
@endphp

<div class="overflow-x-auto border mb-4 relative">
    <div class="bg-gray-200 text-center text-base py-2 sticky left-0 top-0 font-semibold">{{ $title }}</div>
    <table data-type="table-type1" class="w-full text-center text-sm bg-white">
        <thead>
            <tr class="border-b text-sm">
                <th class="text-left py-4 px-8 bg-white">{{ __('media.code') }}</th>
                <th class="min-w-28 bg-white text-left">{{ __('media.name') }}</th>
                <th class="min-w-28 bg-white text-left">{{ __('media.fund_price_tl') }}</th>
                <th class="min-w-28 bg-white text-left">{{ __('media.change_tl_pct') }}</th>
                <th class="min-w-28 bg-white text-left">{{ __('media.change_usd_pct') }}</th>
            </tr>
        </thead>
        <tbody class="text-left">
            @forelse ($rows as $row)
                <tr class="border-b text-sm{{ $loop->index % 2 === 1 ? ' bg-gray-100' : '' }}">
                    <td class="py-4"><span class="px-8 whitespace-nowrap text-center"><a class="font-bold" href="{{ $row['url'] }}">{{ $row['code'] }}</a></span></td>
                    <td class="py-4"><span class="pr-8 whitespace-nowrap text-center"><a href="{{ $row['url'] }}">{{ $row['name'] }}</a></span></td>
                    <td class="py-4"><span class="pr-8 whitespace-nowrap text-center">{{ $rawPrice((float) $row['price']) }}</span></td>
                    <td class="py-4"><span class="pr-8 whitespace-nowrap text-center {{ $dirClass($row) }}">{{ $rawPct((float) $row['change_tl']) }}</span></td>
                    <td class="py-4"><span class="pr-8 whitespace-nowrap text-center {{ $dirClass($row) }}">{{ $rawPct((float) $row['change_usd']) }}</span></td>
                </tr>
            @empty
                <tr><td colspan="5" class="py-4 text-gray-400">{{ __('ui.no_data') }}</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
