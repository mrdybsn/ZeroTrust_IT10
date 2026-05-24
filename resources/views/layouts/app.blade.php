<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'ZERO TRUST')</title>
    <link href="https://fonts.googleapis.com/css2?family=Share+Tech+Mono&family=Orbitron:wght@400;700;900&family=Rajdhani:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/zero-trust.css') }}">
    @stack('styles')
</head>
<body>
<div class="layout-app">
    <aside class="sidebar">
        <div class="sidebar-logo">
            <div class="game-title">ZERO TRUST</div>
            <div style="font-family:var(--font-mono);font-size:9px;color:var(--muted);letter-spacing:2px;margin-top:4px;">LARAVEL SECURE MODULE</div>
        </div>
        <div class="sidebar-user">
            <strong>{{ auth()->user()->fullname }}</strong>
            {{ '@'.auth()->user()->username }}
            <div style="margin-top:8px;">
                <span class="badge badge-{{ auth()->user()->role }}">{{ strtoupper(auth()->user()->role) }}</span>
            </div>
        </div>
        <nav class="sidebar-nav">
            @if(auth()->user()->isAdmin())
                <a href="{{ route('admin.dashboard') }}" class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">◈ Dashboard</a>
                <a href="{{ route('admin.users.index') }}" class="nav-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">◉ User Management</a>
                <a href="{{ route('admin.logs.index') }}" class="nav-item {{ request()->routeIs('admin.logs.*') ? 'active' : '' }}">◎ Activity Logs</a>
            @else
                <a href="{{ route('player.dashboard') }}" class="nav-item active">◈ Player HQ</a>
            @endif
        </nav>
        <div class="sidebar-footer">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn-logout">⏻ LOGOUT</button>
            </form>
        </div>
    </aside>
    <div class="main">
        <header class="topbar">
            <div class="topbar-title">@yield('page-title', 'DASHBOARD')</div>
            <div style="font-family:var(--font-mono);font-size:10px;color:var(--muted);">ELOQUENT ORM · BCRYPT · CSRF</div>
        </header>
        <main class="content">
            @if(session('success'))
                <div class="flash flash-success">✔ {{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="flash flash-error">⚠ {{ session('error') }}</div>
            @endif
            @yield('content')
        </main>
    </div>
</div>
@stack('scripts')
</body>
</html>
