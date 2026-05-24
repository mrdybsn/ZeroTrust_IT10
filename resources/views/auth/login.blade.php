@extends('layouts.guest')

@section('content')
<div class="panel">
    <div style="font-family:var(--font-mono);font-size:11px;color:var(--muted);letter-spacing:3px;text-align:center;margin-bottom:24px;">ACCESS TERMINAL</div>

    @if(!empty($recaptchaError))
        <div class="alert-error">⚠ {{ $recaptchaError }}</div>
    @endif

    @if($errors->any())
        <div class="alert-error">⚠ {{ $errors->first() }}</div>
    @endif
    @if(session('success'))
        <div class="alert-success">✔ {{ session('success') }}</div>
    @endif

    @if(!empty($recaptchaSiteKey))
    <form method="POST" action="{{ route('login') }}" autocomplete="off" id="loginForm">
        @csrf
        <div class="field">
            <label class="field-label" for="username">// AGENT ID</label>
            <input type="text" id="username" name="username" value="{{ old('username') }}" maxlength="50" required autofocus placeholder="Username">
        </div>
        <div class="field">
            <label class="field-label" for="password">// ACCESS KEY</label>
            <input type="password" id="password" name="password" maxlength="128" required placeholder="Password">
            <div class="strength-meter" id="pwStrength" style="display:none;"><div class="bar" id="pwStrengthBar"></div></div>
        </div>
        <div class="field">
            <label class="field-label">// CAPTCHA</label>
            <div class="g-recaptcha" data-sitekey="{{ $recaptchaSiteKey }}" data-theme="dark"></div>
        </div>
        <button type="submit" class="btn-login">▶ INITIATE ACCESS</button>
    </form>

    <p style="margin-top:16px;text-align:center;font-family:var(--font-mono);font-size:11px;">
        <a href="{{ route('password.request') }}">Forgot password?</a>
    </p>
    @endif
</div>
@endsection

@push('scripts')
@if(!empty($recaptchaSiteKey))
<script src="https://www.google.com/recaptcha/api.js" async defer></script>
@endif
<script>
document.getElementById('password')?.addEventListener('input', function() {
    const bar = document.getElementById('pwStrengthBar');
    const meter = document.getElementById('pwStrength');
    const v = this.value;
    if (!v) { meter.style.display = 'none'; return; }
    meter.style.display = 'block';
    let score = 0;
    if (v.length >= 8) score++;
    if (/[A-Z]/.test(v)) score++;
    if (/[0-9]/.test(v)) score++;
    if (/[^A-Za-z0-9]/.test(v)) score++;
    const widths = ['25%','50%','75%','100%'];
    const colors = ['#ff2d6b','#ffe700','#00ffe7','#0f0'];
    bar.style.width = widths[score - 1] || '10%';
    bar.style.background = colors[score - 1] || '#456070';
});
</script>
@endpush
