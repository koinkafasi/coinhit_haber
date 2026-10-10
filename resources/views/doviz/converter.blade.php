@extends('front.layouts.app')

@section('title', __('market.converter').' | '.($siteSettings['site_name'] ?? 'Bloomberg HT'))
@section('description', __('market.doviz_converter_desc'))

@section('content')
    <div class="borsa-page doviz-page">
        <h1 class="page-title font-unna font-bold">{{ __('market.converter') }}</h1>

        <div class="borsa-layout">
            <div class="borsa-main">
                @include('doviz.partials.converter')
            </div>

            <aside class="borsa-aside">
                <x-ad-zone :zone="1257" :index="1" />
            </aside>
        </div>
    </div>
@endsection