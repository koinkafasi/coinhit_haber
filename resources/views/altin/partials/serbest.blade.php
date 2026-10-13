{{-- Serbest Piyasada Altın tablosu — 17 satır (ALIŞ | SATIŞ | % | SAAT), spec §5/§8;
     referans `economy-table-type3` birebir. --}}
@php
    $title = $title ?? __('market.free_market_gold');
    $href = $href ?? \App\Support\Locale::url('/altin');
    $allLabel = $allLabel ?? null;
    $columns = [
        ['label' => __('market.bid'), 'field' => 'bid', 'color' => true],
        ['label' => __('market.ask'), 'field' => 'ask', 'color' => true],
        ['label' => '%', 'field' => 'change_percent', 'color' => true, 'pct' => true],
        ['label' => __('market.time'), 'field' => 'time'],
    ];
@endphp

@include('altin.partials.economy-table', [
    'title' => $title,
    'firstLabel' => __('market.free_market_gold'),
    'columns' => $columns,
    'quotes' => $quotes,
    'href' => $href,
    'allLabel' => $allLabel,
    'id' => 'serbest-piyasada-altin',
    'rowClass' => 'gold-row',
])
