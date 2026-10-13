{{-- İlgili Haberler — referans `head-news-type1` birebir:
     h2 + 2 kolonlu grid; 1. kolon tip3 kapak + type2, 2. kolon type2. --}}
@if (($items ?? collect())->isNotEmpty())
    <div data-widget-type="head-news-type1">
        <div data-type="head-type1">
            <div class="relative before:absolute before:top-0 before:m-auto before:w-[230px] before:h-2.5 before:bg-black before:bottom-0 before:h-2.5 border-b border-black mb-4"></div>
            <div class="mb-3">
                <h2 class="font-unna font-bold text-3xl">{{ $title ?? __('market.related_news') }}</h2>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 md:gap-4">
            <div class="flex flex-col">
                @if ($items->get(0))
                    <x-news-card :news="$items[0]" type="type3" />
                @endif
                @foreach ($items->slice(1, 2) as $item)
                    <x-news-card :news="$item" type="type2" />
                @endforeach
            </div>
            <div class="flex flex-col">
                @foreach ($items->slice(3) as $item)
                    <x-news-card :news="$item" type="type2" />
                @endforeach
            </div>
        </div>
    </div>
@endif
