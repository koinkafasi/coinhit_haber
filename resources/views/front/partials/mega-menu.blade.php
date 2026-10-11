{{-- ============================================================
     HAMBURGER MEGA MENÜ MODALI (referans widgetMegaMenu —
     body sonunda, widgetBottomNavigation'dan hemen önce)
     ============================================================ --}}
<div data-widget-type="widgetMegaMenu" data-mega-menu-panel
     class="menu-modal bg-[#384552] w-full h-screen sm:h-auto fixed top-0 left-0 px-4 z-600 hidden">
    <span class="bg-[#384552]/30 w-full h-full fixed left-0 top-0 menu-area-close z-1" data-menu-close></span>

    <div class="flex flex-col md:flex-row justify-between gap-4 items-start md:items-center py-4 mb-4 border-b border-gray-600 2xl:max-w-screen-xl mx-auto max-w-screen-lg relative">
        <a href="{{ \App\Support\Locale::route('home') }}" title="Bloomberght" class="block">
            <img src="{{ asset($settings['logo_white_path'] ?? '/brand/logo-white.svg') }}" alt="{{ $settings['logo_alt'] ?? 'Bloomberg HT' }}" class="w-auto h-8 sm:h-16">
        </a>
        <div class="relative w-full md:w-auto h-11 mr-8">
            <form action="{{ \App\Support\Locale::url('/arama') }}" method="get" role="search">
                <input type="search" name="q" class="search-input rounded-3xl w-full h-full pr-10 outline-none bg-[#384552] text-gray-400 border border-gray-600 py-2 px-4 text-xs sm:text-sm" placeholder="{{ $settings['search_menu_placeholder'] ?? 'Bloomberght\'de ara' }}">
                <button type="submit" aria-label="Search" class="search-button absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 cursor-pointer">
                    <i class="bloomberght-icon-font-search"></i>
                </button>
            </form>
        </div>
        <button type="button" class="absolute right-0 menu-button-close cursor-pointer text-white text-xl bloomberght-icon-font-close" data-menu-close aria-label="{{ __('ui.close') }}"></button>
    </div>

    <div class="w-full h-screen md:h-full overflow-auto max-w-screen-lg 2xl:max-w-screen-xl mx-auto bg-[#384552] text-white relative grid grid-cols-1 md:grid-cols-4 gap-4 pb-96 md:pb-8 relative">
        @foreach ($megaMenu ?? config('navigation.mega_menu', []) as $column)
            <div class="menu-list md:border-r border-gray-600 pr-4">
                <a href="{{ \App\Support\Locale::navUrl($column['url']) }}" class="inline-block mb-4 font-unna font-bold text-2xl">{{ \App\Support\Locale::navLabel($column['title']) }}</a>
                <ul class="text-base md:text-sm flex flex-col gap-2 md:flex-wrap lg:max-h-72">
                    @foreach ($column['links'] as $link)
                        <li class="flex items-center w-full lg:w-1/2">
                            <a href="{{ \App\Support\Locale::navUrl($link['url']) }}"
                               @if (str_starts_with($link['url'], 'http')) target="_blank" rel="noopener noreferrer" @endif>{{ \App\Support\Locale::navLabel($link['label']) }}</a>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endforeach
        <div class="menu-list md:border-r border-gray-600 pr-4">
            <span class="inline-block mb-4 font-unna font-bold text-2xl">{{ __('ui.language') }}</span>
            @include('front.partials.language-switcher')
        </div>
    </div>
</div>
