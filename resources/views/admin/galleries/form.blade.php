@extends('admin.layouts.app')

@section('title', $gallery->exists ? 'Galeri Düzenle' : 'Yeni Galeri')
@section('heading', $gallery->exists ? 'Galeri Düzenle' : 'Yeni Galeri')

@section('content')
    <form method="post" action="{{ $gallery->exists ? route('admin.galleries.update', $gallery) : route('admin.galleries.store') }}" enctype="multipart/form-data">
        @csrf
        @if ($gallery->exists) @method('PUT') @endif

        <div class="card">
            <div class="form-grid">
                <div class="form-row">
                    <label for="title">Başlık</label>
                    <input id="title" type="text" name="title" value="{{ old('title', $gallery->title) }}" required>
                </div>
                <div class="form-row">
                    <label for="slug">Slug</label>
                    <input id="slug" type="text" name="slug" value="{{ old('slug', $gallery->slug) }}" placeholder="otomatik">
                </div>
                <div class="form-row">
                    <label for="category_id">Kategori</label>
                    <select id="category_id" name="category_id">
                        <option value="">—</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" @selected((int) old('category_id', $gallery->category_id) === $category->id)>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-row">
                    <label for="published_at">Yayın Tarihi</label>
                    <input id="published_at" type="datetime-local" name="published_at" value="{{ old('published_at', $gallery->published_at?->format('Y-m-d\TH:i')) }}">
                </div>
            </div>
            <div class="form-row">
                <label for="summary">Özet</label>
                <textarea id="summary" name="summary">{{ old('summary', $gallery->summary) }}</textarea>
            </div>
            <div class="form-grid">
                <div class="form-row">
                    <label for="cover">Kapak URL</label>
                    <input id="cover" type="text" name="cover" value="{{ old('cover', $gallery->cover) }}">
                </div>
                <div class="form-row">
                    <label for="cover_file">Kapak Yükle</label>
                    <input id="cover_file" type="file" name="cover_file" accept="image/*">
                </div>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn accent">{{ $gallery->exists ? 'Güncelle' : 'Oluştur' }}</button>
            <a href="{{ route('admin.galleries.index') }}" class="btn ghost">Geri</a>
        </div>
    </form>

    @if ($gallery->exists)
        <div class="card">
            <h2 style="margin-top:0;font-size:16px">Görseller ({{ $gallery->items->count() }})</h2>

            <form method="post" action="{{ route('admin.galleries.items.store', $gallery) }}" enctype="multipart/form-data" style="margin-bottom:20px">
                @csrf
                <div class="form-grid">
                    <div class="form-row">
                        <label for="item_image">Görsel Yükle</label>
                        <input id="item_image" type="file" name="image_file" accept="image/*">
                    </div>
                    <div class="form-row">
                        <label for="item_caption">Altyazı</label>
                        <input id="item_caption" type="text" name="caption">
                    </div>
                    <div class="form-row">
                        <label for="item_sort">Sıra</label>
                        <input id="item_sort" type="number" name="sort_order">
                    </div>
                </div>
                <button type="submit" class="btn">Görsel Ekle</button>
            </form>

            <table class="admin-table">
                <thead><tr><th>Görsel</th><th>Altyazı</th><th>Sıra</th><th></th></tr></thead>
                <tbody>
                @forelse ($gallery->items as $item)
                    <tr>
                        <td>{{ $item->image }}</td>
                        <td>{{ $item->caption }}</td>
                        <td>{{ $item->sort_order }}</td>
                        <td>
                            <form method="post" action="{{ route('admin.galleries.items.destroy', [$gallery, $item]) }}" onsubmit="return confirm('Silinsin mi?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn danger sm">Sil</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="muted">Görsel yok.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    @endif
@endsection