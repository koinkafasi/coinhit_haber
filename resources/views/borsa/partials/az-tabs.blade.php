{{-- A-Z harf sekmeleri (spec §8 /borsa/hisseler, /borsa/borsa-kapanis) --}}
@php($azBase = $azBase ?? '/borsa/hisseler')
<div class="az-tabs" role="tablist" aria-label="Harf seçimi">
    <a href="{{ $azBase.($azQuery ?? '') }}" class="{{ empty($letter) ? 'is-active' : '' }}">Tümü</a>
    @foreach ($letters as $l)
        <a href="{{ $azBase.'/'.$l.($azQuery ?? '') }}" class="{{ $letter === $l ? 'is-active' : '' }}">{{ $l }}</a>
    @endforeach
</div>