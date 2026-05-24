@extends('layouts.guest')

@section('content')
<div class="panel">
    <div style="font-family:var(--font-mono);font-size:11px;color:var(--muted);letter-spacing:3px;text-align:center;margin-bottom:24px;">PASSWORD RESET VIA EMAIL</div>

    @if($errors->any())
        <div class="alert-error">⚠ {{ $errors->first() }}</div>
    @endif
    @if(session('success'))
        <div class="alert-success">✔ {{ session('success') }}</div>
    @endif

    @if(session('debug_reset_url'))
        <div class="alert-success" style="margin-bottom:16px;">
            <strong>DEV MODE (MAIL_MAILER=log):</strong> Email was written to <code>storage/logs/laravel.log</code>.<br>
            Or use this link directly:<br>
            <a href="{{ session('debug_reset_url') }}" style="word-break:break-all;font-size:11px;">{{ session('debug_reset_url') }}</a>
        </div>
    @endif

    <p style="font-family:var(--font-mono);font-size:12px;color:var(--muted);margin-bottom:20px;line-height:1.6;">
        Enter the email address registered to your account. We will send a secure reset link valid for {{ config('auth.passwords.users.expire', 60) }} minutes.
    </p>

    <form method="POST" action="{{ route('password.email') }}">
        @csrf
        <div class="field">
            <label class="field-label" for="email">// REGISTERED EMAIL</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" required placeholder="admin@zerotrust.local" autocomplete="email">
        </div>
        <button type="submit" class="btn-login">▶ SEND RESET LINK</button>
    </form>

    <p style="margin-top:16px;text-align:center;font-family:var(--font-mono);font-size:11px;">
        <a href="{{ route('login') }}">← Back to login</a>
    </p>
</div>
@endsection
