@extends('admin.layouts.app')

@section('title', $chart->exists ? 'Grafik Düzenle' : 'Yeni Grafik')
@section('heading', $chart->exists ? 'Grafik Düzenle' : 'Yeni Grafik')

@section('content')
    <form method="post" action="{{ $chart->exists ? route('admin.charts.update', $chart) : route('admin.charts.store') }}" enctype="multipart/form-data">
        @csrf
        @if ($chart->exists) @method('PUT') @endif

        <div class="card">
            <div class="form-grid">
                <div class="form-row">
                    <label for="title">Başlık</label>
                    <input id="title" type="text" name="title" value="{{ old('title', $chart->title) }}" required>
                </div>
                <div class="form-row">
                    <label for="slug">Slug</label>
                    <input id="slug" type="text" name="slug" value="{{ old('slug', $chart->slug) }}" placeholder="otomatik">
                </div>
                <div class="form-row">
                    <label for="category">Kategori</label>
                    <select id="category" name="category">
                        @foreach ($categories as $category)
                            <option value="{{ $category }}" @selected(old('category', $chart->category) === $category)>{{ $category }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-row">
                    <label for="chart_id">Grafik ID</label>
                    <input id="chart_id" type="number" name="chart_id" value="{{ old('chart_id', $chart->chart_id) }}" placeholder="otomatik">
                </div>
                <div class="form-row">
                    <label for="published_at">Yayın Tarihi</label>
                    <input id="published_at" type="datetime-local" name="published_at" value="{{ old('published_at', $chart->published_at?->format('Y-m-d\TH:i')) }}">
                </div>
            </div>
            <div class="form-row">
                <label for="summary">Özet</label>
                <textarea id="summary" name="summary">{{ old('summary', $chart->summary) }}</textarea>
            </div>
            <div class="form-grid">
                <div class="form-row">
                    <label for="image">Görsel URL</label>
                    <input id="image" type="text" name="image" value="{{ old('image', $chart->image) }}">
                </div>
                <div class="form-row">
                    <label for="image_file">Görsel Yükle</label>
                    <input id="image_file" type="file" name="image_file" accept="image/*">
                </div>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn accent">{{ $chart->exists ? 'Güncelle' : 'Oluştur' }}</button>
            <a href="{{ route('admin.charts.index') }}" class="btn ghost">Geri</a>
        </div>
    </form>
@endsection