<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'OHS - PT. Semen Tonasa') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <!-- PANGGIL SEKALI DI APP BLADE -->

        <!-- Styles & Vite -->
             @vite(['resources/css/app.css', 'resources/js/app.js']) 
    <!-- <link rel="stylesheet" href="{{ asset('build/assets/app-Cs8kzoEt.css') }}">
    <script src="{{ asset('build/assets/app-B84ErxN3.js') }}"></script>  -->

    </head>
    <body class="font-sans antialiased">
        <!-- Preload bg image -->
        <img src="/images/bg-login.jpg" alt="" loading="lazy" class="hidden">

        <div class="min-h-screen bg-blue-100 dark:bg-gray-900" style="background-image: url('/images/bg-login.jpg');">
            @include('layouts.navigation')

            @isset($header)
                <header class="bg-white dark:bg-gray-800 shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <main>
                {{ $slot }}
            </main>
        </div>

        <!-- Move signature_pad to bottom & defer -->
        <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.6/dist/signature_pad.umd.min.js" defer></script>
    </body>
</html>
