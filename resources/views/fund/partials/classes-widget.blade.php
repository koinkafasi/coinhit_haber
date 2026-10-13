{{-- Fon Sınıflarında Bugün widget — referans birebir:
     head-type1 başlık + 7 sınıf dropdown (select + <i> oku, sağ üstte hizalı) +
     her panelde Yükselen/Düşen tab-menu (iki tablo da DOM'da). --}}
@props(['fundClasses' => [], 'showHead' => true])

<div class="w-full relative mb-4" id="fon-siniflari" data-widget-type="fund-classes">
    @if ($showHead)
        <x-section-head :title="__('media.fund_classes_today')" />
    @endif

    <div class="select-tab-menu relative">
        <div class="select-tab-head md:w-64 h-10 relative md:absolute mb-4 md:mb-0 md:top-4 md:right-0">
            <select class="select-tab-options w-full border p-2 appearance-none outline-none" aria-label="{{ __('media.fund_classes_aria') }}">
                @foreach ($fundClasses as $class)
                    <option value="{{ $class['key'] }}">{{ $class['label'] }}</option>
                @endforeach
            </select>
            <i class="bloomberght-icon-font-chevron-bottom absolute bottom-3 right-3 text-xs pointer-events-none"></i>
        </div>

        <div class="select-tab-body">
            @foreach ($fundClasses as $i => $class)
                <div class="select-tab-content fund-class-panel{{ $i === 0 ? '' : ' hidden' }}" data-id="{{ $class['key'] }}">
                    {{-- Yükselen / Düşen — referans: `tab-button w-1/2 border py-2` --}}
                    <div class="tab-menu fund-dir-menu">
                        <div class="tab-head cursor-pointer flex w-full text-center mb-4">
                            <div class="tab-button w-1/2 border py-2 bg-gray-200" data-tab="tab-graph-0">{{ __('media.rising_funds') }}</div>
                            <div class="tab-button w-1/2 border py-2 bg-white" data-tab="tab-graph-1">{{ __('media.falling_funds') }}</div>
                        </div>
                        <div class="tab-body">
                            <div class="tab-content" data-id="tab-graph-0">
                                @include('fund.partials.leaderboard-table', [
                                    'title' => __('media.most_rising_funds'),
                                    'rows' => $class['rows']['yukselen'],
                                ])
                            </div>
                            <div class="tab-content hidden" data-id="tab-graph-1">
                                @include('fund.partials.leaderboard-table', [
                                    'title' => __('media.most_falling_funds'),
                                    'rows' => $class['rows']['dusen'],
                                ])
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
