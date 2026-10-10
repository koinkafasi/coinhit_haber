{{-- KAP bildirim listesi (ters kronolojik) — spec §8 --}}
<ul class="kap-list">
    @forelse ($disclosures as $disclosure)
        <li>
            <a href="{{ $disclosure->url }}">
                <span class="kap-company">{{ $disclosure->company_code }} · {{ $disclosure->company_name }}</span>
                <span class="kap-title line-clamp-1">{{ $disclosure->title }}</span>
                <time class="kap-time">{{ $disclosure->published_at?->format('d.m.Y H:i') }}</time>
            </a>
        </li>
    @empty
        <li class="kap-empty">{{ __('market.no_kap') }}</li>
    @endforelse
</ul>