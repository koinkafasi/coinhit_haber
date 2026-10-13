@extends('front.layouts.app')

@section('title', __('media.fund_classes_page_title').' | '.($siteSettings['site_name'] ?? 'Bloomberg HT'))
@section('description', __('media.fund_index_desc'))
@section('canonical', url(\App\Support\Locale::url('/yatirim-fonlari/fon-siniflarinda-bugun')))

@section('content')
    <div class="fund-page fund-classes-page">
        <x-breadcrumb :items="[
            ['label' => __('media.fund_title'), 'url' => \App\Support\Locale::url('/yatirim-fonlari')],
            ['label' => __('media.fund_classes_page_title')],
        ]" />

        <div data-type="head-type1">
            <div class="relative before:absolute before:top-0 before:m-auto before:w-[230px] before:h-2.5 before:bg-black before:bottom-0 before:h-2.5 border-b border-black mb-4"></div>
            <div class="mb-3">
                <h1 class="font-unna font-bold text-3xl">{{ __('media.fund_classes_page_title') }}</h1>
            </div>
        </div>

        @include('fund.partials.classes-widget', ['fundClasses' => $fundClasses, 'showHead' => false])
    </div>
@endsection
