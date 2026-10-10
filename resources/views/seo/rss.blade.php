{!! '<?xml version="1.0" encoding="UTF-8"?>' !!}
<rss version="2.0"
     xmlns:media="http://search.yahoo.com/mrss/"
     xmlns:atom="http://www.w3.org/2005/Atom">
    <channel>
        <title>{{ $title }}</title>
        <link>{{ $siteUrl }}</link>
        <description>{{ $description }}</description>
        <language>{{ app()->getLocale() === 'en' ? 'en-US' : 'tr-TR' }}</language>
        <lastBuildDate>{{ ($items->first()?->modified_at ?? $items->first()?->published_at ?? now())->toRfc2822String() }}</lastBuildDate>
        <atom:link href="{{ $selfUrl }}" rel="self" type="application/rss+xml"/>
        @foreach ($items as $item)
            @php($image = $item->image_og ?: ($item->image ?: '/img/og-default.png'))
            <item>
                <title>{{ $item->title }}</title>
                <link>{{ $item->url }}</link>
                <guid isPermaLink="true">{{ $item->url }}</guid>
                <pubDate>{{ ($item->published_at ?? $item->created_at)->toRfc2822String() }}</pubDate>
                @if ($item->category)
                    <category>{{ $item->category->name }}</category>
                @endif
                @if ($item->author)
                    <author>editor@bloomberght.com ({{ $item->author->name }})</author>
                @endif
                <description><![CDATA[{!! strip_tags((string) $item->spot) !!}]]></description>
                @if ($withMedia)
                    <media:content url="{{ url($image) }}" type="image/jpeg" width="1280" height="720">
                        <media:title type="plain">{{ $item->title }}</media:title>
                        <media:description type="plain">{{ strip_tags((string) $item->spot) }}</media:description>
                    </media:content>
                @endif
            </item>
        @endforeach
    </channel>
</rss>