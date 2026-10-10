{{-- Paylaş çubuğu (fb, x, nsosyal, linkedin, whatsapp, mail) — spec §8 KAP detay --}}
@php
    $shareHref = $shareUrl ?? url()->current();
    $shareText = $shareTitle ?? '';
    $u = urlencode($shareHref);
    $t = urlencode($shareText);
@endphp
<ul class="share-bar" aria-label="{{ __('market.share') }}">
    <li>
        <a href="https://www.facebook.com/sharer/sharer.php?u={{ $u }}" target="_blank" rel="noopener noreferrer" aria-label="{{ __('market.share_facebook') }}">
            <x-icon name="facebook" />
        </a>
    </li>
    <li>
        <a href="https://twitter.com/intent/tweet?url={{ $u }}&text={{ $t }}" target="_blank" rel="noopener noreferrer" aria-label="{{ __('market.share_x') }}">
            <x-icon name="twitter-x" />
        </a>
    </li>
    <li>
        <a href="https://nsosyal.com/share?url={{ $u }}" target="_blank" rel="noopener noreferrer" aria-label="{{ __('market.share_nsosyal') }}">
            <x-icon name="nsosyal" />
        </a>
    </li>
    <li>
        <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ $u }}" target="_blank" rel="noopener noreferrer" aria-label="{{ __('market.share_linkedin') }}">
            <x-icon name="linkedin" />
        </a>
    </li>
    <li>
        <a href="https://wa.me/?text={{ $t }}%20{{ $u }}" target="_blank" rel="noopener noreferrer" aria-label="{{ __('market.share_whatsapp') }}">
            <x-icon name="whatsapp" />
        </a>
    </li>
    <li>
        <a href="mailto:?subject={{ $t }}&body={{ $u }}" aria-label="{{ __('market.share_mail') }}">
            <x-icon name="mail" />
        </a>
    </li>
    <li>
        <button type="button" class="share-copy" data-copy-link="{{ $shareHref }}" aria-label="{{ __('market.share_copy') }}">
            <x-icon name="copy" />
        </button>
    </li>
</ul>