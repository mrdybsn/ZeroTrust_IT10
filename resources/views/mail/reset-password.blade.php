<x-mail::message>
# Password Reset Request

Hello **{{ $user->fullname }}**,

A password reset was requested for your Zero Trust account (**{{ $user->username }}**).

<x-mail::button :url="$url">
Reset Password
</x-mail::button>

This link expires in **{{ $expireMinutes }} minutes**.

If you did not request a reset, ignore this email — your password will stay unchanged.

Thanks,<br>
{{ config('app.name') }}

<x-mail::subcopy>
If the button does not work, copy and paste this URL into your browser:<br>
<span style="word-break: break-all;">{{ $url }}</span>
</x-mail::subcopy>
</x-mail::message>
