{!! '<?xml version="1.0" encoding="UTF-8"?>' !!}
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
        xmlns:video="http://www.google.com/schemas/sitemap-video/1.1">
@foreach ($urls as $item)
    <url>
        <loc>{{ $item['loc'] }}</loc>
        @if (! empty($item['lastmod']))
            <lastmod>{{ $item['lastmod'] }}</lastmod>
        @endif
        <video:video>
            <video:thumbnail_loc>{{ $item['thumbnail'] }}</video:thumbnail_loc>
            <video:title>{{ $item['title'] }}</video:title>
            <video:description>{{ $item['description'] }}</video:description>
            @if (! empty($item['content_loc']))
                <video:content_loc>{{ $item['content_loc'] }}</video:content_loc>
            @endif
            <video:player_loc>{{ $item['player_loc'] }}</video:player_loc>
            <video:duration>{{ $item['duration'] }}</video:duration>
            <video:publication_date>{{ $item['publication_date'] }}</video:publication_date>
        </video:video>
    </url>
@endforeach
</urlset>