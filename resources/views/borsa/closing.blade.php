@extends('front.layouts.app')

@section('title', 'Borsa Kapanış | '.($siteSettings['site_name'] ?? 'Bloomberg HT'))
@section('description', 'Borsa kapanış verileri: hisse bazında dün, kapanış, yüksek, düşük, değişim ve hacim.')

@section('content')
    <div class="borsa-page">
        <h1 class="page-title font-unna font-bold">Borsa Kapanış</h1>

        @include('borsa.partials.quick-links')

        @php
            $closingAction = $letter ? '/borsa/borsa-kapanis/'.$letter : '/borsa/borsa-kapanis';
            $columns = [
                ['label' => 'DÜN', 'field' => 'previous_close', 'class' => 'previousClose'],
                ['label' => 'KAPIŞ', 'field' => 'last', 'class' => 'lastPrice'],
                ['label' => 'YÜKSEK', 'field' => 'high', 'class' => 'high'],
                ['label' => 'DÜŞÜK', 'field' => 'low', 'class' => 'low'],
                ['label' => 'DEĞİŞİM%', 'field' => 'change_percent', 'class' => 'percentChange', 'color' => true],
                ['label' => 'HACİM LOT', 'field' => 'volume_lot', 'class' => 'volumeLot'],
                ['label' => 'HACİM TL', 'field' => 'volume_tl', 'class' => 'volumeTl'],
            ];
        @endphp

        <form class="date-select" method="get" action="{{ $closingAction }}">
            <label for="tarih-gun">Tarih Seçiniz</label>
            <div class="date-select-fields">
                <select name="gun" id="tarih-gun" aria-label="Gün" onchange="this.form.submit()">
                    <option value="">Gün</option>
                    @foreach ($days as $day)
                        <option value="{{ $day }}" @selected((string) $selected['gun'] === (string) $day)>{{ str_pad((string) $day, 2, '0', STR_PAD_LEFT) }}</option>
                    @endforeach
                </select>
                <select name="ay" aria-label="Ay" onchange="this.form.submit()">
                    <option value="">Ay</option>
                    @foreach ($months as $i => $month)
                        <option value="{{ $i + 1 }}" @selected((string) $selected['ay'] === (string) ($i + 1))>{{ $month }}</option>
                    @endforeach
                </select>
                <select name="yil" aria-label="Yıl" onchange="this.form.submit()">
                    <option value="">Yıl</option>
                    @foreach ($years as $year)
                        <option value="{{ $year }}" @selected((string) $selected['yil'] === (string) $year)>{{ $year }}</option>
                    @endforeach
                </select>
                <noscript><button type="submit">Getir</button></noscript>
            </div>
        </form>

        @include('borsa.partials.az-tabs', ['azBase' => '/borsa/borsa-kapanis', 'letters' => $letters, 'letter' => $letter])

        <div class="borsa-layout">
            <div class="borsa-main">
                <x-market-table title="Hisse" firstLabel="Hisse" :quotes="$paginator" :columns="$columns"
                    :urlResolver="fn ($q) => $q->instrument->stockUrl()" firstField="code"
                    :head="false" id="borsa-kapanis-table" />

                @include('borsa.partials.pagination', ['paginator' => $paginator])
            </div>
        </div>

        @include('altin.partials.cme-note')
    </div>
@endsection