{{-- A-Z harf sekmeleri (spec §8 /borsa/hisseler, /borsa/borsa-kapanis) --}}
@php($azBase = $azBase ?? '/borsa/hisseler')
<div class="az-tabs" role="tablist" aria-label="{{ __('market.letter_select_aria') }}">
    <a href="{{ \App\Support\Locale::url($azBase).($azQuery ?? '') }}" class="{{ empty($letter) ? 'is-active' : '' }}">{{ __('market.all_range') }}</a>
    @foreach ($letters as $l)
        <a href="{{ \App\Support\Locale::url($azBase.'/'.$l).($azQuery ?? '') }}" class="{{ $letter === $l ? 'is-active' : '' }}">{{ $l }}</a>
    @endforeach
</div>