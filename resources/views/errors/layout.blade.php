<!doctype html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>@yield('title', 'Hata') · Bloomberg HT</title>
    <style>
        :root { --black:#101418; --yellow:#FFE400; --red:#bd1b2d; }
        * { box-sizing:border-box; }
        body {
            margin:0; min-height:100vh; display:flex; align-items:center; justify-content:center;
            background:var(--black); color:#fff;
            font-family:Poppins, 'Helvetica Neue', Arial, sans-serif;
            padding:24px;
        }
        .error-box { max-width:520px; text-align:center; }
        .brand { display:inline-block; font-size:22px; font-weight:700; letter-spacing:-.5px; color:#fff; text-decoration:none; margin-bottom:32px; }
        .brand span { background:var(--yellow); color:var(--black); padding:2px 6px; margin-left:2px; }
        .code { font-size:96px; line-height:1; font-weight:900; color:var(--yellow); }
        h1 { font-size:24px; margin:16px 0 8px; }
        p { color:#9aa3ad; margin:0 0 28px; }
        .btn {
            display:inline-block; background:var(--yellow); color:var(--black); font-weight:700;
            padding:12px 28px; border-radius:4px; text-decoration:none;
        }
        .btn:hover { background:#fff; }
        .rule { border:0; border-top:3px solid var(--red); width:64px; margin:24px auto; }
    </style>
</head>
<body>
    <main class="error-box">
        <a href="/" class="brand">Bloomberg<span>HT</span></a>
        <div class="code">@yield('code')</div>
        <hr class="rule">
        <h1>@yield('heading')</h1>
        <p>@yield('message')</p>
        <a class="btn" href="/">Ana Sayfa</a>
    </main>
</body>
</html>