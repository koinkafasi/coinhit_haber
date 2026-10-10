@extends('front.layouts.app')

@section('title', $fund->name.' ('.$fund->code.') | '.($siteSettings['site_name'] ?? 'Bloomberg HT'))
@section('description', __('media.fund_meta_description', ['name' => $fund->name]))

@section('content')
    @php($dirClass = $snapshot['direction'] === 'up' ? 'text-green-700' : ($snapshot['direction'] === 'down' ? 'text-red-700' : 'text-gray-400'))
    <div class="fund-page fund-detail-page">
        <x-breadcrumb :items="[
            ['label' => __('media.fund_title'), 'url' => \App\Support\Locale::url('/yatirim-fonlari')],
            ['label' => $fund->name],
        ]" />

        @include('fund.partials.tool-nav', ['toolLinks' => $toolLinks])

        <header class="fund-detail-head">
            <h1 class="font-unna font-bold">{{ $fund->name }}</h1>
            <div class="fund-detail-meta">
                <span class="fund-detail-code">{{ $fund->code }}</span>
                <span class="fund-detail-class">{{ $fund->class_name }}</span>
                @if ($fund->type)
                    <span class="fund-detail-type">{{ $fund->type }}</span>
                @endif
            </div>
            <div class="fund-detail-quote">
                <span class="fund-detail-price lastPrice font-bold">{{ $snapshot['price_formatted'] }} TL</span>
                <span class="fund-detail-change {{ $dirClass }}">{{ __('media.change_tl') }} {{ $snapshot['change_tl_formatted'] }}</span>
                <span class="fund-detail-change {{ $dirClass }}">{{ __('media.change_usd') }} {{ $snapshot['change_usd_formatted'] }}</span>
            </div>
        </header>

        <section class="fund-return-matrix" id="getiri-matrisi">
            <x-section-head :title="__('media.return_matrix')" />
            <div class="table-scroll">
                <table class="market-table fund-matrix-table w-full text-center text-sm bg-white">
                    <thead>
                        <tr class="border-b">
                            @foreach (\App\Models\Fund::returnColumns() as $column)
                                <th class="p-4">{{ $column['label'] }}</th>
                            @endforeach
                            <th class="p-4">{{ __('media.risk_value') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="text-sm">
                            @foreach (\App\Models\Fund::returnColumns() as $column)
                                @php($value = (float) ($snapshot['returns'][$column['field']] ?? 0))
                                <td class="py-4 px-4 {{ $value > 0 ? 'text-green-700' : ($value < 0 ? 'text-red-700' : 'text-gray-400') }}">{{ number_format($value, 2, ',', '.') }}%</td>
                            @endforeach
                            <td class="py-4 px-4">{{ $snapshot['risk_value'] }} / 7 · {{ $snapshot['risk_label'] }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>

        <section class="fund-portfolio-summary" id="portfoy-bilgisi">
            <x-section-head :title="__('media.portfolio_info')" />
            <p class="fund-portfolio-text">
                {{ __('media.fund_about', ['name' => $fund->name, 'code' => $fund->code, 'class' => $fund->class_name]) }}
                <a href="{{ \App\Support\Locale::url('/yatirim-fonlari/fon-portfoy-dagilim?fon='.$fund->code) }}">{{ __('media.fund_portfolio_allocation') }}</a>
                {{ __('media.fund_about_tail') }}
            </p>
        </section>

        @if ($related->isNotEmpty())
            <section class="fund-related">
                <x-section-head :title="__('media.class_funds', ['class' => $fund->class_name])" :href="\App\Support\Locale::url('/yatirim-fonlari#fon-siniflari')" />
                @include('fund.partials.leaderboard-table', ['rows' => $related->map(fn ($f) => app(\App\Services\Fund\FundDataService::class)->snapshot($f))])
            </section>
        @endif

        @include('borsa.partials.related-news', ['items' => $relatedNews])
    </div>
@endsection