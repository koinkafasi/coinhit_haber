@extends('front.layouts.app')

@section('title', __('media.broadcast_schedule').' | '.($siteSettings['site_name'] ?? 'Bloomberg HT'))
@section('description', __('media.schedule_desc'))
@section('canonical', url(\App\Support\Locale::url('/yayinakisi')))

@section('content')
    <div class="tv-page tv-schedule-page">
        <x-breadcrumb :items="[['label' => __('media.tv'), 'url' => \App\Support\Locale::url('/tv')], ['label' => __('media.broadcast_schedule')]]" />

        <x-section-head :title="__('media.broadcast_schedule')" level="h1" />

        @include('tv.partials.schedule', ['days' => $days])
    </div>
@endsection