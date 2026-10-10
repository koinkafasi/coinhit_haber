@extends('front.layouts.app')

@section('title', 'Yayın Akışı | '.($siteSettings['site_name'] ?? 'Bloomberg HT'))
@section('description', 'Bloomberg HT 7 günlük yayın akışı: gün gün saat sıralı program listesi, prime kuşak yayınları ve gece tekrarları.')
@section('canonical', url(\App\Support\Locale::url('/yayinakisi')))

@section('content')
    <div class="tv-page tv-schedule-page">
        <x-breadcrumb :items="[['label' => 'TV', 'url' => \App\Support\Locale::url('/tv')], ['label' => 'Yayın Akışı']]" />

        <x-section-head title="Yayın Akışı" level="h1" />

        @include('tv.partials.schedule', ['days' => $days])
    </div>
@endsection