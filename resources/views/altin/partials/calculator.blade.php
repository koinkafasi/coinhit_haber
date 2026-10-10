{{-- Altın Hesaplama widget'ı (spec §6, §8) — Alış/Satış modu, A↔B enstrüman, Tutar, HESAPLA --}}
@php
    $options = $calculator['options'];
    $resultText = number_format($calculator['result'], 2, ',', '.');
@endphp

<section class="altin-calculator doviz-converter" data-gold-calculator
         data-values='@json($calculator['values'])'>
    <x-section-head :title="__('market.gold_calculator')" :href="null" />

    <div class="gold-calc-modes" role="tablist" aria-label="{{ __('market.gold_calc_mode_aria') }}">
        @foreach ($calculator['modes'] as $key => $label)
            <button type="button" role="tab"
                    class="range-btn gold-calc-mode {{ $key === $calculator['mode'] ? 'is-active' : '' }}"
                    data-gold-mode="{{ $key }}"
                    aria-selected="{{ $key === $calculator['mode'] ? 'true' : 'false' }}">{{ $label }}</button>
        @endforeach
    </div>

    <form class="converter-form" data-gold-form>
        <div class="converter-field">
            <label for="gold-from">{{ __('market.gold_calc_from') }}</label>
            <select id="gold-from" name="from" data-gold-from>
                @foreach ($options as $key => $label)
                    <option value="{{ $key }}" @selected($key === $calculator['from'])>{{ $label }}</option>
                @endforeach
            </select>
        </div>

        <div class="converter-field">
            <label for="gold-to">{{ __('market.gold_calc_to') }}</label>
            <select id="gold-to" name="to" data-gold-to>
                @foreach ($options as $key => $label)
                    <option value="{{ $key }}" @selected($key === $calculator['to'])>{{ $label }}</option>
                @endforeach
            </select>
        </div>

        <div class="converter-field">
            <label for="gold-amount">{{ __('market.amount') }}</label>
            <input id="gold-amount" name="amount" type="number" inputmode="decimal"
                   min="0" step="0.01" value="{{ $calculator['amount'] }}" data-gold-amount>
        </div>

        <button type="submit" class="converter-submit" data-gold-submit>{{ __('market.calculate') }}</button>

        <output class="converter-result" data-gold-result aria-live="polite">
            <span class="converter-result-value" data-gold-value>{{ $resultText }}</span>
            <span class="converter-result-currency" data-gold-currency>{{ $options[$calculator['to']] }}</span>
        </output>
    </form>
</section>