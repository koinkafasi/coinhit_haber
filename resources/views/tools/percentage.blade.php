@extends('front.layouts.app')

@section('title', $pageTitle)
@section('description', $pageDescription)
@section('canonical', url('/yuzde-hesaplama'))
@section('og_image', url('/img/manifest-192.png'))
@section('og_image_width', '180')
@section('og_image_height', '180')

@section('content')
    <div class="static-layout tool-layout">
        <x-breadcrumb :items="[['label' => 'Yüzde Hesaplama', 'url' => '/yuzde-hesaplama']]" />

        <article class="static-page tool-page">
            <h1 class="static-title font-unna font-bold">Yüzde Hesaplama</h1>
            <p>Bir sayının yüzdesini veya iki sayı arasındaki yüzde değişimi hesaplayın.</p>

            <form class="tool-form" data-percentage-calculator>
                <div class="tool-fields">
                    <label>Değer <input type="number" step="any" name="value" value="100" inputmode="decimal"></label>
                    <label>Yüzde (%) <input type="number" step="any" name="percent" value="10" inputmode="decimal"></label>
                </div>
                <p class="tool-result">Sonuç: <output data-result>110</output></p>
            </form>
        </article>
    </div>
@endsection

@push('scripts')
<script>
(function () {
    var form = document.querySelector('[data-percentage-calculator]');
    if (!form) { return; }
    var value = form.querySelector('[name="value"]');
    var percent = form.querySelector('[name="percent"]');
    var result = form.querySelector('[data-result]');
    function update() {
        var base = parseFloat(value.value) || 0;
        var rate = parseFloat(percent.value) || 0;
        result.textContent = (base + (base * rate / 100)).toLocaleString('tr-TR', { maximumFractionDigits: 4 });
    }
    form.addEventListener('submit', function (event) { event.preventDefault(); update(); });
    value.addEventListener('input', update);
    percent.addEventListener('input', update);
    update();
})();
</script>
@endpush