<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Unit K3 - PT. Semen Tonasa</title>
    <link rel="stylesheet" href="{{ asset('build/assets/app-CP4tGefM.css') }}">
    <script src="{{ asset('build/assets/app-C7JkqvC6.js') }}"></script> 
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css"/>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free/css/all.min.css">
    <style>
        .hero-bg {
            background-image: url('/images/bg-login.jpg');
            background-size: cover;
            background-position: center;
        }

        .glass {
            background: rgba(255, 255, 255, 0.75);
            backdrop-filter: blur(3px);
        }

        @media (max-width: 640px) {
            header img {
                width: 2.5rem;
                height: 2.5rem;
            }

            header h1 {
                font-size: 1rem;
            }

            header p {
                font-size: 0.75rem;
            }
        }
    </style>
</head>

<body class="bg-white text-gray-800 font-sans">

    <!-- Header -->
    <header class="bg-white border-b shadow py-4 sticky top-0 z-50">
        <div class="container mx-auto flex flex-wrap justify-between items-center px-4">
            <div class="flex items-center gap-3">
                <img src="{{ asset('images/logo-k3.png') }}" alt="Logo ST" class="w-12 h-12">
                <img src="{{ asset('images/logo-st.png') }}" alt="Logo SIG" class="w-12 h-12">
                <div class="flex items-center gap-3">
                    <h1 class="text-xl font-bold text-red-700">Unit K3 - PT. Semen Tonasa</h1>
                </div>
            </div>
            <div class="flex gap-2 mt-4 md:mt-0">
                <a href="{{ route('login') }}"
                    class="bg-red-600 hover:bg-red-700 text-white font-semibold px-4 py-2 rounded-lg transition duration-300">
                    Login
                </a>
                <a href="{{ route('register') }}"
                    class="bg-white text-red-600 border border-red-600 hover:bg-red-50 font-semibold px-4 py-2 rounded-lg transition duration-300">
                    Register
                </a>
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="hero-bg relative py-24">
        <div class="absolute inset-0 bg-black bg-opacity-40 z-0"></div>
        <div class="glass container mx-auto px-4 text-center py-12 rounded-lg relative z-10">
            <h2 class="text-2xl md:text-2xl font-bold text-red-800 mb-4 animate-fade-in-up">Selamat Datang di Unit Keselamatan & Kesehatan Kerja</h2>
            <h3 class="text-3xl md:text-3xl font-bold text-red-800 mb-4 animate-fade-in-up delay-100">PT. Semen Tonasa</h3>
        </div>
    </section>
<!-- Informasi / Galeri K3 (Slider Cards) -->
<section class="py-12 bg-white dark:bg-gray-900">
    <div class="container mx-auto px-4">
        <h3 class="text-2xl font-bold text-center text-red-700 dark:text-green-300 mb-8">Informasi Tentang Unit K3</h3>

        <!-- Swiper -->
        <div class="swiper mySwiper">
            <div class="swiper-wrapper">
                @for ($i = 1; $i <= 5; $i++)
                    <div class="swiper-slide bg-white dark:bg-gray-800 border rounded-lg p-4 shadow-md w-72">
                        <img src="{{ asset('images/' . $i . '.jpg') }}" alt="Galeri {{ $i }}" class="rounded mb-3 h-40 w-full object-cover">
                        <h4 class="text-sm font-semibold text-gray-800 dark:text-white mb-1">Aktivitas K3 #{{ $i }}</h4>
                        <p class="text-xs text-gray-600 dark:text-gray-400">Deskripsi singkat kegiatan K3 yang dilakukan di lapangan.</p>
                    </div>
                @endfor
            </div>
        </div>
    </div>
</section>

    <!-- Alur Proses -->
    <section class="py-12 bg-gray-100">
        <div class="container mx-auto px-4">
            <h3 class="text-2xl font-bold text-center text-green-700 mb-10">Alur Pengajuan Izin Kerja</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 text-sm">
                @php
                    $steps = [
                        ['Input OP/SPK/Notifikasi', 'fa-bell'],
                        ['Upload Foto Copy BPJS Ketenagakerjaan', 'fa-id-card'],
                        ['Create Job Safety Analysis (JSA)', 'fa-file-alt'],
                        ['Create Working Permit', 'fa-clipboard-check'],
                        ['Upload Fakta Integritas Kontraktor', 'fa-file-contract'],
                        ['Upload Sertifikasi AK3 Umum', 'fa-certificate'],
                        ['Upload KTP Personil K3', 'fa-id-badge'],
                        ['Upload Surat Kesehatan STMC', 'fa-notes-medical'],
                        ['Upload Struktur Organisasi Berlaku', 'fa-sitemap'],
                        ['Upload Hasil Post Test', 'fa-upload'],
                        ['Terbit Surat Izin Kerja & Upload BST', 'fa-file-signature'],
                        ['Tanda Tangan Digital', 'fa-pen-nib'],
                    ];
                @endphp

                @foreach ($steps as $index => [$step, $icon])
                    <div
                        class="bg-white border rounded-lg p-4 shadow-sm hover:shadow-md transition transform hover:-translate-y-1 hover:scale-105 duration-300 text-center relative animate-fade-in">
                        <div class="absolute top-2 left-2 text-xs font-bold text-green-600 bg-green-100 px-2 py-0.5 rounded-full shadow">
                            {{ $index + 1 }}
                        </div>
                        <div class="text-2xl text-green-600 mb-2">
                            <i class="fas {{ $icon }}"></i>
                        </div>
                        <p class="text-gray-700 font-medium leading-tight">{{ $step }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-green-700 text-white py-6 text-center">
        <p>&copy; {{ date('Y') }} PT. Semen Tonasa - Unit Keselamatan & Kesehatan Kerja</p>
    </footer>

    <!-- Animasi CSS -->
    <style>
        @keyframes fade-in {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-in {
            animation: fade-in 0.6s ease-out forwards;
        }

        .animate-fade-in-up {
            opacity: 0;
            animation: fade-in 1s ease forwards;
        }

        .delay-100 {
            animation-delay: 0.1s;
        }

        .delay-200 {
            animation-delay: 0.2s;
        }
    </style>

<script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js"></script>

<script>
    const swiper = new Swiper('.mySwiper', {
        slidesPerView: 1.2,
        spaceBetween: 15,
        breakpoints: {
            640: { slidesPerView: 2.5 },
            768: { slidesPerView: 3 },
            1024: { slidesPerView: 4 }
        },
        autoplay: {
            delay: 3000,
            disableOnInteraction: false,
        },
        loop: true,
    });
</script>

</body>

</html>
