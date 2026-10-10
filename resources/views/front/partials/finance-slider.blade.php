@php
    $groups = $financeGroups ?? [];
    $first = true;
@endphp

{{-- Piyasa ticker widget'ı: Borsa | Döviz | Emtia | Faiz (Bölüm 2.4, 17.3) --}}
<div class="finance-slider bg-white border-b" data-type="finance-slider" data-widget-type="finance-slider">
    <div class="container finance-slider-inner">
        <div class="finance-slider-tabs" role="tablist" aria-label="Piyasa verileri">
            @foreach ($groups as $key => $group)
                <button type="button"
                        role="tab"
                        class="tab-button finance-slider-tab {{ $first ? 'is-active' : '' }}"
                        data-tab="{{ $key }}"
                        aria-selected="{{ $first ? 'true' : 'false' }}">{{ $group['label'] }}</button>
                @php($first = false)
            @endforeach
        </div>

        <div class="finance-slider-viewport">
            @php($first = true)
            @foreach ($groups as $key => $group)
                <div class="tab-content finance-slider-track {{ $first ? '' : 'hidden' }}" data-id="{{ $key }}">
                    @foreach ($group['quotes'] as $quote)
                        <a class="finance-slider-row" href="{{ $quote->instrument->url }}" data-market-code="{{ $quote->code() }}">
                            <span class="fs-name text-xs">{{ $quote->instrument->name }}</span>
                            <span class="lastPrice font-bold" data-market-field="last">{{ $quote->formattedLast() }}</span>
                            <span class="fs-change text-xs">
                                <span class="setIcon bloomberght-icon-font-icon-graphic-{{ $quote->direction() === 'up' ? 'up' : ($quote->direction() === 'down' ? 'down' : 'fixed') }}" aria-hidden="true"></span>
                                <span class="percentChange {{ $quote->direction() === 'up' ? 'text-green-700' : ($quote->direction() === 'down' ? 'text-red-700' : 'text-gray-400') }}" data-market-field="change_percent">{{ $quote->formattedChangePercent() }}</span>
                            </span>
                        </a>
                    @endforeach
                </div>
                @php($first = false)
            @endforeach
        </div>
    </div>
</div>