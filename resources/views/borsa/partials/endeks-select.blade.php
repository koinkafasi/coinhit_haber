{{-- Endeks Seçiniz dropdown (spec §8) --}}
<form class="endeks-select" method="get" action="{{ $endeksAction ?? '/borsa/hisseler' }}">
    <label for="endeks-select-input">Endeks Seçiniz</label>
    <select name="endeks" id="endeks-select-input" class="endeks-select-input" onchange="this.form.submit()">
        <option value="">Endeks Seçiniz</option>
        @foreach ($indexOptions as $option)
            <option value="{{ $option->slug }}" @selected(($selectedIndex ?? '') === $option->slug)>{{ $option->name }}</option>
        @endforeach
    </select>
    @foreach (($hidden ?? []) as $name => $value)
        @if (! empty($value))
            <input type="hidden" name="{{ $name }}" value="{{ $value }}">
        @endif
    @endforeach
    <noscript><button type="submit">Filtrele</button></noscript>
</form>