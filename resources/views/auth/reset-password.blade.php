@extends('layouts.guest')

@section('content')
<div class="panel">
    <div style="font-family:var(--font-mono);font-size:11px;color:var(--muted);letter-spacing:3px;text-align:center;margin-bottom:24px;">SET NEW PASSWORD</div>

    @if($errors->any())
        <div class="alert-error">⚠ {{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('password.update') }}">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">
        <input type="hidden" name="email" value="{{ $email }}">
        <div class="field">
            <label class="field-label" for="password">// NEW PASSWORD</label>
            <input type="password" id="password" name="password" required minlength="8">
            <div class="strength-meter"><div class="bar" id="pwStrengthBar"></div></div>
        </div>
        <div class="field">
            <label class="field-label" for="password_confirmation">// CONFIRM</label>
            <input type="password" id="password_confirmation" name="password_confirmation" required>
        </div>
        <button type="submit" class="btn-login">▶ RESET PASSWORD</button>
    </form>
</div>
@endsection
