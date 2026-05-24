<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'ZERO TRUST — Login')</title>
    <link href="https://fonts.googleapis.com/css2?family=Share+Tech+Mono&family=Orbitron:wght@400;700;900&family=Rajdhani:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/zero-trust.css') }}">
</head>
<body>
<div class="layout-auth">
    <div style="width:100%;max-width:420px;">
        <div class="logo-subtitle" style="margin-bottom:8px;">FCU — IT 10 Capstone · Laravel</div>
        <div class="logo-title" style="margin-bottom:24px;">ZERO TRUST</div>
        @yield('content')
        <p style="text-align:center;font-family:var(--font-mono);font-size:9px;color:rgba(0,255,231,0.25);margin-top:16px;">
            SQL INJECTION PROTECTED · ELOQUENT ORM · BCRYPT
        </p>
    </div>
</div>
@stack('scripts')
@yield('scripts')
</body>
</html>
