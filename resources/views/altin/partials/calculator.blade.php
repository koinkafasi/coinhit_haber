{{-- Altın Hesaplama widget'ı (spec §6, §8) — Alış/Satış modu, A↔B enstrüman, Tutar, HESAPLA --}}
@php
    $options = $calculator['options'];
    $resultText = number_format($calculator['result'], 2, ',', '.');
@endphp

<section class="altin-calculator doviz-converter" data-gold-calculator
         data-values='@json($calculator['values'])'>
    <x-section-head title="Altın Hesaplama" :href="null" />

    <div class="gold-calc-modes" role="tablist" aria-label="İşlem yönü">
        @foreach ($calculator['modes'] as $key => $label)
            <button type="button" role="tab"
                    class="range-btn gold-calc-mode {{ $key === $calculator['mode'] ? 'is-active' : '' }}"
                    data-gold-mode="{{ $key }}"
                    aria-selected="{{ $key === $calculator['mode'] ? 'true' : 'false' }}">{{ $label }}</button>
        @endforeach
    </div>

    <form class="converter-form" data-gold-form>
        <div class="converter-field">
            <label for="gold-from">Mevcut Altın</label>
            <select id="gold-from" name="from" data-gold-from>
                @foreach ($options as $key => $label)
                    <option value="{{ $key }}" @selected($key === $calculator['from'])>{{ $label }}</option>
                @endforeach
            </select>
        </div>

        <div class="converter-field">
            <label for="gold-to">Çevrilecek Altın</label>
            <select id="gold-to" name="to" data-gold-to>
                @foreach ($options as $key => $label)
                    <option value="{{ $key }}" @selected($key === $calculator['to'])>{{ $label }}</option>
                @endforeach
            </select>
        </div>

        <div class="converter-field">
            <label for="gold-amount">Tutar</label>
            <input id="gold-amount" name="amount" type="number" inputmode="decimal"
                   min="0" step="0.01" value="{{ $calculator['amount'] }}" data-gold-amount>
        </div>

        <button type="submit" class="converter-submit" data-gold-submit>HESAPLA</button>

        <output class="converter-result" data-gold-result aria-live="polite">
            <span class="converter-result-value" data-gold-value>{{ $resultText }}</span>
            <span class="converter-result-currency" data-gold-currency>{{ $options[$calculator['to']] }}</span>
        </output>
    </form>
</section>