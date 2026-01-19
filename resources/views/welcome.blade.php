<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Unit OHS - PT. Semen Tonasa</title>
  <meta name="description" content="Sistem Unit OHS PT. Semen Tonasa untuk pengajuan izin kerja, notifikasi/OP/SPK, JSA, working permit, dan dokumentasi K3." />
  <meta name="robots" content="index,follow" />
  <meta name="author" content="PT. Semen Tonasa" />
  <meta name="theme-color" content="#b91c1c" />
  <link rel="canonical" href="{{ url('/') }}" />
  <meta property="og:title" content="Unit OHS - PT. Semen Tonasa" />
  <meta property="og:description" content="Sistem Unit OHS PT. Semen Tonasa untuk pengajuan izin kerja, notifikasi/OP/SPK, JSA, working permit, dan dokumentasi K3." />
  <meta property="og:type" content="website" />
  <meta property="og:url" content="{{ url('/') }}" />
  <meta property="og:image" content="{{ asset('images/logo-st2.png') }}" />
  <meta property="og:site_name" content="Unit OHS - PT. Semen Tonasa" />
  <meta name="twitter:card" content="summary" />
  <meta name="twitter:title" content="Unit OHS - PT. Semen Tonasa" />
  <meta name="twitter:description" content="Sistem Unit OHS PT. Semen Tonasa untuk pengajuan izin kerja, notifikasi/OP/SPK, JSA, working permit, dan dokumentasi K3." />
  <meta name="twitter:image" content="{{ asset('images/logo-st2.png') }}" />

  {{-- Tailwind build --}}
      <link rel="icon" type="image/png" href="{{ asset('images/logo-st2.png') }}?v=1">
      <link rel="shortcut icon" href="{{ asset('images/logo-st2.png') }}?v=1">
      <link rel="apple-touch-icon" href="{{ asset('images/logo-st2.png') }}?v=1">
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

    /* ===== NEWS CAROUSEL ===== */
    .news-carousel {
      position: relative;
    }
    .news-slide {
      display: none;
      transition: opacity .3s ease;
    }
    .news-slide.active {
      display: block;
    }
    .news-card {
      background: linear-gradient(135deg, #ffffff, #f8fafc);
      border: 1px solid #e5e7eb;
    }
    .news-image {
      width: 100%;
      height: 200px;
      object-fit: cover;
      border-radius: 16px;
      margin-bottom: 16px;
    }
    .news-nav {
      position: absolute;
      top: 50%;
      transform: translateY(-50%);
    }
    .news-dot {
      width: 10px;
      height: 10px;
      border-radius: 999px;
      background: #cbd5f5;
    }
    .news-dot.active {
      background: #ef4444;
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

{{-- ================= BERITA ================= --}}
<section id="berita" class="py-12 bg-slate-50">
  <div class="max-w-6xl mx-auto px-4">
    <div class="flex flex-col gap-2 text-center mb-8">
      <h3 class="text-2xl font-semibold text-slate-900">Berita K3 Terbaru</h3>
      <p class="text-sm text-slate-600">Update kegiatan dan prestasi K3 dari PT. Semen Tonasa.</p>
    </div>

    <div class="news-carousel" data-carousel>
      <div class="news-slide active" data-slide="0">
        <div class="news-card rounded-2xl p-6 shadow-sm">
          <img class="news-image"
               src="https://www.sementonasa.co.id/wp-content/uploads/2025/03/WhatsApp-Image-2025-03-03-at-07.49.16_7c0b11a0.jpg"
               alt="Upacara Bulan K3 tingkat Provinsi Sulsel di Semen Tonasa">
          <div class="text-xs text-red-600 font-semibold mb-2">Semen Tonasa</div>
          <h4 class="text-lg font-bold text-slate-900 mb-2">
            Upacara Bulan K3 Tingkat Provinsi Sulsel Digelar di Semen Tonasa
          </h4>
          <p class="text-sm text-slate-600 mb-4">
            Kegiatan Bulan K3 digelar untuk meningkatkan budaya keselamatan kerja di lingkungan industri.
          </p>
          <a href="https://www.sementonasa.co.id/upacara-bulan-k3-tingkat-provinsi-sulsel-digelar-di-semen-tonasa/"
             target="_blank"
             class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-red-600 text-white text-sm btn-main">
            Baca Selengkapnya
          </a>
        </div>
      </div>

      <div class="news-slide" data-slide="1">
        <div class="news-card rounded-2xl p-6 shadow-sm">
          <img class="news-image"
               src="https://www.sementonasa.co.id/wp-content/uploads/2025/12/WhatsApp-Image-2025-12-31-at-15.12.42.jpeg"
               alt="PT Semen Tonasa raih tujuh penghargaan K3 di Sulawesi Selatan">
          <div class="text-xs text-red-600 font-semibold mb-2">Simpul Rakyat</div>
          <h4 class="text-lg font-bold text-slate-900 mb-2">
            PT Semen Tonasa Raih Tujuh Penghargaan K3 di Sulawesi Selatan
          </h4>
          <p class="text-sm text-slate-600 mb-4">
            Prestasi K3 ini mencerminkan konsistensi penerapan standar keselamatan dan kesehatan kerja.
          </p>
          <a href="https://www.simpulrakyat.co.id/2025/02/pt-semen-tonasa-raih-tujuh-penghargaan-k3-di-sulawesi-selatan.html"
             target="_blank"
             class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-red-600 text-white text-sm btn-main">
            Baca Selengkapnya
          </a>
        </div>
      </div>

      <div class="news-slide" data-slide="2">
        <div class="news-card rounded-2xl p-6 shadow-sm">
          <img class="news-image"
               src="https://www.sementonasa.co.id/wp-content/uploads/2022/11/WhatsApp-Image-2022-11-30-at-12.12.17.jpeg"
               alt="Pelatihan dasar keselamatan dan kesehatan kerja PT Semen Tonasa">
          <div class="text-xs text-red-600 font-semibold mb-2">Semen Tonasa</div>
          <h4 class="text-lg font-bold text-slate-900 mb-2">
            Tingkatkan Sadar Keselamatan, PT Semen Tonasa Gelar Pelatihan Dasar K3
          </h4>
          <p class="text-sm text-slate-600 mb-4">
            Pelatihan ini fokus pada peningkatan pemahaman dasar keselamatan dan kesehatan kerja.
          </p>
          <a href="https://www.sementonasa.co.id/tingkatkan-sadar-keselamatan-pt-semen-tonasa-gelar-pelatihan-dasar-dasar-keselamatan-dan-kesehatan-kerja/"
             target="_blank"
             class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-red-600 text-white text-sm btn-main">
            Baca Selengkapnya
          </a>
        </div>
      </div>

      <button type="button"
              class="news-nav left-2 md:left-4 p-2 rounded-full bg-white shadow hover:bg-slate-100"
              data-prev>
        <i class="fas fa-chevron-left text-slate-700"></i>
      </button>
      <button type="button"
              class="news-nav right-2 md:right-4 p-2 rounded-full bg-white shadow hover:bg-slate-100"
              data-next>
        <i class="fas fa-chevron-right text-slate-700"></i>
      </button>

      <div class="mt-4 flex justify-center gap-2">
        <button type="button" class="news-dot active" data-dot="0" aria-label="Slide 1"></button>
        <button type="button" class="news-dot" data-dot="1" aria-label="Slide 2"></button>
        <button type="button" class="news-dot" data-dot="2" aria-label="Slide 3"></button>
      </div>
    </div>
  </div>
</section>

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

  const carousel = document.querySelector('[data-carousel]');
  if (carousel) {
    const slides = Array.from(carousel.querySelectorAll('[data-slide]'));
    const dots = Array.from(carousel.querySelectorAll('[data-dot]'));
    let index = 0;
    let timer = null;

    const renderSlide = (nextIndex) => {
      slides[index].classList.remove('active');
      dots[index].classList.remove('active');
      index = nextIndex;
      slides[index].classList.add('active');
      dots[index].classList.add('active');
    };

    const nextSlide = () => renderSlide((index + 1) % slides.length);
    const prevSlide = () => renderSlide((index - 1 + slides.length) % slides.length);

    const start = () => {
      clearInterval(timer);
      timer = setInterval(nextSlide, 7000);
    };

    carousel.querySelector('[data-next]').addEventListener('click', () => {
      nextSlide();
      start();
    });
    carousel.querySelector('[data-prev]').addEventListener('click', () => {
      prevSlide();
      start();
    });

    dots.forEach((dot) => {
      dot.addEventListener('click', (event) => {
        const targetIndex = Number(event.currentTarget.dataset.dot);
        if (!Number.isNaN(targetIndex)) {
          renderSlide(targetIndex);
          start();
        }
      });
    });

    start();
  }
</script>

</body>
</html>
