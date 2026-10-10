@extends('front.layouts.app')

@section('title', 'Dünya Borsaları | '.($siteSettings['site_name'] ?? 'Bloomberg HT'))
@section('description', 'Amerika, Avrupa ve Asya Pasifik borsa endeksleri ile vadeli kontratlar.')

@section('content')
    <div class="borsa-page">
        <h1 class="page-title font-unna font-bold">Dünya Borsaları</h1>

        @include('borsa.partials.quick-links')

        @php
            $columns = [
                ['label' => 'SON', 'field' => 'last', 'class' => 'lastPrice'],
                ['label' => '%', 'field' => 'change_percent', 'class' => 'percentChange', 'color' => true],
            ];
            $indexUrl = fn ($q) => $q->instrument->indexUrl();
        @endphp

        <div class="world-markets">
            @foreach ($tables as $table)
                <x-market-table :title="$table['title']" :firstLabel="$table['title']" :quotes="$table['quotes']"
                    :columns="$columns" :urlResolver="$indexUrl" :id="'world-'.$table['key']" />
            @endforeach
        </div>

        @if ($cmeNote)
            <p class="cme-notice text-xs">{!! $cmeNote !!}</p>
        @endif
    </div>
@endsection