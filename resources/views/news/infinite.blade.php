{{-- Sonsuz kaydırma HTML parçası (spec §3, §9, §15.15) --}}
@include('news.partials.article', [
    'news' => $news,
    'categoryUrl' => $categoryUrl,
    'index' => request()->integer('index', 1),
    'isInfinite' => true,
    'eager' => false,
])