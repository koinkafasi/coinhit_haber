{{-- Yatırım fonları alt-tool nav (3 buton) — referans birebir:
     üst üste gri bloklar `block p-4 bg-gray-200 text-center text-base font-bold mb-4`. --}}
<nav class="fund-tool-nav" aria-label="{{ __('media.fund_tools_aria') }}">
    @foreach ($toolLinks as $link)
        <a class="fund-tool-link block p-4 bg-gray-200 text-center text-base font-bold mb-4"
           href="{{ \App\Support\Locale::url($link['url']) }}">{{ $link['label'] }}</a>
    @endforeach
</nav>
