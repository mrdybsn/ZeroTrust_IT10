@extends('layouts.app')

@section('page-title', 'PLAYER HQ')

@section('content')
<div class="card">
    <div class="card-title">◉ WELCOME, {{ strtoupper(auth()->user()->fullname) }}</div>
    <p style="font-family:var(--font-mono);font-size:13px;color:var(--muted);line-height:1.8;">
        You are logged in as <strong style="color:var(--accent);">{{ auth()->user()->username }}</strong> (Player role).<br>
    </p>

    <!-- Game placeholder cards -->
    <div class="stat-row">
        <div class="stat-card">
            <div class="stat-num" style="color:var(--accent3);">—</div>
            <div class="stat-label">MISSIONS CLEARED</div>
        </div>
        <div class="stat-card">
            <div class="stat-num" style="color:var(--accent2);">—</div>
            <div class="stat-label">THREATS DEFEATED</div>
        </div>
        <div class="stat-card">
            <div class="stat-num">—</div>
            <div class="stat-label">CURRENT LEVEL</div>
        </div>
        <div class="stat-card">
            <div class="stat-num" style="color:#0f0;">—</div>
            <div class="stat-label">SCORE</div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card" style="text-align:center;padding:48px 32px;">
    <div style="font-family:var(--font-hud);font-size:15px;color:var(--accent);letter-spacing:5px;margin-bottom:12px;">
        ▶ GAME MODULE
    </div>
    <div style="font-family:var(--font-mono);font-size:12px;color:var(--muted);letter-spacing:2px;line-height:1.8;">
        ZERO TRUST — CYBERSECURITY ROLE PLAYING GAME<br>
        <span style="color:var(--accent2);">[ GAME CONTENT COMING SOON ]</span><br><br>
        This system currently demonstrates the secure<br>
        Login &amp; User Management module for IT 10.
    </div>
</div>
</div>
<div class="card">
    <div class="card-title">◉ YOUR RECENT ACTIVITY</div>
    <table class="data-table">
        <thead><tr><th>Time</th><th>Activity</th><th>IP</th></tr></thead>
        <tbody>
        @forelse($logs as $log)
            <tr>
                <td class="mono">{{ $log->created_at?->format('Y-m-d H:i') }}</td>
                <td>{{ $log->activity }}</td>
                <td class="mono">{{ $log->ip_address }}</td>
            </tr>
        @empty
            <tr><td colspan="3" style="color:var(--muted);">No activity recorded yet.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
@endsection
