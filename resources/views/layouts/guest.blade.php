<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0" style="background: linear-gradient(135deg, #fff8fb 0%, #fdeaf3 35%, #f9d7e8 100%);">
            <div>
                <a href="/">
                    <x-application-logo class="w-20 h-20 fill-current text-pink-600" />
                </a>
            </div>

            <div class="w-full sm:max-w-md mt-6 px-6 py-5 bg-white/90 shadow-[0_18px_45px_rgba(181,94,126,0.18)] backdrop-blur-sm overflow-hidden sm:rounded-2xl border border-pink-100">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
