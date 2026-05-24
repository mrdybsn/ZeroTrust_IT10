@extends('layouts.app')

@section('page-title', 'ACTIVITY LOGS')

@section('content')
<div class="card">
    <div class="card-title">◉ ACCOUNTING LOG — AAA FRAMEWORK</div>
    <table class="data-table">
        <thead>
            <tr><th>#</th><th>Timestamp</th><th>User</th><th>Activity</th><th>IP Address</th></tr>
        </thead>
        <tbody>
        @foreach($logs as $log)
            <tr>
                <td class="mono">{{ $log->id }}</td>
                <td class="mono">{{ $log->created_at?->format('Y-m-d H:i:s') }}</td>
                <td>{{ $log->user?->username ?? 'SYSTEM' }}</td>
                <td>{{ $log->activity }}</td>
                <td class="mono">{{ $log->ip_address }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
    <div class="pagination">{{ $logs->links('vendor.pagination.simple-default') }}</div>
</div>
@endsection
