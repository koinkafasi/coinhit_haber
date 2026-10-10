@extends('front.layouts.app')

@section('title', __('media.frequencies').' | '.($siteSettings['site_name'] ?? 'Bloomberg HT'))
@section('description', __('media.frequencies_desc'))
@section('canonical', url(\App\Support\Locale::url('/tv/frekans-listesi')))

@section('content')
    <div class="tv-page tv-frequencies-page">
        <x-breadcrumb :items="[['label' => __('media.tv'), 'url' => \App\Support\Locale::url('/tv')], ['label' => __('media.frequencies')]]" />

        <x-section-head :title="__('media.frequencies')" level="h1" />

        <p class="tv-freq-intro">{{ __('media.frequencies_intro') }}</p>

        <h2 class="tv-group-title font-unna font-bold">{{ __('media.satellite_frequencies') }}</h2>
        <div class="tv-table-wrap">
            <table class="tv-freq-table">
                <thead>
                    <tr>
                        <th>{{ __('media.satellite') }}</th>
                        <th>{{ __('media.position') }}</th>
                        <th>{{ __('media.frequency') }}</th>
                        <th>{{ __('media.symbol_rate') }}</th>
                        <th>{{ __('media.fec') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($satellite as [$sat, $position, $frequency, $symbol, $fec])
                        <tr>
                            <td>{{ $sat }}</td>
                            <td>{{ $position }}</td>
                            <td>{{ $frequency }}</td>
                            <td>{{ $symbol }}</td>
                            <td>{{ $fec }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <h2 class="tv-group-title font-unna font-bold">{{ __('media.cable_channels') }}</h2>
        <div class="tv-table-wrap">
            <table class="tv-freq-table">
                <thead>
                    <tr>
                        <th>{{ __('media.platform') }}</th>
                        <th>{{ __('media.channel') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($cable as [$platform, $channel])
                        <tr>
                            <td>{{ $platform }}</td>
                            <td>{{ $channel }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection