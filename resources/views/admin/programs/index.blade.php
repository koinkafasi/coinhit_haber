@extends('admin.layouts.app')

@section('title', 'Programlar')
@section('heading', 'Programlar')

@section('content')
    <div class="card">
        <div class="toolbar end">
            <a href="{{ route('admin.programs.create') }}" class="btn accent">+ Yeni Program</a>
        </div>
        <table class="admin-table">
            <thead>
            <tr><th>Program</th><th>Sunucu</th><th>Saat</th><th>Bölüm</th><th>Durum</th><th></th></tr>
            </thead>
            <tbody>
            @forelse ($programs as $program)
                <tr>
                    <td>{{ $program->name }}</td>
                    <td>{{ $program->host }}</td>
                    <td>{{ $program->air_time }}</td>
                    <td>{{ $program->episodes_count }}</td>
                    <td><span class="badge {{ $program->is_active ? 'ok' : 'draft' }}">{{ $program->is_active ? 'aktif' : 'pasif' }}</span></td>
                    <td>
                        <div class="row-actions">
                            <a href="{{ route('admin.programs.edit', $program) }}" class="btn ghost sm">Düzenle</a>
                            <form method="post" action="{{ route('admin.programs.destroy', $program) }}" onsubmit="return confirm('Silinsin mi?')">
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
        <div class="pagination-wrap">{{ $programs->links() }}</div>
    </div>
@endsection