<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class TwoFactorService
{
    public static function sendCode(User $user): string
    {
        $code = (string) random_int(100000, 999999);
        Cache::put(self::cacheKey($user->id), $code, now()->addMinutes(10));
        session(['pending_2fa_user_id' => $user->id]);

        return $code;
    }

    public static function verify(User $user, string $code): bool
    {
        $stored = Cache::get(self::cacheKey($user->id));

        if ($stored && hash_equals($stored, $code)) {
            Cache::forget(self::cacheKey($user->id));
            session()->forget('pending_2fa_user_id');

            return true;
        }

        return false;
    }

    public static function cacheKey(int $userId): string
    {
        return "2fa_code_{$userId}";
    }

    public static function generateSecret(): string
    {
        return Str::upper(Str::random(16));
    }
}
