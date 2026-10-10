@extends('front.layouts.app')

@section('title', __('market.gold_calculator').' | '.($siteSettings['site_name'] ?? 'Bloomberg HT'))
@section('description', __('market.gold_calc_desc'))

@section('content')
    <div class="borsa-page altin-page altin-calc-page">
        <x-breadcrumb :items="[
            ['label' => __('market.gold'), 'url' => \App\Support\Locale::url('/altin')],
            ['label' => __('market.gold_calculator')],
        ]" />

        <h1 class="page-title font-unna font-bold">{{ __('market.gold_calculator') }}</h1>

        <div class="borsa-layout">
            <div class="borsa-main">
                @include('altin.partials.calculator')

                <p class="altin-calc-desc">
                    {{ __('market.gold_calc_intro') }}
                </p>

                @include('borsa.partials.related-news', ['items' => $relatedNews])

                @include('altin.partials.cme-note')
            </div>

            <aside class="borsa-aside">
                <x-ad-zone :zone="1257" :index="1" />

                <div class="aside-block">
                    <x-section-head :title="__('market.featured')" :href="null" />
                    <ul class="aside-list">
                        @foreach ($featured as $item)
                            <li><a href="{{ $item->url }}">{{ $item->title }}</a></li>
                        @endforeach
                    </ul>
                </div>
            </aside>
        </div>
    </div>
@endsection