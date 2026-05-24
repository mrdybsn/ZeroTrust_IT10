@extends('layouts.guest')

@section('content')
<div class="panel">
    <div style="font-family:var(--font-mono);font-size:11px;color:var(--muted);letter-spacing:3px;text-align:center;margin-bottom:24px;">SET NEW PASSWORD</div>

    @if($errors->any())
        <div class="alert-error">⚠ {{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('password.update') }}" id="resetForm">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">
        <input type="hidden" name="email" value="{{ $email }}">
        <div class="field">
            <label class="field-label">// ACCOUNT EMAIL</label>
            <input type="email" value="{{ $email }}" disabled>
        </div>
        <div class="field">
            <label class="field-label" for="password">// NEW PASSWORD</label>
            <input type="password" id="password" name="password" required minlength="8" placeholder="Min. 8 characters">
            <div class="strength-meter"><div class="bar" id="pwStrengthBar"></div></div>
        </div>
        <div class="field">
            <label class="field-label" for="password_confirmation">// CONFIRM PASSWORD</label>
            <input type="password" id="password_confirmation" name="password_confirmation" required placeholder="Re-enter password">
        </div>
        <button type="submit" class="btn-login">▶ RESET PASSWORD</button>
    </form>
    <p style="margin-top:16px;text-align:center;"><a href="{{ route('login') }}">← Back to login</a></p>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('password')?.addEventListener('input', function() {
    const bar = document.getElementById('pwStrengthBar');
    const v = this.value;
    let score = 0;
    if (v.length >= 8) score++;
    if (/[A-Z]/.test(v)) score++;
    if (/[0-9]/.test(v)) score++;
    if (/[^A-Za-z0-9]/.test(v)) score++;
    bar.style.width = ['25%','50%','75%','100%'][score - 1] || '10%';
    bar.style.background = ['#ff2d6b','#ffe700','#00ffe7','#0f0'][score - 1] || '#456070';
});
</script>
@endpush
