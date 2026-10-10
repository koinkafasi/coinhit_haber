@extends('admin.layouts.app')

@section('title', $disclosure->exists ? 'KAP Düzenle' : 'Yeni KAP Bildirimi')
@section('heading', $disclosure->exists ? 'KAP Düzenle' : 'Yeni KAP Bildirimi')

@php
    $existing = $disclosure->exists ? $disclosure->fields->keyBy('key') : collect();
@endphp

@section('content')
    <form method="post" action="{{ $disclosure->exists ? route('admin.kap.update', $disclosure) : route('admin.kap.store') }}">
        @csrf
        @if ($disclosure->exists) @method('PUT') @endif

        <div class="card">
            <div class="form-grid">
                <div class="form-row">
                    <label for="company_name">Firma Adı</label>
                    <input id="company_name" type="text" name="company_name" value="{{ old('company_name', $disclosure->company_name) }}" required>
                </div>
                <div class="form-row">
                    <label for="company_code">Firma Kodu</label>
                    <input id="company_code" type="text" name="company_code" value="{{ old('company_code', $disclosure->company_code) }}" required>
                </div>
                <div class="form-row">
                    <label for="company_slug">Firma Slug</label>
                    <input id="company_slug" type="text" name="company_slug" value="{{ old('company_slug', $disclosure->company_slug) }}" placeholder="otomatik">
                </div>
                <div class="form-row">
                    <label for="kap_id">KAP ID</label>
                    <input id="kap_id" type="number" name="kap_id" value="{{ old('kap_id', $disclosure->kap_id) }}" placeholder="otomatik">
                </div>
                <div class="form-row">
                    <label for="title">Açıklama Başlığı</label>
                    <input id="title" type="text" name="title" value="{{ old('title', $disclosure->title) }}" required>
                </div>
                <div class="form-row">
                    <label for="category">Kategori</label>
                    <input id="category" type="text" name="category" value="{{ old('category', $disclosure->category) }}">
                </div>
                <div class="form-row">
                    <label for="published_at">Tarih-Saat</label>
                    <input id="published_at" type="datetime-local" name="published_at" value="{{ old('published_at', $disclosure->published_at?->format('Y-m-d\TH:i')) }}">
                </div>
            </div>
            <div class="form-row">
                <label for="summary">Özet</label>
                <textarea id="summary" name="summary" style="min-height:70px">{{ old('summary', $disclosure->summary) }}</textarea>
            </div>
            <div class="form-row">
                <label for="body">Açıklama Metni</label>
                <textarea id="body" name="body">{{ old('body', $disclosure->body) }}</textarea>
            </div>
        </div>

        <div class="card">
            <h2 style="margin-top:0;font-size:16px">TR / EN Alan Tablosu</h2>
            <table class="admin-table">
                <thead><tr><th>Alan</th><th>TR</th><th>EN</th></tr></thead>
                <tbody>
                @foreach ($dictionary as $key => [$labelTr, $labelEn])
                    @php($field = $existing->get($key))
                    <tr>
                        <td><code>{{ $key }}</code><div class="muted">{{ $labelTr }}</div></td>
                        <td><input type="text" name="fields[{{ $key }}][tr]" value="{{ old("fields.$key.tr", $field?->value_tr) }}"></td>
                        <td><input type="text" name="fields[{{ $key }}][en]" value="{{ old("fields.$key.en", $field?->value_en) }}"></td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn accent">{{ $disclosure->exists ? 'Güncelle' : 'Oluştur' }}</button>
            <a href="{{ route('admin.kap.index') }}" class="btn ghost">Geri</a>
        </div>
    </form>
@endsection