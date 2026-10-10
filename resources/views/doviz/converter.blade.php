@extends('front.layouts.app')

@section('title', 'Döviz Çevirici | '.($siteSettings['site_name'] ?? 'Bloomberg HT'))
@section('description', '22 para birimi arasında güncel kurlarla döviz çevirisi yapın.')

@section('content')
    <div class="borsa-page doviz-page">
        <h1 class="page-title font-unna font-bold">Döviz Çevirici</h1>

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