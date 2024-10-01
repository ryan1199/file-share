<x-mail::message>
# New Password

Hello {{ $user->name }}, i am The Admin, i want to tell you that we have create a new password for you so this is your password **{{ $password }}**, if you do not like it you can change it later.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>