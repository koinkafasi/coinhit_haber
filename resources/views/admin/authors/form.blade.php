@extends('admin.layouts.app')

@section('title', $author->exists ? 'Yazar Düzenle' : 'Yeni Yazar')
@section('heading', $author->exists ? 'Yazar Düzenle' : 'Yeni Yazar')

@section('content')
    <form method="post" action="{{ $author->exists ? route('admin.authors.update', $author) : route('admin.authors.store') }}" enctype="multipart/form-data">
        @csrf
        @if ($author->exists) @method('PUT') @endif

        <div class="card">
            <div class="form-grid">
                <div class="form-row">
                    <label for="name">Ad Soyad</label>
                    <input id="name" type="text" name="name" value="{{ old('name', $author->name) }}" required>
                </div>
                <div class="form-row">
                    <label for="slug">Slug</label>
                    <input id="slug" type="text" name="slug" value="{{ old('slug', $author->slug) }}" placeholder="otomatik">
                </div>
                <div class="form-row">
                    <label for="title">Köşe Başlığı</label>
                    <input id="title" type="text" name="title" value="{{ old('title', $author->title) }}">
                </div>
                <div class="form-row">
                    <label for="sort_order">Sıra</label>
                    <input id="sort_order" type="number" name="sort_order" value="{{ old('sort_order', $author->sort_order) }}">
                </div>
            </div>
            <div class="form-row">
                <label for="bio">Biyografi</label>
                <textarea id="bio" name="bio">{{ old('bio', $author->bio) }}</textarea>
            </div>
            <div class="form-grid">
                <div class="form-row">
                    <label for="avatar">Avatar URL</label>
                    <input id="avatar" type="text" name="avatar" value="{{ old('avatar', $author->avatar) }}">
                </div>
                <div class="form-row">
                    <label for="avatar_file">Avatar Yükle</label>
                    <input id="avatar_file" type="file" name="avatar_file" accept="image/*">
                </div>
            </div>
            <div class="checkbox">
                <input id="is_active" type="checkbox" name="is_active" value="1" @checked(old('is_active', $author->is_active))>
                <label for="is_active">Aktif</label>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn accent">{{ $author->exists ? 'Güncelle' : 'Oluştur' }}</button>
            <a href="{{ route('admin.authors.index') }}" class="btn ghost">Geri</a>
        </div>
    </form>
@endsection