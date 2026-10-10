@extends('front.layouts.app')

@section('title', __('market.market_close').' | '.($siteSettings['site_name'] ?? 'Bloomberg HT'))
@section('description', __('market.borsa_closing_desc'))

@section('content')
    <div class="borsa-page">
        <h1 class="page-title font-unna font-bold">{{ __('market.market_close') }}</h1>

        @include('borsa.partials.quick-links')

        @php
            $closingAction = $letter ? '/borsa/borsa-kapanis/'.$letter : '/borsa/borsa-kapanis';
            $columns = [
                ['label' => __('market.previous'), 'field' => 'previous_close', 'class' => 'previousClose'],
                ['label' => __('market.close'), 'field' => 'last', 'class' => 'lastPrice'],
                ['label' => __('market.high'), 'field' => 'high', 'class' => 'high'],
                ['label' => __('market.low'), 'field' => 'low', 'class' => 'low'],
                ['label' => __('market.change_percent'), 'field' => 'change_percent', 'class' => 'percentChange', 'color' => true],
                ['label' => __('market.volume_lot'), 'field' => 'volume_lot', 'class' => 'volumeLot'],
                ['label' => __('market.volume_tl'), 'field' => 'volume_tl', 'class' => 'volumeTl'],
            ];
        @endphp

        <form class="date-select" method="get" action="{{ \App\Support\Locale::url($closingAction) }}">
            <label for="tarih-gun">{{ __('market.select_date') }}</label>
            <div class="date-select-fields">
                <select name="gun" id="tarih-gun" aria-label="{{ __('market.day') }}" onchange="this.form.submit()">
                    <option value="">{{ __('market.day') }}</option>
                    @foreach ($days as $day)
                        <option value="{{ $day }}" @selected((string) $selected['gun'] === (string) $day)>{{ str_pad((string) $day, 2, '0', STR_PAD_LEFT) }}</option>
                    @endforeach
                </select>
                <select name="ay" aria-label="{{ __('market.month') }}" onchange="this.form.submit()">
                    <option value="">{{ __('market.month') }}</option>
                    @foreach ($months as $i => $month)
                        <option value="{{ $i + 1 }}" @selected((string) $selected['ay'] === (string) ($i + 1))>{{ $month }}</option>
                    @endforeach
                </select>
                <select name="yil" aria-label="{{ __('market.year') }}" onchange="this.form.submit()">
                    <option value="">{{ __('market.year') }}</option>
                    @foreach ($years as $year)
                        <option value="{{ $year }}" @selected((string) $selected['yil'] === (string) $year)>{{ $year }}</option>
                    @endforeach
                </select>
                <noscript><button type="submit">{{ __('market.fetch') }}</button></noscript>
            </div>
        </form>

        @include('borsa.partials.az-tabs', ['azBase' => '/borsa/borsa-kapanis', 'letters' => $letters, 'letter' => $letter])

        <div class="borsa-layout">
            <div class="borsa-main">
                <x-market-table :title="__('market.stock_title')" :firstLabel="__('market.stock_title')" :quotes="$paginator" :columns="$columns"
                    :urlResolver="fn ($q) => $q->instrument->stockUrl()" firstField="code"
                    :head="false" id="borsa-kapanis-table" />

                @include('borsa.partials.pagination', ['paginator' => $paginator])
            </div>
        </div>

        @include('altin.partials.cme-note')
    </div>
@endsection