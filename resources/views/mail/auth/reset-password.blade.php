<x-mail::message>
# Reset Password Confirmation

Hello {{ $user->name }}, i am The Admin, i want to tell you that you or someone made a request to reset your password, ignore this message if you think you are not made this request.

<x-mail::button :url="$url">
Reset my password
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>