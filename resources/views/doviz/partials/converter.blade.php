{{-- Döviz Çevirici widget'ı (spec §6, §8) — A↔B kur seçimi + Tutar + HESAPLA --}}
@php
    $currencies = $converter['currencies'];
    $decimals = $converter['decimals'] ?? [];
@endphp

<section class="doviz-converter" data-currency-converter
         data-rates='@json($converter['rates'])'
         data-decimals='@json($decimals)'>
    <x-section-head title="Döviz Çevirici" :href="null" />

    <form class="converter-form" data-converter-form>
        <div class="converter-field">
            <label for="converter-from-{{ $converter['from'] }}">Mevcut Döviz</label>
            <select id="converter-from-{{ $converter['from'] }}" name="from" data-converter-from>
                @foreach ($currencies as $code => $label)
                    <option value="{{ $code }}" @selected($code === $converter['from'])>{{ $code }} — {{ $label }}</option>
                @endforeach
            </select>
        </div>

        <div class="converter-field">
            <label for="converter-to-{{ $converter['to'] }}">Çevrilecek Döviz</label>
            <select id="converter-to-{{ $converter['to'] }}" name="to" data-converter-to>
                @foreach ($currencies as $code => $label)
                    <option value="{{ $code }}" @selected($code === $converter['to'])>{{ $code }} — {{ $label }}</option>
                @endforeach
            </select>
        </div>

        <div class="converter-field">
            <label for="converter-amount">Tutar</label>
            <input id="converter-amount" name="amount" type="number" inputmode="decimal"
                   min="0" step="0.01" value="{{ $converter['amount'] }}" data-converter-amount>
        </div>

        <button type="submit" class="converter-submit" data-converter-submit>HESAPLA</button>

        <output class="converter-result" data-converter-result aria-live="polite">
            <span class="converter-result-value" data-converter-value>{{ $converter['formattedResult'] }}</span>
            <span class="converter-result-currency" data-converter-currency>{{ $converter['to'] }}</span>
        </output>
    </form>
</section>