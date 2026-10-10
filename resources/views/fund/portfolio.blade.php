@extends('front.layouts.app')

@section('title', 'Fon Portföy Dağılımı | '.($siteSettings['site_name'] ?? 'Bloomberg HT'))
@section('description', 'Yatırım fonlarının varlık sınıfı bazında portföy dağılımı: hisse senedi, borçlanma araçları, ters repo, mevduat ve diğer kalemlerin oranları.')

@section('content')
    <div class="fund-page fund-tool-page">
        <x-breadcrumb :items="[
            ['label' => 'Yatırım Fonları', 'url' => \App\Support\Locale::url('/yatirim-fonlari')],
            ['label' => 'Fon Portföy Dağılımı'],
        ]" />

        <h1 class="page-title font-unna font-bold">Fon Portföy Dağılımı</h1>

        @include('fund.partials.tool-nav', ['toolLinks' => $toolLinks])

        <form class="fund-portfolio-form" method="get" action="{{ \App\Support\Locale::url('/yatirim-fonlari/fon-portfoy-dagilim') }}">
            <div class="fund-compare-field">
                <label for="fund-select">Fon Seçiniz</label>
                <select id="fund-select" name="fon">
                    @foreach ($fundOptions as $option)
                        <option value="{{ $option->code }}" @selected($fund && $fund->code === $option->code)>
                            {{ $option->code }} — {{ $option->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="fund-compare-submit">Görüntüle</button>
        </form>

        @if ($fund && ! empty($distribution))
            <section class="fund-portfolio-result">
                <x-section-head :title="$fund->name.' — Portföy Dağılımı'" />

                @if ($snapshot)
                    <div class="fund-portfolio-quote">
                        <span class="fund-detail-code">{{ $fund->code }}</span>
                        <span class="fund-detail-class">{{ $fund->class_name }}</span>
                        <span class="fund-detail-price lastPrice font-bold">{{ $snapshot['price_formatted'] }} TL</span>
                        <span class="fund-detail-change">Risk Değeri: {{ $snapshot['risk_value'] }} / 7 · {{ $snapshot['risk_label'] }}</span>
                    </div>
                @endif

                <div class="fund-distribution">
                    @foreach ($distribution as $asset => $percent)
                        <div class="fund-distribution-row">
                            <span class="fund-distribution-label">{{ $asset }}</span>
                            <span class="fund-distribution-bar-track">
                                <span class="fund-distribution-bar" style="width: {{ max(0, min(100, (float) $percent)) }}%"></span>
                            </span>
                            <span class="fund-distribution-value">{{ number_format((float) $percent, 1, ',', '.') }}%</span>
                        </div>
                    @endforeach
                </div>
            </section>
        @endif
    </div>
@endsection