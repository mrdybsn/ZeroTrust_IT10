<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\ActivityLogger;
use App\Services\RecaptchaService;
use App\Services\TwoFactorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check()) {
            return $this->redirectByRole(Auth::user());
        }

        if (! RecaptchaService::isConfigured()) {
            return view('auth.login', [
                'recaptchaError' => 'reCAPTCHA is not configured. Add RECAPTCHA_SITE_KEY and RECAPTCHA_SECRET_KEY to your .env file.',
            ]);
        }

        return view('auth.login', [
            'recaptchaSiteKey' => RecaptchaService::siteKey(),
        ]);
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => ['required', 'string', 'max:50'],
            'password' => ['required', 'string', 'max:128'],
            'g-recaptcha-response' => ['required', 'string'],
        ]);

        if (! RecaptchaService::verify($request->input('g-recaptcha-response'), $request->ip())) {
            throw ValidationException::withMessages([
                'g-recaptcha-response' => 'CAPTCHA verification failed. Please complete the challenge and try again.',
            ]);
        }

        $throttleKey = Str::lower($request->input('username')).'|'.$request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);

            throw ValidationException::withMessages([
                'username' => "Too many login attempts. Try again in {$seconds} seconds.",
            ]);
        }

        $user = User::where('username', $request->username)->first();

        if (! $user) {
            RateLimiter::hit($throttleKey, 900);
            ActivityLogger::log(null, "Failed login attempt for username: {$request->username}");

            throw ValidationException::withMessages([
                'username' => 'Invalid username or password.',
            ]);
        }

        if (! $user->isActive()) {
            throw ValidationException::withMessages([
                'username' => 'Account is deactivated. Contact admin.',
            ]);
        }

        if ($user->isLocked()) {
            throw ValidationException::withMessages([
                'username' => 'Account is locked due to too many failed attempts. Try again later.',
            ]);
        }

        if (! Hash::check($request->password, $user->password)) {
            RateLimiter::hit($throttleKey, 900);
            $user->recordFailedLogin();
            ActivityLogger::log(null, "Failed login attempt for username: {$request->username}");

            throw ValidationException::withMessages([
                'username' => 'Invalid username or password.',
            ]);
        }

        RateLimiter::clear($throttleKey);
        $user->clearLoginAttempts();

        if ($user->two_factor_enabled) {
            $code = TwoFactorService::sendCode($user);
            session(['2fa_code_display' => config('app.debug') ? $code : null]);

            return redirect()->route('2fa.show');
        }

        return $this->completeLogin($user, $request);
    }

    public function completeLogin(User $user, Request $request)
    {
        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        ActivityLogger::log($user->id, "User '{$user->username}' logged in successfully.");

        return $this->redirectByRole($user);
    }

    protected function redirectByRole(User $user)
    {
        return redirect()->route($user->isAdmin() ? 'admin.dashboard' : 'player.dashboard');
    }
}
