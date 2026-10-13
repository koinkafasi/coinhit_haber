@extends('admin.layouts.app')

@section('title', 'Reklamlar')
@section('heading', 'Reklamlar')

@section('content')
    <div class="card">
        <div class="toolbar between">
            <div class="muted">Aktif reklam yoksa ilgili alanlar sitede gizlenir (boş kutu görünmez).</div>
            <a href="{{ route('admin.advertisements.create') }}" class="btn accent">+ Yeni Reklam</a>
        </div>
        <table class="admin-table">
            <thead>
            <tr><th>Başlık</th><th>Konum</th><th>Reklamveren</th><th>Başlangıç</th><th>Bitiş</th><th>Durum</th><th></th></tr>
            </thead>
            <tbody>
            @forelse ($ads as $ad)
                <tr>
                    <td>{{ $ad->title }}</td>
                    <td>
                        {{ $ad->zoneLabel() ?? $ad->position }}
                        <div class="muted">zone {{ $ad->position }}</div>
                    </td>
                    <td>{{ $ad->advertiser_name ?: '-' }}</td>
                    <td>{{ $ad->starts_at?->format('d.m.Y') ?: '-' }}</td>
                    <td>{{ $ad->ends_at?->format('d.m.Y') ?: '-' }}</td>
                    <td>
                        @if (! $ad->is_active)
                            <span class="badge muted">Kapalı</span>
                        @elseif ($ad->starts_at?->isFuture() || $ad->ends_at?->isPast())
                            <span class="badge muted">Zaman dışı</span>
                        @else
                            <span class="badge ok">Aktif</span>
                        @endif
                    </td>
                    <td>
                        <div class="row-actions">
                            <a href="{{ route('admin.advertisements.edit', $ad) }}" class="btn ghost sm">Düzenle</a>
                            <form method="post" action="{{ route('admin.advertisements.destroy', $ad) }}" onsubmit="return confirm('Silinsin mi?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn danger sm">Sil</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="muted">Henüz reklam tanımlanmadı.</td></tr>
            @endforelse
            </tbody>
        </table>
        <div class="pagination-wrap">{{ $ads->links() }}</div>
    </div>
@endsection
