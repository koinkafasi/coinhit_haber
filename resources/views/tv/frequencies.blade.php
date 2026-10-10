@extends('front.layouts.app')

@section('title', 'Frekans Listesi | '.($siteSettings['site_name'] ?? 'Bloomberg HT'))
@section('description', 'Bloomberg HT uydu ve kablo frekans bilgileri: Türksat, Eutelsat ve operatör kanal numaraları.')
@section('canonical', url(\App\Support\Locale::url('/tv/frekans-listesi')))

@section('content')
    <div class="tv-page tv-frequencies-page">
        <x-breadcrumb :items="[['label' => 'TV', 'url' => \App\Support\Locale::url('/tv')], ['label' => 'Frekans Listesi']]" />

        <x-section-head title="Frekans Listesi" level="h1" />

        <p class="tv-freq-intro">Bloomberg HT yayınlarını aşağıdaki uydu ve kablo platformlarından izleyebilirsiniz.</p>

        <h2 class="tv-group-title font-unna font-bold">Uydu Frekansları</h2>
        <div class="tv-table-wrap">
            <table class="tv-freq-table">
                <thead>
                    <tr>
                        <th>Uydu</th>
                        <th>Konum</th>
                        <th>Frekans</th>
                        <th>Sembol Oranı</th>
                        <th>FEC</th>
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

        <h2 class="tv-group-title font-unna font-bold">Kablo / Platform Kanalları</h2>
        <div class="tv-table-wrap">
            <table class="tv-freq-table">
                <thead>
                    <tr>
                        <th>Platform</th>
                        <th>Kanal</th>
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