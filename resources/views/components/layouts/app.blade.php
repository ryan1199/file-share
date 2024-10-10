<!DOCTYPE html>
<html data-theme="light" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ isset($title) ? $title.' | '.config('app.name') : config('app.name') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    {{-- Cropper.js --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.css" />
    {{-- Flatpickr  --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <link href="https://unpkg.com/pattern.css" rel="stylesheet">
</head>
<body class="min-h-screen font-sans antialiased bg-base-200/50 dark:bg-base-200 pattern-cross-dots-md">

    {{-- NAVBAR mobile only --}}
    <x-nav sticky class="lg:hidden">
        <x-slot:brand>
            <x-app-brand />
        </x-slot:brand>
        <x-slot:actions>
            <label for="main-drawer" class="lg:hidden me-3">
                <x-icon name="o-bars-3" class="cursor-pointer" />
            </label>
        </x-slot:actions>
    </x-nav>

    {{-- MAIN --}}
    <x-main full-width>
        {{-- SIDEBAR --}}
        <x-slot:sidebar drawer="main-drawer" collapsible class="bg-base-100">

            {{-- BRAND --}}
            <x-app-brand class="p-5 pt-3" />

            {{-- MENU --}}
            <x-menu activate-by-route>

                {{-- User --}}
                @if($user = auth()->user())
                    {{-- <x-menu-separator /> --}}

                    <x-list-item :item="$user" value="name" sub-value="email" link="{{ route('user.show', $user->slug) }}" no-separator no-hover class="-mx-0 !-my-0 rounded-lg bg-base-300 hover:bg-base-content/10 active:bg-base-200">
                        <x-slot:avatar>
                            <x-avatar :image="asset('storage/avatars/'.$user->avatar)" class="!w-full max-w-12 !rounded-full" />
                        </x-slot:avatar>
                        <x-slot:actions>
                            @livewire('auth.logout')
                        </x-slot:actions>
                    </x-list-item>

                    {{-- <x-menu-separator /> --}}
                @endif

                <x-menu-item title="User List" icon="o-users" link="{{ route('user.index') }}" />
                <x-menu-item title="Archive Box List" icon="o-archive-box" link="{{ route('archive-box.index') }}" />
                @guest
                    <x-menu-item title="Register" icon="o-clipboard-document-list" link="{{ route('auth.register') }}" />
                    <x-menu-item title="Login" icon="o-arrow-left-on-rectangle" link="{{ route('auth.login') }}" />
                    <x-menu-item title="Reset Password" icon="o-lock-closed" link="{{ route('auth.reset-password') }}" />
                    <x-menu-item title="Email Verification" icon="o-check-badge" link="{{ route('auth.email-verification') }}" />
                @endguest
                <x-menu-item title="Theme" icon="o-swatch" @click="$dispatch('mary-toggle-theme')" />
            </x-menu>
            <x-theme-toggle darkTheme="night" lightTheme="light" class="hidden" />
        </x-slot:sidebar>

        {{-- The `$slot` goes here --}}
        <x-slot:content>
            {{ $slot }}
        </x-slot:content>
    </x-main>

    {{--  TOAST area --}}
    <x-toast />
</body>
</html>
