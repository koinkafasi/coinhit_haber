@extends('admin.layouts.app')

@section('title', $ad->exists ? 'Reklam Düzenle' : 'Yeni Reklam')
@section('heading', $ad->exists ? 'Reklam Düzenle' : 'Yeni Reklam')

@section('content')
    <form method="post" action="{{ $ad->exists ? route('admin.advertisements.update', $ad) : route('admin.advertisements.store') }}" enctype="multipart/form-data">
        @csrf
        @if ($ad->exists) @method('PUT') @endif

        <div class="card">
            <h2 class="card-title">Konum ve İçerik</h2>
            <div class="form-grid">
                <div class="form-row">
                    <label for="title">Reklam Adı</label>
                    <input id="title" type="text" name="title" value="{{ old('title', $ad->title) }}" required>
                </div>
                <div class="form-row">
                    <label for="position">Konum (zone)</label>
                    <select id="position" name="position">
                        @foreach (\App\Models\Advertisement::zones() as $zoneId => $zoneLabel)
                            <option value="{{ $zoneId }}" @selected(old('position', $ad->position) == $zoneId)>
                                {{ $zoneLabel }} (zone {{ $zoneId }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-row">
                    <label for="advertiser_name">Reklamveren</label>
                    <input id="advertiser_name" type="text" name="advertiser_name" value="{{ old('advertiser_name', $ad->advertiser_name) }}">
                </div>
                <div class="form-row">
                    <label for="link_url">Bağlantı (tıklanınca)</label>
                    <input id="link_url" type="url" name="link_url" value="{{ old('link_url', $ad->link_url) }}" placeholder="https://">
                </div>
                <div class="form-row">
                    <label for="starts_at">Başlangıç</label>
                    <input id="starts_at" type="date" name="starts_at" value="{{ old('starts_at', $ad->starts_at?->format('Y-m-d')) }}">
                </div>
                <div class="form-row">
                    <label for="ends_at">Bitiş</label>
                    <input id="ends_at" type="date" name="ends_at" value="{{ old('ends_at', $ad->ends_at?->format('Y-m-d')) }}">
                </div>
            </div>

            <div class="form-row">
                <label>Görsel</label>
                @if ($ad->image_path)
                    <div class="img-preview-wrap">
                        <img src="{{ $ad->imageUrl() }}" alt="" class="img-preview">
                    </div>
                @endif
                <input type="file" name="image" accept="image/*">
                <div class="muted">Görsel veya aşağıdaki ham kod kullanabilirsiniz (ikisi de verilirse kod önceliklidir).</div>
            </div>

            <div class="form-row">
                <label for="code">Reklam Kodu / HTML</label>
                <textarea id="code" name="code" class="textarea-code" placeholder="ör. <script src=...></script> veya DFP/prebid etiketi">{{ old('code', $ad->code) }}</textarea>
            </div>

            <div class="form-row">
                <label class="checkbox">
                    <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $ad->is_active))>
                    Aktif
                </label>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn accent">{{ $ad->exists ? 'Güncelle' : 'Oluştur' }}</button>
            <a href="{{ route('admin.advertisements.index') }}" class="btn ghost">Geri</a>
        </div>
    </form>
@endsection
