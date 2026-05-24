<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\ActivityLogger;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as PasswordRule;
use Illuminate\Validation\ValidationException;

class PasswordResetController extends Controller
{
    public function showForgotForm()
    {
        return view('auth.forgot-password');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user) {
            throw ValidationException::withMessages([
                'email' => 'No account found with that email address.',
            ]);
        }

        if (! $user->hasEmailForReset()) {
            throw ValidationException::withMessages([
                'email' => 'This account has no email on file. Contact an administrator.',
            ]);
        }

        if (! $user->isActive()) {
            throw ValidationException::withMessages([
                'email' => 'This account is deactivated. Contact an administrator.',
            ]);
        }

        $token = Password::broker()->createToken($user);
        $user->sendPasswordResetNotification($token);

        ActivityLogger::log($user->id, "Password reset link requested for {$user->username}");

        $resetUrl = url(route('password.reset', [
            'token' => $token,
            'email' => $user->email,
        ], false));

        $flash = ['success' => 'Password reset link sent! Check your email inbox.'];

        if ($this->shouldShowDebugResetLink()) {
            $flash['debug_reset_url'] = $resetUrl;
        }

        return back()->with($flash);
    }

    public function showResetForm(Request $request, string $token)
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->query('email', $request->email),
        ]);
    }

    public function reset(Request $request)
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', PasswordRule::min(8)],
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => $password,
                    'remember_token' => Str::random(60),
                    'failed_login_attempts' => 0,
                    'locked_until' => null,
                ])->save();

                event(new PasswordReset($user));
                ActivityLogger::log($user->id, "Password reset completed for {$user->username}");
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return redirect()->route('login')->with('success', 'Password has been reset. You may log in with your new password.');
        }

        throw ValidationException::withMessages([
            'email' => __(is_string($status) ? $status : 'Invalid or expired reset token.'),
        ]);
    }

    protected function shouldShowDebugResetLink(): bool
    {
        return config('app.debug') && in_array(config('mail.default'), ['log', 'array'], true);
    }

}
