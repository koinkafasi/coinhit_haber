@extends('admin.layouts.app')

@section('title', 'Yazarlar')
@section('heading', 'Yazarlar / Görüş')

@section('content')
    <div class="card">
        <div style="display:flex;justify-content:flex-end;margin-bottom:16px">
            <a href="{{ route('admin.authors.create') }}" class="btn accent">+ Yeni Yazar</a>
        </div>
        <table class="admin-table">
            <thead>
            <tr><th>Ad</th><th>Köşe</th><th>Slug</th><th>Haber</th><th>Durum</th><th></th></tr>
            </thead>
            <tbody>
            @forelse ($authors as $author)
                <tr>
                    <td>{{ $author->name }}</td>
                    <td>{{ $author->title }}</td>
                    <td>{{ $author->slug }}</td>
                    <td>{{ $author->news_count }}</td>
                    <td><span class="badge {{ $author->is_active ? 'ok' : 'draft' }}">{{ $author->is_active ? 'aktif' : 'pasif' }}</span></td>
                    <td>
                        <div class="row-actions">
                            <a href="{{ route('admin.authors.edit', $author) }}" class="btn ghost sm">Düzenle</a>
                            <form method="post" action="{{ route('admin.authors.destroy', $author) }}" onsubmit="return confirm('Silinsin mi?')">
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
        <div class="pagination-wrap">{{ $authors->links() }}</div>
    </div>
@endsection