@extends('layouts.guest')

@section('content')
<div class="panel">
    <div style="font-family:var(--font-mono);font-size:11px;color:var(--muted);letter-spacing:3px;text-align:center;margin-bottom:24px;">PASSWORD RESET</div>

    @if($errors->any())
        <div class="alert-error">⚠ {{ $errors->first() }}</div>
    @endif
    @if(session('success'))
        <div class="alert-success">✔ {{ session('success') }}</div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
        @csrf
        <div class="field">
            <label class="field-label" for="email">// REGISTERED EMAIL</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" required placeholder="user@zerotrust.local">
        </div>
        <button type="submit" class="btn-login">▶ SEND RESET LINK</button>
    </form>
    <p style="margin-top:16px;text-align:center;"><a href="{{ route('login') }}">← Back to login</a></p>
</div>
@endsection
