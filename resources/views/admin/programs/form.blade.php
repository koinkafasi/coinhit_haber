@extends('admin.layouts.app')

@section('title', $program->exists ? 'Program Düzenle' : 'Yeni Program')
@section('heading', $program->exists ? 'Program Düzenle' : 'Yeni Program')

@section('content')
    <form method="post" action="{{ $program->exists ? route('admin.programs.update', $program) : route('admin.programs.store') }}" enctype="multipart/form-data">
        @csrf
        @if ($program->exists) @method('PUT') @endif

        <div class="card">
            <div class="form-grid">
                <div class="form-row">
                    <label for="name">Program Adı</label>
                    <input id="name" type="text" name="name" value="{{ old('name', $program->name) }}" required>
                </div>
                <div class="form-row">
                    <label for="slug">Slug</label>
                    <input id="slug" type="text" name="slug" value="{{ old('slug', $program->slug) }}" placeholder="otomatik">
                </div>
                <div class="form-row">
                    <label for="host">Sunucu</label>
                    <input id="host" type="text" name="host" value="{{ old('host', $program->host) }}">
                </div>
                <div class="form-row">
                    <label for="air_time">Yayın Saati (HH:MM)</label>
                    <input id="air_time" type="text" name="air_time" value="{{ old('air_time', $program->air_time) }}" placeholder="19:00">
                </div>
                <div class="form-row">
                    <label for="day_of_week">Gün</label>
                    <select id="day_of_week" name="day_of_week">
                        <option value="">—</option>
                        @foreach (\App\Models\BroadcastSchedule::DAYS as $num => $name)
                            <option value="{{ $num }}" @selected((int) old('day_of_week', $program->day_of_week) === $num)>{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-row">
                    <label for="sort_order">Sıra</label>
                    <input id="sort_order" type="number" name="sort_order" value="{{ old('sort_order', $program->sort_order) }}">
                </div>
            </div>
            <div class="form-row">
                <label for="description">Açıklama</label>
                <textarea id="description" name="description">{{ old('description', $program->description) }}</textarea>
            </div>
            <div class="form-grid">
                <div class="form-row">
                    <label for="thumbnail">Thumbnail URL (360x203)</label>
                    <input id="thumbnail" type="text" name="thumbnail" value="{{ old('thumbnail', $program->thumbnail) }}">
                </div>
                <div class="form-row">
                    <label for="thumbnail_file">Thumbnail Yükle</label>
                    <input id="thumbnail_file" type="file" name="thumbnail_file" accept="image/*">
                </div>
            </div>
            <div class="checkbox">
                <input id="is_live" type="checkbox" name="is_live" value="1" @checked(old('is_live', $program->is_live))>
                <label for="is_live">Şu an canlı yayında</label>
            </div>
            <div class="checkbox">
                <input id="is_active" type="checkbox" name="is_active" value="1" @checked(old('is_active', $program->is_active))>
                <label for="is_active">Aktif</label>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn accent">{{ $program->exists ? 'Güncelle' : 'Oluştur' }}</button>
            <a href="{{ route('admin.programs.index') }}" class="btn ghost">Geri</a>
        </div>
    </form>

    @if ($program->exists)
        <div class="card">
            <h2 class="card-title">Bölümler</h2>

            <form method="post" action="{{ route('admin.programs.episodes.store', $program) }}" enctype="multipart/form-data" class="stack-form">
                @csrf
                <div class="form-grid">
                    <div class="form-row">
                        <label for="ep_title">Başlık</label>
                        <input id="ep_title" type="text" name="title" required>
                    </div>
                    <div class="form-row">
                        <label for="ep_guest">Konuk</label>
                        <input id="ep_guest" type="text" name="guest">
                    </div>
                    <div class="form-row">
                        <label for="ep_duration">Süre (HH:MM:SS / M:SS)</label>
                        <input id="ep_duration" type="text" name="duration" placeholder="42:30">
                    </div>
                    <div class="form-row">
                        <label for="ep_air_at">Yayın Zamanı</label>
                        <input id="ep_air_at" type="datetime-local" name="air_at">
                    </div>
                    <div class="form-row">
                        <label for="ep_thumb">Thumbnail Yükle</label>
                        <input id="ep_thumb" type="file" name="thumbnail_file" accept="image/*">
                    </div>
                    <div class="form-row">
                        <label for="ep_video">Video URL</label>
                        <input id="ep_video" type="text" name="video_url">
                    </div>
                </div>
                <button type="submit" class="btn">Bölüm Ekle</button>
            </form>

            <table class="admin-table">
                <thead><tr><th>Başlık</th><th>Konuk</th><th>Süre</th><th>Yayın</th><th></th></tr></thead>
                <tbody>
                @forelse ($program->episodes as $episode)
                    <tr>
                        <td>{{ $episode->display_title }}</td>
                        <td>{{ $episode->guest }}</td>
                        <td>{{ $episode->duration }}</td>
                        <td>{{ $episode->air_at?->format('d.m.Y H:i') }}</td>
                        <td>
                            <form method="post" action="{{ route('admin.programs.episodes.destroy', [$program, $episode]) }}" onsubmit="return confirm('Silinsin mi?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn danger sm">Sil</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="muted">Bölüm yok.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    @endif
@endsection