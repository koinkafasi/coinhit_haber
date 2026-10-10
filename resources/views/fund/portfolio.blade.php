@extends('front.layouts.app')

@section('title', __('media.fund_portfolio_allocation').' | '.($siteSettings['site_name'] ?? 'Bloomberg HT'))
@section('description', __('media.fund_portfolio_desc'))

@section('content')
    <div class="fund-page fund-tool-page">
        <x-breadcrumb :items="[
            ['label' => __('media.fund_title'), 'url' => \App\Support\Locale::url('/yatirim-fonlari')],
            ['label' => __('media.fund_portfolio_allocation')],
        ]" />

        <h1 class="page-title font-unna font-bold">{{ __('media.fund_portfolio_allocation') }}</h1>

        @include('fund.partials.tool-nav', ['toolLinks' => $toolLinks])

        <form class="fund-portfolio-form" method="get" action="{{ \App\Support\Locale::url('/yatirim-fonlari/fon-portfoy-dagilim') }}">
            <div class="fund-compare-field">
                <label for="fund-select">{{ __('media.select_fund') }}</label>
                <select id="fund-select" name="fon">
                    @foreach ($fundOptions as $option)
                        <option value="{{ $option->code }}" @selected($fund && $fund->code === $option->code)>
                            {{ $option->code }} — {{ $option->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="fund-compare-submit">{{ __('media.view') }}</button>
        </form>

        @if ($fund && ! empty($distribution))
            <section class="fund-portfolio-result">
                <x-section-head :title="__('media.portfolio_allocation_title', ['name' => $fund->name])" />

                @if ($snapshot)
                    <div class="fund-portfolio-quote">
                        <span class="fund-detail-code">{{ $fund->code }}</span>
                        <span class="fund-detail-class">{{ $fund->class_name }}</span>
                        <span class="fund-detail-price lastPrice font-bold">{{ $snapshot['price_formatted'] }} TL</span>
                        <span class="fund-detail-change">{{ __('media.risk_value') }}: {{ $snapshot['risk_value'] }} / 7 · {{ $snapshot['risk_label'] }}</span>
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