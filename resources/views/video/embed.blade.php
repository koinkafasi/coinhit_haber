<!doctype html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $episode->display_title }}</title>
    <style>
        html, body { margin: 0; padding: 0; background: #000; height: 100%; }
        .embed-wrap { position: relative; width: 100%; height: 100vh; display: flex; align-items: center; justify-content: center; }
        .embed-wrap video, .embed-wrap img { width: 100%; height: 100%; object-fit: contain; }
    </style>
</head>
<body>
    <div class="embed-wrap">
        @if ($episode->video_url)
            <video controls autoplay preload="metadata"
                   poster="{{ asset($episode->thumbnail ?: '/img/placeholders/placeholder-1.svg') }}">
                <source src="{{ $episode->video_url }}" type="video/mp4">
            </video>
        @else
            <img src="{{ asset($episode->thumbnail ?: '/img/placeholders/placeholder-1.svg') }}" alt="">
        @endif
    </div>
</body>
</html>