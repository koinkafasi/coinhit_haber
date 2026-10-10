@extends('admin.layouts.app')

@section('title', 'KAP Bildirimleri')
@section('heading', 'KAP Bildirimleri')

@section('content')
    <div class="card">
        <div style="display:flex;justify-content:flex-end;margin-bottom:16px">
            <a href="{{ route('admin.kap.create') }}" class="btn accent">+ Yeni KAP Bildirimi</a>
        </div>
        <table class="admin-table">
            <thead>
            <tr><th>KAP ID</th><th>Firma</th><th>Başlık</th><th>Kategori</th><th>Yayın</th><th></th></tr>
            </thead>
            <tbody>
            @forelse ($disclosures as $disclosure)
                <tr>
                    <td>{{ $disclosure->kap_id }}</td>
                    <td>{{ $disclosure->company_name }} ({{ $disclosure->company_code }})</td>
                    <td>{{ $disclosure->title }}</td>
                    <td>{{ $disclosure->category }}</td>
                    <td>{{ $disclosure->published_at?->format('d.m.Y H:i') }}</td>
                    <td>
                        <div class="row-actions">
                            <a href="{{ route('admin.kap.edit', $disclosure) }}" class="btn ghost sm">Düzenle</a>
                            <form method="post" action="{{ route('admin.kap.destroy', $disclosure) }}" onsubmit="return confirm('Silinsin mi?')">
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
        <div class="pagination-wrap">{{ $disclosures->links() }}</div>
    </div>
@endsection