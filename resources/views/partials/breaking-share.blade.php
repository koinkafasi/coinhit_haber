{{-- Son dakika satırı paylaş çubuğu: 6 servis + bb.ht kısa link kopyala (spec §8 / §15.11). --}}
@php
    $shareUrl = $item->news?->url ?? url()->current();
    $shareTitle = $item->title;
    $u = urlencode($shareUrl);
    $t = urlencode($shareTitle);
    $short = $item->shortlink;
    $shortUrl = $short !== '' ? 'https://'.$short : $shareUrl;
@endphp
<ul class="breaking-share share-bar" aria-label="{{ __('ui.share') }}">
    <li><a href="https://www.facebook.com/sharer/sharer.php?u={{ $u }}" target="_blank" rel="noopener noreferrer" aria-label="Facebook"><x-icon name="facebook" /></a></li>
    <li><a href="https://twitter.com/intent/tweet?url={{ $u }}&text={{ $t }}" target="_blank" rel="noopener noreferrer" aria-label="X"><x-icon name="twitter-x" /></a></li>
    <li><a href="https://nsosyal.com/share?url={{ $u }}" target="_blank" rel="noopener noreferrer" aria-label="nSosyal"><x-icon name="nsosyal" /></a></li>
    <li><a href="https://www.linkedin.com/sharing/share-offsite/?url={{ $u }}" target="_blank" rel="noopener noreferrer" aria-label="LinkedIn"><x-icon name="linkedin" /></a></li>
    <li><a href="https://wa.me/?text={{ $t }}%20{{ $u }}" target="_blank" rel="noopener noreferrer" aria-label="WhatsApp"><x-icon name="whatsapp" /></a></li>
    <li><a href="mailto:?subject={{ $t }}&body={{ $u }}" aria-label="E-posta"><x-icon name="mail" /></a></li>
    @if ($short !== '')
        <li>
            <button type="button" class="share-copy shortlink-copy" data-copy-link="{{ $shortUrl }}" aria-label="{{ __('ui.copy_short_link') }}">
                <x-icon name="copy" /> <span class="shortlink-text">{{ $short }}</span>
            </button>
        </li>
    @endif
</ul>