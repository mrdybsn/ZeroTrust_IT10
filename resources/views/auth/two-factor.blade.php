@extends('layouts.guest')

@section('content')
<div class="panel">
    <div style="font-family:var(--font-mono);font-size:11px;color:var(--muted);letter-spacing:3px;text-align:center;margin-bottom:24px;">TWO-FACTOR VERIFICATION</div>

    @if($errors->any())
        <div class="alert-error">⚠ {{ $errors->first() }}</div>
    @endif

    @if($debugCode)
        <div class="alert-success">DEV MODE: Your code is <strong>{{ $debugCode }}</strong></div>
    @endif

    <p style="font-family:var(--font-mono);font-size:12px;color:var(--muted);margin-bottom:20px;">
        Enter the 6-digit verification code sent to your session cache (demo: shown above when APP_DEBUG=true).
    </p>

    <form method="POST" action="{{ route('2fa.verify') }}">
        @csrf
        <div class="field">
            <label class="field-label" for="code">// VERIFICATION CODE</label>
            <input type="text" id="code" name="code" maxlength="6" pattern="[0-9]{6}" required autofocus placeholder="000000">
        </div>
        <button type="submit" class="btn-login">▶ VERIFY</button>
    </form>
</div>
@endsection
