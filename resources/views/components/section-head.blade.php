@props([
    'title',
    'href' => null,
    'allLabel' => 'TÜMÜ',
    'level' => 'h2',
    'id' => null,
    'class' => '',
])

<div class="section-head head-type1 {{ $class }}" @if ($id) id="{{ $id }}" @endif>
    <div class="section-head-inner">
        <{{ $level }} class="section-head-title font-unna font-bold text-3xl">{{ $title }}</{{ $level }}>
        @if ($href)
            <a class="section-head-all" href="{{ $href }}">{{ $allLabel }} &raquo;</a>
        @endif
    </div>
    <span class="section-head-bar" aria-hidden="true"></span>
</div>