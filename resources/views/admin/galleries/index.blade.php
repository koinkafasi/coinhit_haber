@extends('admin.layouts.app')

@section('title', 'Foto Haber')
@section('heading', 'Foto Haber / Galeriler')

@section('content')
    <div class="card">
        <div class="toolbar end">
            <a href="{{ route('admin.galleries.create') }}" class="btn accent">+ Yeni Galeri</a>
        </div>
        <table class="admin-table">
            <thead>
            <tr><th>Başlık</th><th>Kategori</th><th>Görsel</th><th>Yayın</th><th></th></tr>
            </thead>
            <tbody>
            @forelse ($galleries as $gallery)
                <tr>
                    <td>{{ $gallery->title }}</td>
                    <td>{{ $gallery->category?->name }}</td>
                    <td>{{ $gallery->items_count }}</td>
                    <td>{{ $gallery->published_at?->format('d.m.Y H:i') }}</td>
                    <td>
                        <div class="row-actions">
                            <a href="{{ route('admin.galleries.edit', $gallery) }}" class="btn ghost sm">Düzenle</a>
                            <form method="post" action="{{ route('admin.galleries.destroy', $gallery) }}" onsubmit="return confirm('Silinsin mi?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn danger sm">Sil</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="muted">Kayıt bulunamadı.</td></tr>
            @endforelse
            </tbody>
        </table>
        <div class="pagination-wrap">{{ $galleries->links() }}</div>
    </div>
@endsection