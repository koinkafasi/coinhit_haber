@extends('front.layouts.app')

@section('title', __('ui.ipo_title').' | '.($siteSettings['site_name'] ?? 'Bloomberg HT'))
@section('description', __('ui.ipo_intro'))
@section('canonical', url(\App\Support\Locale::url('/halka-arz-takvimi')))

@section('content')
    <div class="borsa-page ipo-page">
        <div class="section-title bht-page-band">
            <h1 class="font-unna font-bold">{{ __('ui.ipo_title') }}</h1>
        </div>

        <p class="ipo-intro">{{ __('ui.ipo_intro') }}</p>
        <p class="ipo-legal">{{ __('ui.ipo_legal') }}</p>

        <h2 class="ipo-subtitle font-unna font-bold">{{ __('ui.ipo_request_dates') }}</h2>

        <div class="table-scroll">
            <table class="market-table ipo-table" data-type="table-type1">
                <thead>
                    <tr class="border-b">
                        <th class="sticky left-0 p-4 text-left">{{ __('ui.ipo_col_request') }}</th>
                        <th>{{ __('ui.ipo_col_company') }}</th>
                        <th>{{ __('ui.ipo_col_value') }}</th>
                        <th>{{ __('ui.ipo_col_price') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($items as $item)
                        <tr class="border-b ipo-row">
                            <td class="py-4 px-4 sticky left-0">{{ $item->request_range }}</td>
                            <td class="py-4 px-4">{{ $item->company_name }}@if ($item->ticker) <span class="ipo-ticker">({{ $item->ticker }})</span>@endif</td>
                            <td class="py-4 px-4">{{ $item->lot_value ? number_format((float) $item->lot_value, 0, ',', '.') : '—' }}</td>
                            <td class="py-4 px-4">{{ $item->price ? number_format((float) $item->price, 2, ',', '.').' TL' : '—' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="ipo-empty">{{ __('ui.no_data') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($related->isNotEmpty())
            <section class="borsa-related">
                <x-section-head :title="__('ui.related')" :href="\App\Support\Locale::url('/haberler')" />
                <div class="news-card-grid cols-2">
                    @foreach ($related as $item)
                        <x-news-card :news="$item" type="type2" :showSpot="true" />
                    @endforeach
                </div>
            </section>
        @endif

        @if ($featured->isNotEmpty())
            <section class="borsa-related">
                <x-section-head :title="__('ui.featured')" :href="\App\Support\Locale::url('/haberler')" />
                <div class="news-card-grid cols-2">
                    @foreach ($featured as $item)
                        <x-news-card :news="$item" type="type1" />
                    @endforeach
                </div>
            </section>
        @endif
    </div>
@endsection