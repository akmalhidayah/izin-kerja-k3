<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'OHS - PT. Semen Tonasa') }}</title>
        <link rel="icon" type="image/png" href="{{ asset('images/logo-st2.png') }}?v=1">
        <link rel="shortcut icon" href="{{ asset('images/logo-st2.png') }}?v=1">
        <link rel="apple-touch-icon" href="{{ asset('images/logo-st2.png') }}?v=1">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        {{-- Ikon: fa-lite.css (SVG inline), tidak lagi memuat Font Awesome dari CDN --}}

        <!-- Styles & Vite -->
        @vite(['resources/css/app.css', 'resources/css/fa-lite.css', 'resources/js/app.js'])

    </head>
    <body class="font-sans antialiased">
       {{-- Latar polos (gradien statis). Foto + backdrop-blur seluruh layar dihapus karena memberatkan scroll. --}}
       <div class="min-h-screen relative" style="background: linear-gradient(180deg, #f1f5f9 0%, #ecfdf5 100%);">

<!-- Content -->
<div class="relative z-10">

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
</div>

        {{-- SignaturePad sudah ikut di bundel app.js (window.SignaturePad), tidak perlu CDN lagi --}}
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11" defer></script>
        @if (session('token_pdf_url'))
            <script>
                document.addEventListener('DOMContentLoaded', () => {
                    const pdfUrl = @json(session('token_pdf_url'));

                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil disimpan',
                        text: @json(session('token_saved', 'Data berhasil disimpan.')),
                        showCancelButton: true,
                        confirmButtonText: 'Lihat PDF',
                        cancelButtonText: 'Tetap di Halaman',
                        confirmButtonColor: '#16a34a',
                        cancelButtonColor: '#64748b',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.open(pdfUrl, '_blank', 'noopener');
                        }
                    });
                });
            </script>
        @endif
    </body>
</html>
