<!doctype html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex,nofollow">
    <title>@yield('title', 'Yönetim') | Bloomberg HT</title>
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
</head>
<body class="admin-body">
@php($route = request()->route()?->getName() ?? '')
<div class="admin-shell">
    <aside class="admin-sidebar">
        <div class="brand">Bloomberg HT · Yönetim</div>
        <nav class="admin-nav">
            <a href="{{ route('admin.dashboard') }}" class="{{ $route === 'admin.dashboard' ? 'active' : '' }}">Panel</a>
            <a href="{{ route('admin.news.index') }}" class="{{ str_starts_with($route, 'admin.news') ? 'active' : '' }}">Haberler</a>
            <a href="{{ route('admin.authors.index') }}" class="{{ str_starts_with($route, 'admin.authors') ? 'active' : '' }}">Yazarlar / Görüş</a>
            <a href="{{ route('admin.programs.index') }}" class="{{ str_starts_with($route, 'admin.programs') ? 'active' : '' }}">Programlar</a>
            <a href="{{ route('admin.galleries.index') }}" class="{{ str_starts_with($route, 'admin.galleries') ? 'active' : '' }}">Foto Haber</a>
            <a href="{{ route('admin.kap.index') }}" class="{{ str_starts_with($route, 'admin.kap') ? 'active' : '' }}">KAP</a>
            <a href="{{ route('admin.charts.index') }}" class="{{ str_starts_with($route, 'admin.charts') ? 'active' : '' }}">Grafik Arşivi</a>
        </nav>
    </aside>

    <div class="admin-main">
        <div class="admin-topbar">
            <h1>@yield('heading', 'Panel')</h1>
            <div class="user">
                <span>{{ auth()->user()?->name }}</span>
                <form method="post" action="{{ route('admin.logout') }}">
                    @csrf
                    <button type="submit" class="btn ghost sm">Çıkış</button>
                </form>
            </div>
        </div>

        <div class="admin-content">
            @if (session('status'))
                <div class="flash">{{ session('status') }}</div>
            @endif

            @if ($errors->any())
                <div class="flash error">
                    <strong>Lütfen formu kontrol edin:</strong>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </div>
    </div>
</div>
</body>
</html>