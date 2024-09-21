<x-mail::message>
# Email Verification

Hello {{ $user->name }}, i am The Admin, i want to tell you that you have to verify your email address.

<x-mail::button :url="$url">
Verify my email address
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
