<!doctype html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex,nofollow">
    <title>Yönetim Girişi | Bloomberg HT</title>
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
</head>
<body class="admin-body">
<div class="login-wrap">
    <div class="login-card">
        <h1>Bloomberg HT Yönetim</h1>
        <p class="muted">İçerik yönetimi için giriş yapın.</p>

        @if ($errors->any())
            <div class="flash error">{{ $errors->first() }}</div>
        @endif

        <form method="post" action="{{ route('admin.login.attempt') }}">
            @csrf
            <div class="form-row">
                <label for="email">E-posta</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" autofocus required>
            </div>
            <div class="form-row">
                <label for="password">Parola</label>
                <input id="password" type="password" name="password" required>
            </div>
            <div class="checkbox">
                <input id="remember" type="checkbox" name="remember" value="1">
                <label for="remember" class="login-remember">Beni hatırla</label>
            </div>
            <button type="submit" class="btn accent btn-block">Giriş Yap</button>
        </form>
    </div>
</div>
</body>
</html>