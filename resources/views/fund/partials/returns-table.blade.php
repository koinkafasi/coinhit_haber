{{-- Karşılaştırmalı Fon Getirileri tablosu — referans birebir:
     gri zemin bandı + tüm fonlar (Kod|Fonlar|Fiyat|dönemsel|Risk Değeri, noktalı
     ham değerler, % işareti yok) + "Tümünü Görüntüle" → /yatirim-fonlari/fon-karsilastirma. --}}
@props([
    'rows' => [],
])

@php
    $columns = \App\Models\Fund::returnColumns();
    $numClass = fn (float $v): string => $v > 0 ? 'text-green-700' : ($v < 0 ? 'text-red-700' : 'text-gray-400');
    // Referans birebir: ham noktalı değerler (35.2088, 0.705878) — % işareti yok
    $rawPrice = fn (float $v): string => rtrim(rtrim(number_format($v, 8, '.', ''), '0'), '.');
    $rawPct = fn (float $v): string => rtrim(rtrim(number_format($v, 6, '.', ''), '0'), '.');
@endphp

<div class="relative bg-white">
    <div class="absolute top-0 left-0 right-0 h-full bg-cover bg-center bg-no-repeat -z-100 before:absolute before:top-0 before:left-0 before:w-full before:h-full before:pointer-events-none bg-gray-100"></div>
    <div class="bg-gray-200 text-center text-base py-2 font-semibold">{{ __('media.comparative_returns_pct') }}</div>
    <div class="overflow-x-auto border mb-4 relative">
        <table data-type="table-type1" class="w-full text-center text-sm bg-white">
            <thead>
                <tr class="border-b text-sm">
                    <th class="text-left py-4 px-8 bg-white">{{ __('media.code') }}</th>
                    <th class="min-w-28 bg-white text-left">{{ __('media.funds') }}</th>
                    <th class="min-w-28 bg-white text-left">{{ __('media.price') }}</th>
                    @foreach ($columns as $column)
                        <th class="min-w-28 bg-white text-left">{{ $column['label'] }}</th>
                    @endforeach
                    <th class="min-w-28 bg-white text-left">{{ __('media.risk_value') }}</th>
                </tr>
            </thead>
            <tbody class="text-left">
                @forelse ($rows as $row)
                    <tr class="border-b text-sm{{ $loop->index % 2 === 1 ? ' bg-gray-100' : '' }}">
                        <td class="py-4"><span class="px-8 whitespace-nowrap text-center"><a class="font-bold" href="{{ $row['url'] }}">{{ $row['code'] }}</a></span></td>
                        <td class="py-4"><span class="pr-8 whitespace-nowrap text-center"><a href="{{ $row['url'] }}">{{ $row['name'] }}</a></span></td>
                        <td class="py-4"><span class="pr-8 whitespace-nowrap text-center">{{ $rawPrice((float) $row['price']) }}</span></td>
                        @foreach ($columns as $column)
                            @php($value = (float) ($row['returns'][$column['field']] ?? 0))
                            <td class="py-4"><span class="pr-8 whitespace-nowrap text-center {{ $numClass($value) }}">{{ $rawPct($value) }}</span></td>
                        @endforeach
                        <td class="py-4"><span class="pr-8 whitespace-nowrap text-center">{{ $row['risk_value'] }}</span></td>
                    </tr>
                @empty
                    <tr><td colspan="{{ count($columns) + 4 }}" class="py-4 text-gray-400">{{ __('ui.no_data') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <a href="{{ \App\Support\Locale::url('/yatirim-fonlari/fon-karsilastirma') }}"
       title="{{ __('media.show_all') }}"
       class="md:absolute md:bottom-5 md:right-4 text-sm font-bold flex justify-center gap-2 items-center">
        <span>{{ __('media.show_all') }}</span>
        <span class="bloomberght-icon-font-chevron-right text-xs"></span>
    </a>
</div>
