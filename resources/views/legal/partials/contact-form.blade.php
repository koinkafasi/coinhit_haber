{{-- İletişim formu — Bölüm 14.4 (Seçiniz/Ad Soyad/Eposta/Telefon/Konu/Mesaj) --}}
@if (session('contact_status'))
    <p class="form-status" role="status">{{ session('contact_status') }}</p>
@endif

<form class="contact-form" method="post" action="{{ \App\Support\Locale::url('/bizeulasin') }}">
    @csrf

    <label for="contact-type">Konu Tipi</label>
    <select id="contact-type" name="type" required>
        <option value="">Seçiniz</option>
        @foreach ($subjects as $subject)
            <option value="{{ $subject }}" @selected(old('type') === $subject)>{{ $subject }}</option>
        @endforeach
    </select>
    @error('type')<span class="form-error">{{ $message }}</span>@enderror

    <label for="contact-name">Ad Soyad</label>
    <input id="contact-name" type="text" name="name" value="{{ old('name') }}" required>
    @error('name')<span class="form-error">{{ $message }}</span>@enderror

    <label for="contact-email">Eposta</label>
    <input id="contact-email" type="email" name="email" value="{{ old('email') }}" required>
    @error('email')<span class="form-error">{{ $message }}</span>@enderror

    <label for="contact-phone">Telefon</label>
    <input id="contact-phone" type="tel" name="phone" value="{{ old('phone') }}">
    @error('phone')<span class="form-error">{{ $message }}</span>@enderror

    <label for="contact-subject">Konu</label>
    <input id="contact-subject" type="text" name="subject" value="{{ old('subject') }}" required>
    @error('subject')<span class="form-error">{{ $message }}</span>@enderror

    <label for="contact-message">Mesaj</label>
    <textarea id="contact-message" name="message" rows="6" required>{{ old('message') }}</textarea>
    @error('message')<span class="form-error">{{ $message }}</span>@enderror

    <button type="submit">Mesajı Gönder</button>
</form>