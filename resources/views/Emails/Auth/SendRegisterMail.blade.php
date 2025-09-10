<x-mail::message>
# Hello {{ $user->name }},

Your OTP for registration is:

<x-mail::panel>
{{ $otp }}
</x-mail::panel>

Please enter this OTP to verify your account.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
