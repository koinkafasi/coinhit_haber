{{-- Fon Sınıflarında Bugün lider tablosu — spec §8: Kod|Ad|Fon Fiyatı (TL)|Değişim (TL) %|Değişim (USD) % --}}
@php($dirClass = fn (array $row): string => $row['direction'] === 'up' ? 'text-green-700' : ($row['direction'] === 'down' ? 'text-red-700' : 'text-gray-400'))
<div class="table-scroll">
    <table class="market-table fund-leader-table w-full text-center text-sm bg-white">
        <thead>
            <tr class="border-b">
                <th class="p-4 text-left">Kod</th>
                <th class="p-4 text-left">Ad</th>
                <th class="p-4">Fon Fiyatı (TL)</th>
                <th class="p-4">Değişim (TL) %</th>
                <th class="p-4">Değişim (USD) %</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($rows as $row)
                <tr class="border-b text-sm fund-row-{{ $row['direction'] }}">
                    <td class="py-4 px-4 text-left font-bold">
                        <a href="{{ $row['url'] }}">{{ $row['code'] }}</a>
                    </td>
                    <td class="py-4 px-4 text-left">
                        <a href="{{ $row['url'] }}">{{ $row['name'] }}</a>
                    </td>
                    <td class="py-4 px-4">{{ $row['price_formatted'] }}</td>
                    <td class="py-4 px-4 {{ $dirClass($row) }}">{{ $row['change_tl_formatted'] }}</td>
                    <td class="py-4 px-4 {{ $dirClass($row) }}">{{ $row['change_usd_formatted'] }}</td>
                </tr>
            @empty
                <tr><td colspan="5" class="py-4 text-gray-400">{{ __('ui.no_data') }}</td></tr>
            @endforelse
        </tbody>
    </table>
</div>