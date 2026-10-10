@extends('front.layouts.app')

@section('title', __('media.compare_funds').' | '.($siteSettings['site_name'] ?? 'Bloomberg HT'))
@section('description', __('media.fund_compare_desc'))

@section('content')
    <div class="fund-page fund-tool-page">
        <x-breadcrumb :items="[
            ['label' => __('media.fund_title'), 'url' => \App\Support\Locale::url('/yatirim-fonlari')],
            ['label' => __('media.compare_funds')],
        ]" />

        <h1 class="page-title font-unna font-bold">{{ __('media.compare_funds') }}</h1>

        @include('fund.partials.tool-nav', ['toolLinks' => $toolLinks])

        <form class="fund-compare-form" method="get" action="{{ \App\Support\Locale::url('/yatirim-fonlari/fon-karsilastirma') }}">
            <div class="fund-compare-field">
                <label for="fund-a">{{ __('media.fund_1') }}</label>
                <select id="fund-a" name="a">
                    @foreach ($fundOptions as $option)
                        <option value="{{ $option->code }}" @selected($a && $a->code === $option->code)>
                            {{ $option->code }} — {{ $option->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="fund-compare-field">
                <label for="fund-b">{{ __('media.fund_2') }}</label>
                <select id="fund-b" name="b">
                    @foreach ($fundOptions as $option)
                        <option value="{{ $option->code }}" @selected($b && $b->code === $option->code)>
                            {{ $option->code }} — {{ $option->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="fund-compare-submit">{{ __('media.compare') }}</button>
        </form>

        @if ($a && $b && ! empty($rows))
            <section class="fund-compare-result">
                <x-section-head :title="__('media.comparison_result')" />
                <div class="table-scroll">
                    <table class="market-table fund-compare-table w-full text-center text-sm bg-white">
                        <thead>
                            <tr class="border-b">
                                <th class="p-4 text-left">{{ __('media.criterion') }}</th>
                                <th class="p-4">{{ $a->code }} — {{ $a->name }}</th>
                                <th class="p-4">{{ $b->code }} — {{ $b->name }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($rows as $row)
                                @php
                                    $betterA = $row['higher'] ? $row['aNum'] >= $row['bNum'] : $row['aNum'] <= $row['bNum'];
                                    $betterB = $row['higher'] ? $row['bNum'] >= $row['aNum'] : $row['bNum'] <= $row['aNum'];
                                    $tie = abs($row['aNum'] - $row['bNum']) < 1e-9;
                                @endphp
                                <tr class="border-b text-sm">
                                    <td class="py-4 px-4 text-left font-medium">{{ $row['label'] }}</td>
                                    <td class="py-4 px-4 {{ (! $tie && $betterA) ? 'fund-compare-better' : '' }}">{{ $row['a'] }}</td>
                                    <td class="py-4 px-4 {{ (! $tie && $betterB) ? 'fund-compare-better' : '' }}">{{ $row['b'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </section>
        @endif
    </div>
@endsection