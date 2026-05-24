<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\TwoFactorService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class TwoFactorController extends Controller
{
    public function show()
    {
        if (! session('pending_2fa_user_id')) {
            return redirect()->route('login');
        }

        return view('auth.two-factor', [
            'debugCode' => session('2fa_code_display'),
        ]);
    }

    public function verify(Request $request, LoginController $loginController)
    {
        $request->validate(['code' => ['required', 'string', 'size:6']]);

        $userId = session('pending_2fa_user_id');
        $user = User::find($userId);

        if (! $user || ! TwoFactorService::verify($user, $request->code)) {
            throw ValidationException::withMessages([
                'code' => 'Invalid verification code.',
            ]);
        }

        session()->forget('2fa_code_display');

        return $loginController->completeLogin($user, $request);
    }
}
