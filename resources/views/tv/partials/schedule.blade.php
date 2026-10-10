<div class="tv-schedule" data-widget-type="broadcast-days">
    <div class="broadcast-day-tabs tab-menu" role="tablist" aria-label="Yayın akışı günleri">
        @foreach ($days as $day)
            <button type="button" role="tab"
                    class="tab-button broadcast-day-tab {{ $day['is_today'] ? 'is-active' : '' }}"
                    data-tab="day-{{ $day['day'] }}"
                    aria-selected="{{ $day['is_today'] ? 'true' : 'false' }}">{{ $day['name'] }}</button>
        @endforeach
    </div>

    <div class="broadcast-day-panels">
        @foreach ($days as $day)
            <div class="tab-content broadcast-day-panel {{ $day['is_today'] ? '' : 'hidden' }}"
                 data-id="day-{{ $day['day'] }}" data-day="{{ $day['day'] }}">
                <ol class="broadcast-list broadcast-list-rich">
                    @foreach ($day['rows'] as $row)
                        <li class="broadcast-row">
                            <figure class="broadcast-thumb">
                                <x-lazy-image :src="asset($row->program?->thumbnail ?: '/img/placeholders/placeholder-1.svg')"
                                              alt="" contract="video_thumb" class="img-contract" />
                            </figure>
                            <time class="broadcast-time" datetime="{{ $row->air_time }}">{{ $row->air_time }}</time>
                            <div class="broadcast-info">
                                <a class="broadcast-name font-unna font-bold" href="{{ $row->url }}">{{ $row->program_name }}</a>
                                @if ($row->description)
                                    <p class="broadcast-desc">{{ $row->description }}</p>
                                @endif
                            </div>
                        </li>
                    @endforeach
                </ol>

                <div class="broadcast-actions">
                    <a class="tv-action-link" href="{{ \App\Support\Locale::url('/tv/programlar') }}">Tüm Programlar</a>
                    <a class="tv-action-link" href="{{ \App\Support\Locale::url('/tv/frekans-listesi') }}">Frekans Listesi</a>
                </div>
            </div>
        @endforeach
    </div>
</div>