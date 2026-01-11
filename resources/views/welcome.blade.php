<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Unit K3 · PT. Semen Tonasa</title>
  <meta name="description" content="Unit K3 PT. Semen Tonasa — pengajuan izin kerja, JSA, working permit, dokumentasi K3." />

  {{-- Tailwind build --}}
      @vite(['resources/css/app.css', 'resources/js/app.js'])
<!-- 
  <link rel="stylesheet" href="{{ asset('build/assets/app-Dtrtahse.css') }}">
  <script defer src="{{ asset('build/assets/app-B84ErxN3.js') }}"></script> -->

  {{-- Icons --}}
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free/css/all.min.css">

  <style>
    /* ===== GLOBAL FIX ===== */
    html, body {
      overflow-x: hidden;
    }

    /* ===== HEADER FLOATING ===== */
    .header-float {
      position: sticky;
      top: 12px;
      z-index: 60;
    }
    .header-inner {
      backdrop-filter: blur(12px);
      background: rgba(255,255,255,.95);
      border-radius: 18px;
      transition: all .3s ease;
    }
    .header-scrolled .header-inner {
      box-shadow: 0 10px 30px rgba(0,0,0,.12);
      transform: scale(.98);
    }

    /* ===== HERO ===== */
    .hero-wrap {
      position: relative;
      width: 100%;
      height: calc(100vh - 64px);
      min-height: 520px;
      overflow: hidden;
      background: #000;
    }

    .hero-video {
      position: absolute;
      inset: 0;
      width: 100%;
      height: 100%;
      object-fit: cover;
      pointer-events: none;
    }

    .hero-overlay {
      position: absolute;
      inset: 0;
      background: linear-gradient(
        180deg,
        rgba(0,0,0,.25),
        rgba(0,0,0,.55)
      );
      z-index: 20;
    }

    .hero-inner {
      position: relative;
      z-index: 30;
      height: 100%;
      display: flex;
      align-items: center;
    }

    /* ===== ANIMATION ===== */
    .fade-up {
      animation: fadeUp .9s ease forwards;
      opacity: 0;
    }
    .fade-up.delay { animation-delay: .25s; }

    @keyframes fadeUp {
      from { transform: translateY(24px); opacity: 0; }
      to   { transform: translateY(0); opacity: 1; }
    }

    /* ===== BUTTON ===== */
    .btn-main {
      transition: all .25s ease;
    }
    .btn-main:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 20px rgba(0,0,0,.25);
    }

    /* ===== STEP CARD ===== */
    .step-card {
      transition: all .25s ease;
    }
    .step-card:hover {
      transform: translateY(-6px);
      box-shadow: 0 15px 30px rgba(0,0,0,.12);
    }
    .step-card i {
      transition: transform .25s ease;
    }
    .step-card:hover i {
      transform: scale(1.15);
    }
  </style>
</head>

<body class="bg-gray-50 text-gray-900 antialiased">

{{-- ================= HEADER ================= --}}
<header id="header" class="header-float">
  <div class="header-inner max-w-6xl mx-auto px-4 py-3 flex items-center justify-between">
    <div class="flex items-center gap-3">
      <img src="{{ asset('images/logo-k3.png') }}" class="w-11 h-11" alt="Logo K3">
      <img src="{{ asset('images/logo-st.png') }}" class="w-11 h-11" alt="Logo Semen Tonasa">
      <div>
        <h1 class="font-bold text-red-700 leading-tight">
          Unit K3 - PT. Semen Tonasa
        </h1>
        <p class="text-xs text-gray-500">Keselamatan & Kesehatan Kerja</p>
      </div>
    </div>

    <a href="{{ route('login') }}"
       class="px-4 py-2 bg-red-600 text-white rounded-xl btn-main">
      Login
    </a>
  </div>
</header>

{{-- ================= HERO ================= --}}
<section class="hero-wrap">

  <video class="hero-video"
         autoplay
         muted
         loop
         playsinline
         preload="auto">
    <source src="{{ asset('images/videobgk3.mp4') }}" type="video/mp4">
  </video>

  <div class="hero-overlay"></div>

  <div class="hero-inner max-w-6xl mx-auto px-4">
    <div class="text-white max-w-xl">
      <h2 class="text-4xl font-extrabold leading-tight fade-up">
        Selamat Datang di<br>
        Unit Keselamatan &<br>
        Kesehatan Kerja
      </h2>

      <p class="mt-4 text-slate-200 fade-up delay">
        PT. Semen Tonasa — pusat SOP, JSA, dan pengelolaan izin kerja.
        Interaktif, cepat, dan aman.
      </p>

      <div class="mt-6 flex gap-3 fade-up delay">
        <a href="#alur"
           class="px-5 py-2 bg-white text-slate-900 rounded-xl btn-main">
          Lihat Alur
        </a>
        <a href="{{ route('login') }}"
           class="px-5 py-2 border border-white/40 rounded-xl btn-main">
          Masuk
        </a>
      </div>
    </div>
  </div>

</section>

<main>

{{-- ================= POSTER ================= --}}
<section class="py-10">
  <div class="max-w-6xl mx-auto px-4">
    <img src="{{ asset('images/posterk3.jpg') }}"
         class="rounded-2xl shadow-lg w-full"
         alt="Poster K3">
  </div>
</section>

{{-- ================= ALUR ================= --}}
<section id="alur" class="py-12 bg-white">
  <div class="max-w-6xl mx-auto px-4">
    <h3 class="text-2xl font-semibold text-center text-green-700 mb-10">
      Alur Pengajuan Izin Kerja
    </h3>

    @php
      $steps = [
        ['Input OP/SPK/Notifikasi','fa-bell'],
        ['Upload BPJS','fa-id-card'],
        ['Buat JSA','fa-file-alt'],
        ['Working Permit','fa-clipboard-check'],
        ['Dokumen Kontraktor','fa-file-contract'],
        ['Sertifikat AK3','fa-certificate'],
        ['KTP Personil','fa-id-badge'],
        ['Surat Kesehatan','fa-notes-medical'],
        ['Struktur Organisasi','fa-sitemap'],
        ['Post Test','fa-upload'],
        ['Terbit Izin Kerja','fa-file-signature'],
        ['Tanda Tangan Digital','fa-pen-nib'],
      ];
    @endphp

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
      @foreach($steps as [$label,$icon])
        <div class="step-card bg-gray-50 rounded-xl p-5 text-center">
          <div class="text-green-600 text-2xl mb-3">
            <i class="fas {{ $icon }}"></i>
          </div>
          <h4 class="font-semibold text-sm">{{ $label }}</h4>
        </div>
      @endforeach
    </div>
  </div>
</section>

</main>

<footer class="bg-gray-900 text-gray-300 py-6 text-center text-sm">
  &copy; {{ date('Y') }} PT. Semen Tonasa · Unit K3
</footer>

<script>
  window.addEventListener('scroll', () => {
    document
      .getElementById('header')
      .classList.toggle('header-scrolled', window.scrollY > 40);
  });
</script>

</body>
</html>
