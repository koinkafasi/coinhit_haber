@php($settings = $siteSettings ?? [])
<div class="cookie-bar" data-type="cookiePolicy" data-cookie-bar hidden>
    <div class="cookie-inner container">
        <p class="cookie-text">
            {{ __('ui.cookie_text') }}
            <a href="{{ \App\Support\Locale::url('/cerez-politikasi') }}">{{ __('ui.cookie_policy') }}</a> {{ __('ui.cookie_and') }}
            <a href="{{ \App\Support\Locale::url('/aydinlatma-metni') }}">{{ __('ui.privacy_text') }}</a>{{ __('ui.cookie_note') }}
        </p>
        <div class="cookie-actions">
            <button type="button" class="cookie-accept" data-cookie-accept>{{ __('ui.cookie_accept') }}</button>
            <button type="button" class="cookie-close" data-cookie-close aria-label="{{ __('ui.close') }}">&times;</button>
        </div>
    </div>
</div>