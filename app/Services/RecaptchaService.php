<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class RecaptchaService
{
    public static function siteKey(): string
    {
        return (string) config('recaptcha.site_key');
    }

    public static function isConfigured(): bool
    {
        return self::siteKey() !== '' && (string) config('recaptcha.secret_key') !== '';
    }

    public static function verify(?string $response, ?string $remoteIp = null): bool
    {
        if (! self::isConfigured()) {
            return false;
        }

        if (empty($response)) {
            return false;
        }

        $result = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
            'secret' => config('recaptcha.secret_key'),
            'response' => $response,
            'remoteip' => $remoteIp,
        ]);

        if (! $result->successful()) {
            return false;
        }

        return (bool) ($result->json('success') ?? false);
    }
}
