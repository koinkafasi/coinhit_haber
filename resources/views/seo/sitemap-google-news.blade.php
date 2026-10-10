{!! '<?xml version="1.0" encoding="UTF-8"?>' !!}
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
        xmlns:news="http://www.google.com/schemas/sitemap-news/0.9"
        xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">
@foreach ($urls as $item)
    <url>
        <loc>{{ $item['loc'] }}</loc>
        <lastmod>{{ $item['lastmod'] }}</lastmod>
        <image:image>
            <image:loc>{{ $item['image'] }}</image:loc>
            <image:title>{{ $item['image_title'] }}</image:title>
            <image:caption>{{ $item['image_caption'] }}</image:caption>
        </image:image>
        <news:news>
            <news:publication>
                <news:name>BloombergHT</news:name>
                <news:language>tr</news:language>
            </news:publication>
            <news:publication_date>{{ $item['publication_date'] }}</news:publication_date>
            <news:title>{{ $item['title'] }}</news:title>
        </news:news>
    </url>
@endforeach
</urlset>