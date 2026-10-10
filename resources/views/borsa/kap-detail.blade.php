@extends('front.layouts.app')

@section('title', $disclosure->company_code.' '.$disclosure->title.' | '.__('market.kap').' | '.($siteSettings['site_name'] ?? 'Bloomberg HT'))
@section('description', $disclosure->company_name.__('market.kap_detail_desc_prefix').$disclosure->title)

@section('content')
    <div class="borsa-page kap-page">
        <x-breadcrumb :items="[
            ['label' => __('market.kap_news_heading'), 'url' => \App\Support\Locale::url('/borsa/hisseler/kap-haberleri')],
            ['label' => $disclosure->company_code.'/'.$disclosure->company_name, 'url' => \App\Support\Locale::url('/borsa/hisseler/kap-haberleri')],
            ['label' => \Illuminate\Support\Str::limit($disclosure->title, 40)],
        ]" />

        <div class="borsa-layout">
            <div class="borsa-main kap-content">
                <h1 class="kap-title font-unna font-bold">{{ $disclosure->title }}</h1>

                <p class="kap-meta text-sm">
                    <span class="kap-company-badge">{{ $disclosure->company_code }}</span>
                    {{ $disclosure->company_name }}
                    <time datetime="{{ $disclosure->published_at?->toIso8601String() }}">
                        {{ $disclosure->published_at?->format('d.m.Y H:i') }}
                    </time>
                </p>

                @include('borsa.partials.share', [
                    'shareUrl' => url($disclosure->url),
                    'shareTitle' => $disclosure->company_code.' '.$disclosure->title,
                ])

                @if ($disclosure->summary)
                    <p class="kap-summary">{{ $disclosure->summary }}</p>
                @endif

                <table class="widget-table-kapnews">
                    <thead>
                        <tr>
                            <th>TR</th>
                            <th>EN</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($disclosure->fields as $field)
                            <tr data-kap-key="{{ $field->key }}">
                                <td data-kap-lang="tr">
                                    <strong>{{ $field->label_tr }}</strong>
                                    <div class="kap-field-value">{!! nl2br(e($field->value_tr)) !!}</div>
                                </td>
                                <td data-kap-lang="en">
                                    <strong>{{ $field->label_en }}</strong>
                                    <div class="kap-field-value">{!! nl2br(e($field->value_en)) !!}</div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <aside class="borsa-aside">
                <div class="aside-block">
                    <x-section-head :title="__('market.related_kap_news')" :href="\App\Support\Locale::url('/borsa/hisseler/kap-haberleri')" />
                    @include('borsa.partials.kap-list', ['disclosures' => $relatedDisclosures])
                </div>
                <x-ad-zone :zone="1257" :index="1" />
            </aside>
        </div>
    </div>
@endsection