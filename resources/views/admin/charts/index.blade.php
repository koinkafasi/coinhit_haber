@extends('admin.layouts.app')

@section('title', 'Grafik Arşivi')
@section('heading', 'Grafik Arşivi')

@section('content')
    <div class="card">
        <div style="display:flex;justify-content:flex-end;margin-bottom:16px">
            <a href="{{ route('admin.charts.create') }}" class="btn accent">+ Yeni Grafik</a>
        </div>
        <table class="admin-table">
            <thead>
            <tr><th>ID</th><th>Başlık</th><th>Kategori</th><th>Yayın</th><th></th></tr>
            </thead>
            <tbody>
            @forelse ($charts as $chart)
                <tr>
                    <td>{{ $chart->chart_id }}</td>
                    <td>{{ $chart->title }}</td>
                    <td><span class="badge">{{ $chart->category }}</span></td>
                    <td>{{ $chart->published_at?->format('d.m.Y H:i') }}</td>
                    <td>
                        <div class="row-actions">
                            <a href="{{ route('admin.charts.edit', $chart) }}" class="btn ghost sm">Düzenle</a>
                            <form method="post" action="{{ route('admin.charts.destroy', $chart) }}" onsubmit="return confirm('Silinsin mi?')">
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
        <div class="pagination-wrap">{{ $charts->links() }}</div>
    </div>
@endsection