@extends('layouts.app')

@section('page-title', 'ADMIN DASHBOARD')

@section('content')
<div class="stat-row">
    <div class="stat-card">
        <div class="stat-num">{{ $stats['total_users'] }}</div>
        <div class="stat-label">TOTAL USERS</div>
    </div>
    <div class="stat-card">
        <div class="stat-num">{{ $stats['active_users'] }}</div>
        <div class="stat-label">ACTIVE</div>
    </div>
    <div class="stat-card">
        <div class="stat-num">{{ $stats['inactive_users'] }}</div>
        <div class="stat-label">INACTIVE</div>
    </div>
    <div class="stat-card">
        <div class="stat-num">{{ $stats['admins'] }}</div>
        <div class="stat-label">ADMINS</div>
    </div>
    <div class="stat-card">
        <div class="stat-num">{{ $stats['players'] }}</div>
        <div class="stat-label">PLAYERS</div>
    </div>
    <div class="stat-card">
        <div class="stat-num">{{ $stats['logs_today'] }}</div>
        <div class="stat-label">LOGS TODAY</div>
    </div>
    <div class="stat-card">
        <div class="stat-num">{{ $stats['failed_logins'] }}</div>
        <div class="stat-label">FAILED LOGINS (7D)</div>
    </div>
    <div class="stat-card">
        <div class="stat-num">{{ $stats['total_logs'] }}</div>
        <div class="stat-label">TOTAL LOGS</div>
    </div>
</div>

<div class="dashboard-charts">
    <div class="card">
        <div class="card-title">◉ ACTIVITY — LAST 7 DAYS</div>
        <div class="chart-bar">
            @forelse($activityByDay as $day)
                @php
                    $height = $maxActivity > 0 ? max(8, round(($day->total / $maxActivity) * 100)) : 8;
                @endphp
                <div class="bar-item" style="height: {{ $height }}%;" title="{{ $day->total }} events">
                    <span class="bar-value">{{ $day->total }}</span>
                    <span class="bar-label">{{ \Carbon\Carbon::parse($day->day)->format('m/d') }}</span>
                </div>
            @empty
                <p class="mono chart-empty">No activity in the last 7 days.</p>
            @endforelse
        </div>
    </div>

    <div class="card">
        <div class="card-title">◉ USERS BY ROLE</div>
        <div class="role-chart">
            @foreach($loginsByRole as $row)
                @php
                    $pct = $stats['total_users'] > 0 ? round(($row->total / $stats['total_users']) * 100) : 0;
                @endphp
                <div class="role-row">
                    <span class="role-name badge badge-{{ $row->role }}">{{ strtoupper($row->role) }}</span>
                    <div class="role-bar-track">
                        <div class="role-bar-fill badge-{{ $row->role }}" style="width: {{ $pct }}%;"></div>
                    </div>
                    <span class="role-count mono">{{ $row->total }} ({{ $pct }}%)</span>
                </div>
            @endforeach
        </div>
    </div>
</div>

@if($activityByType->isNotEmpty())
<div class="card">
    <div class="card-title">◉ TOP ACTIVITIES (7 DAYS)</div>
    <table class="data-table">
        <thead><tr><th>Activity</th><th>Count</th></tr></thead>
        <tbody>
        @foreach($activityByType as $item)
            <tr>
                <td>{{ str()->limit($item->activity, 60) }}</td>
                <td class="mono">{{ $item->total }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
@endif

<div class="card">
    <div class="card-title">◉ RECENT ACTIVITY LOGS</div>
    <table class="data-table">
        <thead><tr><th>Time</th><th>User</th><th>Activity</th><th>IP</th></tr></thead>
        <tbody>
        @forelse($recentLogs as $log)
            <tr>
                <td class="mono">{{ $log->created_at?->format('Y-m-d H:i') }}</td>
                <td>{{ $log->user?->username ?? 'SYSTEM' }}</td>
                <td>{{ $log->activity }}</td>
                <td class="mono">{{ $log->ip_address }}</td>
            </tr>
        @empty
            <tr><td colspan="4" style="color:var(--muted);">No activity recorded yet.</td></tr>
        @endforelse
        </tbody>
    </table>
    <p style="margin-top:16px;">
        <a href="{{ route('admin.users.index') }}" class="btn btn-primary">User Management</a>
        <a href="{{ route('admin.logs.index') }}" class="btn btn-primary" style="margin-left:8px;">View All Logs</a>
    </p>
</div>
@endsection
