@extends('admin.layouts.app')

@section('title', 'Haberler')
@section('heading', 'Haberler')

@section('content')
    <div class="card">
        <div class="toolbar between">
            <form method="get" action="{{ route('admin.news.index') }}" class="toolbar-form">
                <input type="text" name="q" value="{{ $q }}" placeholder="Başlıkta ara..." class="search-input">
                <button type="submit" class="btn ghost">Ara</button>
            </form>
            <a href="{{ route('admin.news.create') }}" class="btn accent">+ Yeni Haber</a>
        </div>

        <table class="admin-table">
            <thead>
            <tr>
                <th>ID</th>
                <th>Başlık</th>
                <th>Kategori</th>
                <th>Durum</th>
                <th>Yayın</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            @forelse ($news as $item)
                <tr>
                    <td>{{ $item->news_id }}</td>
                    <td>
                        {{ $item->title }}
                        @if ($item->is_breaking) <span class="badge">SON DAKİKA</span> @endif
                        @if ($item->is_featured) <span class="badge ok">ÖNE ÇIKAN</span> @endif
                    </td>
                    <td>{{ $item->category?->name }}</td>
                    <td><span class="badge {{ $item->status === 'published' ? 'ok' : 'draft' }}">{{ $item->status }}</span></td>
                    <td>{{ $item->published_at?->format('d.m.Y H:i') }}</td>
                    <td>
                        <div class="row-actions">
                            <a href="{{ route('admin.news.edit', $item) }}" class="btn ghost sm">Düzenle</a>
                            <form method="post" action="{{ route('admin.news.destroy', $item) }}" onsubmit="return confirm('Silinsin mi?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn danger sm">Sil</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="muted">Kayıt bulunamadı.</td></tr>
            @endforelse
            </tbody>
        </table>

        <div class="pagination-wrap">{{ $news->links() }}</div>
    </div>
@endsection