@extends('admin.layouts.app')

@section('title', $news->exists ? 'Haber Düzenle' : 'Yeni Haber')
@section('heading', $news->exists ? 'Haber Düzenle' : 'Yeni Haber')

@section('content')
    <form method="post" action="{{ $news->exists ? route('admin.news.update', $news) : route('admin.news.store') }}" enctype="multipart/form-data">
        @csrf
        @if ($news->exists) @method('PUT') @endif

        <div class="card">
            <div class="form-row">
                <label for="title">Başlık</label>
                <input id="title" type="text" name="title" value="{{ old('title', $news->title) }}" required>
            </div>
            <div class="form-grid">
                <div class="form-row">
                    <label for="slug">Slug</label>
                    <input id="slug" type="text" name="slug" value="{{ old('slug', $news->slug) }}" placeholder="otomatik">
                </div>
                <div class="form-row">
                    <label for="news_id">News ID</label>
                    <input id="news_id" type="number" name="news_id" value="{{ old('news_id', $news->news_id) }}" placeholder="otomatik">
                </div>
                <div class="form-row">
                    <label for="category_id">Kategori</label>
                    <select id="category_id" name="category_id" required>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" @selected((int) old('category_id', $news->category_id) === $category->id)>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-row">
                    <label for="author_id">Yazar</label>
                    <select id="author_id" name="author_id">
                        <option value="">—</option>
                        @foreach ($authors as $author)
                            <option value="{{ $author->id }}" @selected((int) old('author_id', $news->author_id) === $author->id)>{{ $author->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="form-row">
                <label for="spot">Spot / Lead</label>
                <textarea id="spot" name="spot" class="textarea-spot">{{ old('spot', $news->spot) }}</textarea>
            </div>
            <div class="form-row">
                <label for="body">Gövde</label>
                <textarea id="body" name="body">{{ old('body', $news->body) }}</textarea>
                <p class="muted">Paragraflar boş satırla ayrılır.</p>
            </div>
        </div>

        <div class="card">
            <div class="form-grid">
                <div class="form-row">
                    <label for="image">Hero Görsel URL</label>
                    <input id="image" type="text" name="image" value="{{ old('image', $news->image) }}">
                </div>
                <div class="form-row">
                    <label for="image_file">Hero Görsel Yükle</label>
                    <input id="image_file" type="file" name="image_file" accept="image/*">
                </div>
                <div class="form-row">
                    <label for="image_og">OG Görsel (960x540)</label>
                    <input id="image_og" type="text" name="image_og" value="{{ old('image_og', $news->image_og) }}">
                </div>
                <div class="form-row">
                    <label for="source">Kaynak</label>
                    <input id="source" type="text" name="source" value="{{ old('source', $news->source) }}">
                </div>
            </div>
            <div class="form-grid">
                <div class="form-row">
                    <label for="tags">Etiketler (virgülle)</label>
                    <input id="tags" type="text" name="tags" value="{{ old('tags', implode(', ', (array) $news->tags)) }}">
                </div>
                <div class="form-row">
                    <label for="keywords">Inline Keyword (virgülle)</label>
                    <input id="keywords" type="text" name="keywords" value="{{ old('keywords', implode(', ', (array) $news->keywords)) }}">
                </div>
            </div>
        </div>

        <div class="card">
            <div class="form-grid">
                <div class="form-row">
                    <label for="status">Durum</label>
                    <select id="status" name="status">
                        <option value="published" @selected(old('status', $news->status) === 'published')>published</option>
                        <option value="draft" @selected(old('status', $news->status) === 'draft')>draft</option>
                    </select>
                </div>
                <div class="form-row">
                    <label for="published_at">Yayın Tarihi</label>
                    <input id="published_at" type="datetime-local" name="published_at" value="{{ old('published_at', $news->published_at?->format('Y-m-d\TH:i')) }}">
                </div>
                <div class="form-row">
                    <label for="modified_at">Güncelleme</label>
                    <input id="modified_at" type="datetime-local" name="modified_at" value="{{ old('modified_at', $news->modified_at?->format('Y-m-d\TH:i')) }}">
                </div>
                <div class="form-row">
                    <label for="video_url">Video URL</label>
                    <input id="video_url" type="text" name="video_url" value="{{ old('video_url', $news->video_url) }}">
                </div>
            </div>
            <div class="checkbox">
                <input id="is_breaking" type="checkbox" name="is_breaking" value="1" @checked(old('is_breaking', $news->is_breaking))>
                <label for="is_breaking">Son dakika</label>
            </div>
            <div class="checkbox">
                <input id="is_featured" type="checkbox" name="is_featured" value="1" @checked(old('is_featured', $news->is_featured))>
                <label for="is_featured">Öne çıkan</label>
            </div>
            <div class="checkbox">
                <input id="has_video" type="checkbox" name="has_video" value="1" @checked(old('has_video', $news->has_video))>
                <label for="has_video">Video içeriyor</label>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn accent">{{ $news->exists ? 'Güncelle' : 'Oluştur' }}</button>
            <a href="{{ route('admin.news.index') }}" class="btn ghost">Geri</a>
        </div>
    </form>
@endsection