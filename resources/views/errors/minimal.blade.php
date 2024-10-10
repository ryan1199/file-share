<!DOCTYPE html>
<html data-theme="corporate" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title'){{ ' | '.config('app.name') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://unpkg.com/pattern.css" rel="stylesheet">
</head>
<body class="min-h-screen font-sans antialiased bg-base-200/50 dark:bg-base-200 flex flex-row justify-center items-center pattern-cross-dots-md">
    <x-main full-width>
        <x-slot:content>
            <div class="w-full h-[90vh] flex flex-col space-x-0 space-y-2 items-center justify-start">
                <x-theme-toggle darkTheme="business" lightTheme="corporate" class="hidden" />
                <div class="w-full h-fit self-start justify-self-start text-2xl font-semibold">
                    <a wire:navigate href="{{ route('archive-box.index') }}" class="link link-hover">Home</a>
                </div>
                <div class="w-full h-full max-w-2xl px-4 text-center content-center self-center">
                    <div class="text-7xl font-black">
                        @yield('code')
                    </div>
                    <div class="text-2xl leading-loose tracking-widest font-semibold">
                        @yield('message')
                    </div>
                </div>
            </div>
        </x-slot:content>
    </x-main>
</body>
</html>