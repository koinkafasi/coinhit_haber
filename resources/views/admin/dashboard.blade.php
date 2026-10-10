@extends('admin.layouts.app')

@section('title', 'Panel')
@section('heading', 'Panel')

@section('content')
    <div class="grid-stats">
        <div class="stat"><div class="num">{{ number_format($stats['news']) }}</div><div class="lbl">Haber</div></div>
        <div class="stat"><div class="num">{{ number_format($stats['authors']) }}</div><div class="lbl">Yazar / Görüş</div></div>
        <div class="stat"><div class="num">{{ number_format($stats['programs']) }}</div><div class="lbl">Program</div></div>
        <div class="stat"><div class="num">{{ number_format($stats['podcasts']) }}</div><div class="lbl">Podcast</div></div>
        <div class="stat"><div class="num">{{ number_format($stats['galleries']) }}</div><div class="lbl">Foto Haber</div></div>
        <div class="stat"><div class="num">{{ number_format($stats['kap']) }}</div><div class="lbl">KAP Bildirimi</div></div>
        <div class="stat"><div class="num">{{ number_format($stats['charts']) }}</div><div class="lbl">Grafik</div></div>
        <div class="stat"><div class="num">{{ number_format($stats['instruments']) }}</div><div class="lbl">Piyasa Enstrümanı</div></div>
    </div>
@endsection